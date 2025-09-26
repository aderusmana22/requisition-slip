<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\paymentProofRequest;
use App\Http\Requests\StoreComplainRequest;
use App\Jobs\sendComplain;
use App\Jobs\sendMailComplain;
use App\Jobs\sendPaymentProofer;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
use App\Models\Requisition\Payment;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\json;

class ComplainController extends Controller
{
    /**
     * Helper method to format datetime to Indonesian timezone
     */
    private function formatToIndonesianTime($datetime, $format = 'd M Y, H:i:s')
    {
        if (!$datetime) {
            return 'Unknown';
        }
        
        return Carbon::parse($datetime)->setTimezone('Asia/Jakarta')->format($format);
    }

    public function index()
    {
        return view('page.complain.index');
    }

    public function destroy($id){
        try{
            DB::transaction(function() use ($id){
                $data = Requisition::where('id', $id)->first();
                if($data){
                    $data->delete();
                }
            });
        }catch(\Exception $e){
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function store(StoreComplainRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $headsQA = User::role('head-qa')->first();

        if (!$user) {
            return response()->json(['message' => 'User belum login.'], 401);
        }
        if (!$user->atasan) {
            return response()->json(['message' => 'Atasan tidak ditemukan. Coba hubungi admin.'], 400);
        }
        if (!$headsQA) {
            return response()->json(['message' => 'head QA tidak ditemukan. Coba hubungi admin.'], 400);
        }

        try{
            $approvalLogs = [];
            
            DB::transaction(function () use ($validated, $user, &$approvalLogs, $headsQA) {
        
            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'customer_id' => $validated['customer_id'],
                'no_srs' => str_replace(' ', '', $validated['rs_number']),
                'account' => $validated['account'],
                'cost_center' => $validated['cost_center'],
                'request_date' => $validated['date'],
                'category' => 'Complain',
                'status' => 'Pending',
                'objectives' => $validated['objectives'] ?? null,
                'route_to' => $headsQA->name,
            ]);

            // Insert approval log untuk head QA (level 1)
            $headsQAApprovalLog = ApprovalLog::create([
                'requisition_id' => $requisition->id,
                'approver_nik' => $headsQA->nik,
                'status' => 'Pending',
                'level' => 1,
                'token' => bin2hex(random_bytes(16)),
                'notes' => null,
            ]);
            
            // Simpan approval log untuk job dispatch nanti
            $approvalLogs[] = [
                'approval_log' => $headsQAApprovalLog,
                'approver' => $headsQA,
                'requisition' => $requisition
            ];

            // Insert approval log untuk atasan (level 2)
            $atasanApprovalLog = ApprovalLog::create([
                'requisition_id' => $requisition->id,
                'approver_nik' => $user->atasan->nik,
                'status' => 'Pending',
                'level' => 2,
                'token' => bin2hex(random_bytes(16)),
                'notes' => null,
            ]);
            
            // Simpan approval log untuk job dispatch nanti
            $approvalLogs[] = [
                'approval_log' => $atasanApprovalLog,
                'approver' => $user->atasan,
                'requisition' => $requisition
            ];

            // Ambil approval path untuk kategori Complain
            $approvalPath = ApprovalPath::where('category', 'Complain')->first();
            
            if ($approvalPath && !empty($approvalPath->sequence_approvers)) {
                $approvers = $approvalPath->sequence_approvers;
                
                for ($i = 0; $i < count($approvers); $i++) {
                    $approverNik = $approvers[$i];
                    $level = $i + 3;
                    
                    $approver = User::where('nik', $approverNik)->first();
                    if ($approver) {
                        $approverApprovalLog = ApprovalLog::create([
                            'requisition_id' => $requisition->id,
                            'approver_nik' => $approverNik,
                            'status' => 'Pending',
                            'level' => $level,
                            'token' => bin2hex(random_bytes(16)),
                            'notes' => null,
                        ]);
                        
                        // Simpan approval log untuk job dispatch nanti
                        $approvalLogs[] = [
                            'approval_log' => $approverApprovalLog,
                            'approver' => $approver,
                            'requisition' => $requisition
                        ];
                    }else{
                        throw new \Exception("Approver dengan NIK $approverNik tidak ditemukan.");
                    }
                }
            }else{
                throw new \Exception('Approval path untuk kategori Complain tidak ditemukan.');
            }

            $requisitionitems = [];
            $now = Carbon::now();
    
            foreach ($validated['items'] as $itemMasterId => $masterData) {
                foreach ($masterData['details'] as $itemDetailId => $detailData) {
                    $requisitionitems[] = [
                        'requisition_id'    => $requisition->id,
                        'item_master_id'    => $itemMasterId,
                        'item_detail_id'    => $itemDetailId,
                        'quantity_required' => $detailData['qty_required'] ?? 0,
                        'quantity_issued'   => $detailData['qty_issued'] ?? 0,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
            }

            if (!empty($requisitionitems)) {
                RequisitionItem::insert($requisitionitems);
            } else {
                throw new \Exception('Tidak ada item yang valid untuk disimpan.');
            }

            $casuer = User::where('nik', $user->nik)->first();

            activity()
                ->causedBy($casuer)
                ->performedOn($requisition, $requisitionitems)
                ->event('created form requisition complain')
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                ->log('user ' . $casuer->name . ' Membuat Requisition Complain dengan ID: ' . $requisition->id);

            });

            $firstApprover = $approvalLogs[0]; // Ini sekarang headsQA (level 1)
            sendMailComplain::dispatch(
                $firstApprover['approver'],
                $firstApprover['requisition'],
                $firstApprover['approval_log']
            );

            return response()->json(['message' => 'Form Requisition complain berhasil dibuat.'], 201);
        }catch(\Exception $e){
            Log::error('Gagal menyimpan requisition: ' . $e->getMessage());
            
            $errorMessage = $e->getMessage();
            
            $statusCode = 500;
            if (str_contains($errorMessage, 'tidak ditemukan') || 
                str_contains($errorMessage, 'not found') || 
                str_contains($errorMessage, 'kosong')) {
                $statusCode = 400;
            }
            
            return response()->json(['message' => $errorMessage], $statusCode);
        }
    }

    public function getData(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        // Dapatkan nama kolom untuk sorting dari request berdasarkan indexnya
        $orderColumnName = $request->input("columns.{$orderColumnIndex}.name");

        // Hitung total data tanpa filter apa pun
        $totalData = Requisition::count();

        // Mulai query builder
        $query = Requisition::query();

        // 2. Terapkan filter pencarian jika ada input dari kotak search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('requester', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%");
                })
                ->orWhereHas('customer', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%");
                })
                ->orWhere('cost_center', 'like', "%{$searchValue}%")
                ->orWhere('category', 'like', "%{$searchValue}%")
                ->orWhere('route_to', 'like', "%{$searchValue}%")
                ->orWhere('status', 'like', "%{$searchValue}%");
            });
        }

        $totalFiltered = $query->count();

        if (!empty($orderColumnName)) {
            $query->orderBy($orderColumnName, $orderDirection);
        }

        $data = $query->with(['customer', 'revision', 'requester', 'approvalLogs'])
            ->where('category', 'Complain')
            ->offset($start)
            ->limit($length)
            ->get();

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];

        return response()->json($response);
    }

    public function getCustomerList()
    {
        $customers = Customer::select('id', 'name', 'address')->distinct()->get();
        return response()->json($customers);
    }

    public function getSerial()
    {
        $prefix = 'S';
        $now = Carbon::now();

        $yearMonthPart = $now->format('y m');

        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        $lastRecordThisYear = Requisition::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->latest('id')
            ->first();

        $sequence = 1;

        if ($lastRecordThisYear) {
            $lastSeriesNumber = $lastRecordThisYear->no_srs;

            $lastSequence = (int) substr($lastSeriesNumber, -4);

            $sequence = $lastSequence + 1;
        }

        $sequencePadded = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $seriesNumber = sprintf('%s %s %s', $prefix, $yearMonthPart, $sequencePadded);

        $accountNumber = 4914;

        return response()->json(['series_number' => $seriesNumber, 'account_number' => $accountNumber]);
    }

    public function getProductList(Request $request)
    {
        $items = ItemMaster::with('ItemDetails')->get();
        return response()->json(['items' => $items]);
    }

    public function getFormDetail($id){
        try {
            $complain = Requisition::with([
                'customer', 
                'requester',
                'requisitionItems.itemMaster.ItemDetails', 
                'approvalLogs', 
                'approvalLogs.approver',
                'payments'
            ])->findOrFail($id);

            return response()->json($complain);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Complain data not found.'], 404);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred on the server.'], 500);
        }
    }

    /**
     * Method untuk handle approval process (akan dipanggil dari approval controller)
     */
    public function processApproval(Request $request)
    {
        // Check if token is already used/expired first
        $tokenStatus = $this->checkTokenStatus($request);
        if ($tokenStatus !== null) {
            return $tokenStatus;
        }

        if ($request->isMethod('post')) {
            return $this->processApprovalWithValidation($request);
        } else {
            return $this->processDirectApproval($request);
        }
    }

    /**
     * Check if token is already used or expired
     */
    private function checkTokenStatus(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        $id = $request->query('id') ?? $request->input('id');

        // Jika ID atau token tidak ada dalam request
        if (!$token || !$id) {
            return view('page.complain.approval-invalid', [
                'message' => 'The approval link is missing required parameters.',
                'errorType' => 'missing_params'
            ]);
        }

        // Cek apakah requisition ID ada terlebih dahulu
        $requisitionExists = ApprovalLog::where('requisition_id', $id)->exists();
        
        // Jika requisition ID tidak ditemukan sama sekali
        if (!$requisitionExists) {
            return view('page.complain.approval-invalid', [
                'message' => 'Invalid approval link - requisition not found.',
                'errorType' => 'invalid_link'
            ]);
        }

        // Jika requisition ID ada, cek apakah token masih valid
        $approvalLog = ApprovalLog::where('requisition_id', $id)
            ->where('token', $token)
            ->first();

        // Jika token tidak ditemukan (sudah null/digunakan), berarti link expired
        if (!$approvalLog) {
            // Cari approval log berdasarkan requisition_id saja untuk mendapatkan updated_at terakhir
            $lastApprovalLog = ApprovalLog::where('requisition_id', $id)
                ->orderBy('updated_at', 'desc')
                ->first();

            $lastActionDate = $lastApprovalLog ? $this->formatToIndonesianTime($lastApprovalLog->updated_at) : 'Unknown';

            return view('page.complain.approval-expired', [
                'message' => 'This approval link has already been used and is no longer valid.',
                'errorType' => 'token_expired',
                'lastActionDate' => $lastActionDate,
                'requisition' => Requisition::with('customer')->find($id),
                'approvalLog' => $lastApprovalLog
            ]);
        }

        // Jika approval log ditemukan dan masih pending, lanjut ke proses normal
        if ($approvalLog->status === 'Pending') {
            return null;
        }

        // Jika approval log ditemukan tapi sudah diproses (bukan Pending)
        $requisition = Requisition::with('customer')->find($id);
        
        return view('page.complain.approval-expired', compact('requisition', 'approvalLog'));
    }

    /**
     * Process approval with validation (from review form)
     */
    private function processApprovalWithValidation(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'id' => 'required|integer',
            'status' => 'required|in:approve,reject',
            'notes' => $request->input('status') === 'reject' ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ], [
            'notes.required' => 'Notes/reason is required for rejection.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ]);

        try {
            $token = $request->input('token');
            $id = $request->input('id');
            $status = $request->input('status');
            $notes = $request->input('notes');

            $requisition = null;

            DB::transaction(function () use ($token, $id, $status, $notes, &$requisition) {
                $approvalLog = ApprovalLog::where('requisition_id', $id)
                    ->where('token', $token)
                    ->where('status', 'Pending')
                    ->first();

                if (!$approvalLog) {
                    throw new \Exception('Invalid or expired approval link.');
                }

                // Update status approval log 
                $approvalLog->status = ($status === 'approve') ? 'Approved' : 'Rejected';
                $approvalLog->notes = $notes;
                $approvalLog->token = null;
                $approvalLog->save();

                $requisition = Requisition::with('customer')->find($approvalLog->requisition_id);

                // Jika diapprove, cek apakah ada level berikutnya
                if ($status === 'approve') {
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }

                    // simpan perubahan status requisition karna diapprove
                    $requisition->status = 'In Progress';
                    $requisition->save();
                    $this->mailOtherLevel($approvalLog->requisition_id, $approvalLog->level);
                } else {
                
                    // Jika direject, langsung set status requisition ke Rejected
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }
                
                    $requisition->status = 'Rejected';
                    $requisition->save();

                    if ($approvalLog->level === 1) {
                        $requisition->status = 'payment proof';
                        $requisition->save();
                        sendPaymentProofer::dispatch($requisition, null, 'rejection_warning');
                    }
                }

                activity()
                    ->causedBy(User::where('nik', $approvalLog->approver_nik)->first())
                    ->performedOn($approvalLog)
                    ->event('processed approval')
                    ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                    ->log('User ' . ($approvalLog->approver_nik ?? 'Unknown') . ' has ' . $approvalLog->status . ' requisition ID: ' . $approvalLog->requisition_id);
            });

            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your decision has been recorded successfully.',
                    'status' => $status,
                    'requisition_id' => $requisition->id ?? null
                ]);
            }

            // Untuk non-AJAX approval with validation, tampilkan halaman hasil
            return view('page.complain.approval-result', compact('requisition', 'status'));

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            // Check if it's an AJAX request for error handling
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => 'approval_failed'
                ], 400);
            }
            
            // Untuk non-AJAX error, redirect ke halaman error dengan pesan
            if (str_contains($errorMessage, 'Invalid approval link') || str_contains($errorMessage, 'expired')) {
                return view('page.complain.approval-invalid', [
                    'message' => $errorMessage,
                    'errorType' => 'token_expired'
                ]);
            } elseif (str_contains($errorMessage, 'not found')) {
                return view('page.complain.approval-invalid', [
                    'message' => $errorMessage,
                    'errorType' => 'not_found'
                ]);
            }
            
            return view('page.complain.approval-invalid', [
                'message' => 'Terjadi kesalahan saat memproses approval.',
                'errorType' => 'server_error'
            ]);
        }
    }

    /**
     * Process direct approval (from email links)
     */
    private function processDirectApproval(Request $request)
    {
        try {
            $token = $request->query('token');
            $id = $request->query('id');
            $status = $request->query('status');

            if (!$token || !$id || !in_array($status, ['approve', 'reject'])) {
                return view('page.complain.approval-invalid', [
                    'message' => 'Invalid approval link - missing parameters.',
                    'errorType' => 'missing_params'
                ]);
            }

            $requisition = null;

            DB::transaction(function () use ($token, $id, $status, &$requisition) {
                $approvalLog = ApprovalLog::where('requisition_id', $id)
                    ->where('token', $token)
                    ->where('status', 'Pending')
                    ->first();

                if (!$approvalLog) {
                    throw new \Exception('Invalid or expired approval link.');
                }

                // Update status approval log 
                $approvalLog->status = ($status === 'approve') ? 'Approved' : 'Rejected';
                $approvalLog->notes = null;
                $approvalLog->token = null;
                $approvalLog->save();

                $requisition = Requisition::with('customer')->find($approvalLog->requisition_id);

                // Jika diapprove, cek apakah ada level berikutnya
                if ($status === 'approve') {
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }

                    // simpan perubahan status requisition karna diapprove
                    $requisition->status = 'In Progress';
                    $requisition->save();
                    $this->mailOtherLevel($approvalLog->requisition_id, $approvalLog->level);
                } else {
                
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }
                
                    // Jika direject, langsung set status requisition ke Rejected
                    $requisition->status = 'Rejected';
                    $requisition->save();

                    if ($approvalLog->level === 1) {
                        $requisition->status = 'payment proof';
                        $requisition->save();
                        sendPaymentProofer::dispatch($requisition, null, 'rejection_warning');
                    }
                }

                activity()
                    ->causedBy(User::where('nik', $approvalLog->approver_nik)->first())
                    ->performedOn($approvalLog)
                    ->event('processed approval')
                    ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                    ->log('User ' . ($approvalLog->approver_nik ?? 'Unknown') . ' has ' . $approvalLog->status . ' requisition ID: ' . $approvalLog->requisition_id);
            });

            // tampilkan halaman hasil approval
            return view('page.complain.approval-result', compact('requisition', 'status'));

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            // Untuk semua error, tampilkan halaman error yang sesuai
            if (str_contains($errorMessage, 'Invalid approval link') || str_contains($errorMessage, 'expired')) {
                return view('page.complain.approval-invalid', [
                    'message' => $errorMessage,
                    'errorType' => 'token_expired'
                ]);
            } elseif (str_contains($errorMessage, 'not found')) {
                return view('page.complain.approval-invalid', [
                    'message' => $errorMessage,
                    'errorType' => 'not_found'
                ]);
            }
            
            return view('page.complain.approval-invalid', [
                'message' => 'Terjadi kesalahan saat memproses approval.',
                'errorType' => 'server_error'
            ]);
        }
    }

    /**
     * Show review page for approval with review
     */
    public function showReviewPage(Request $request)
    {
        try {
            $token = $request->query('token');
            $id = $request->query('id');

            if (!$token || !$id) {
                return view('page.complain.approval-invalid', [
                    'message' => 'Invalid approval link - missing parameters.',
                    'errorType' => 'missing_params'
                ]);
            }

            // Check if token is already used/expired first
            $tokenStatus = $this->checkTokenStatus($request);
            if ($tokenStatus !== null) {
                return $tokenStatus;
            }

            // Verify approval log exists and is valid
            $approvalLog = ApprovalLog::where('requisition_id', $id)
                ->where('token', $token)
                ->where('status', 'Pending')
                ->first();

            if (!$approvalLog) {
                return view('page.complain.approval-invalid', [
                    'message' => 'Invalid or expired approval link.',
                    'errorType' => 'token_expired'
                ]);
            }

            // Get requisition with related data
            $requisition = Requisition::with(['customer', 'requisitionItems.itemMaster.ItemDetails'])
                ->find($id);

            if (!$requisition) {
                return view('page.complain.approval-invalid', [
                    'message' => 'Requisition not found.',
                    'errorType' => 'not_found'
                ]);
            }

            return view('page.complain.review', compact('requisition', 'token'));
        } catch (\Exception $e) {
            Log::error('Error showing review page: ' . $e->getMessage());
            return view('page.complain.approval-invalid', [
                'message' => 'An error occurred while loading the review page.',
                'errorType' => 'server_error'
            ]);
        }
    }

    /**
     * Kirim email ke approver level berikutnya setelah current level approve
     */
    public function mailOtherLevel($requisitionId, $currentLevel)
    {
        try {
            // Cari approval log level berikutnya yang masih pending
            $nextApprovalLog = ApprovalLog::where('requisition_id', $requisitionId)
                ->where('level', $currentLevel + 1)
                ->where('status', 'Pending')
                ->whereNotNull('token') // Token masih ada = belum approve
                ->first();

            if ($nextApprovalLog) {
                // Cari data approver
                $approver = User::where('nik', $nextApprovalLog->approver_nik)->first();
                if ($approver) {
                    // Cari data requisition
                    $requisition = Requisition::find($requisitionId);
                    if ($requisition) {

                        $requisition->route_to = $approver->name;;
                        $requisition->save();
                        
                        // Kirim email ke approver level berikutnya
                        sendMailComplain::dispatch($approver, $requisition, $nextApprovalLog);

                        Log::info("Email approval dikirim ke level {$nextApprovalLog->level} - {$approver->name}");
                        return true;
                    }
                }
            }else{
                // Jika tidak ada next level, berarti semua level sudah approve
                $requisition = Requisition::find($requisitionId);
                if ($requisition) {
                    $requisition->status = 'Approved';
                    $requisition->save();
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error sending next level notification: ' . $e->getMessage());
            return false;
        }
    }

    public function testData()
    {
        $headsQA = User::role('head-qa')->get();
        return response()->json($headsQA);
    }

    public function uploadPaymentProof(paymentProofRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function() use ($validated) {
                // Check if requisition exists and has correct status
                $requisition = Requisition::findOrFail($validated['complain_id']);

                if ($requisition->status !== 'payment proof') {
                    throw new \Exception('This requisition does not require payment proof upload.');
                }

                // Handle file upload
                $file = $validated['payment_document'];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('payment_proofs', $fileName, 'public');

                // Create payment record
                $payment = Payment::create([
                    'requisition_id' => $validated['complain_id'],
                    'payment_date' => $validated['payment_date'],
                    'document_url' => $filePath,
                ]);

                // Update requisition status
                $requisition->status = 'In Progress';
                $requisition->save();

                // Log activity
                $user = Auth::user();
                if ($user) {
                    activity()
                        ->causedBy(User::find($user->id))
                        ->performedOn($requisition)
                        ->event('uploaded payment proof')
                        ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                        ->log('User ' . $user->name . ' uploaded payment proof for requisition ID: ' . $requisition->id);
                }

                // Send payment confirmation email with attachment
                $this->mailOtherLevel($validated['complain_id'], 1);
            });

            return response()->json([
                'message' => 'Payment proof uploaded successfully. Requisition status updated to Completed.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to upload payment proof: ' . $e->getMessage());
            
            $errorMessage = $e->getMessage();
            $statusCode = 500;
            
            if (str_contains($errorMessage, 'does not require payment proof') || 
                str_contains($errorMessage, 'Invalid complain ID')) {
                $statusCode = 400;
            }
            
            return response()->json(['message' => $errorMessage], $statusCode);
        }
    }
}
