<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplainRequest;
use App\Jobs\sendMailComplain;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
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
    
        if (!$user) {
            return response()->json(['message' => 'User belum login.'], 401);
        }
        if (!$user->atasan) {
            return response()->json(['message' => 'Atasan tidak ditemukan. Coba hubungi admin.'], 400);
        }

        try{
            $approvalLogs = [];
            
            DB::transaction(function () use ($validated, $user, &$approvalLogs) {
        
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
                'route_to' => $user->atasan->name,
            ]);

            // Insert approval log untuk atasan (level 1)
            $atasanApprovalLog = ApprovalLog::create([
                'requisition_id' => $requisition->id,
                'approver_nik' => $user->atasan->nik,
                'status' => 'Pending',
                'level' => 1,
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
                    $level = $i + 2;
                    
                    // Cek apakah user dengan NIK tersebut ada
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

            $firstApprover = $approvalLogs[0];
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
                $q->where('requester_nik', 'like', "%{$searchValue}%")
                    ->orWhere('customer_id', 'like', "%{$searchValue}%")
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

        $data = $query->with(['customer', 'revision', 'requester'])
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
            // Eager load relasi yang dibutuhkan: customer dan items beserta detail dari item
            // 'items' adalah nama relasi pivot, 'items.detail' mengambil detail produk dari pivot
            $complain = Requisition::with(['customer', 'requisitionItems.itemMaster.ItemDetails'])->findOrFail($id);

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
        try {
            DB::transaction(function () use ($request) {
                $token = $request->query('token');
                $id = $request->query('id');
                $status = $request->query('status');

                if (!$token || !$id || !in_array($status, ['approve', 'reject'])) {
                    throw new \Exception('Invalid approval link.');
                }

                $approvalLog = ApprovalLog::where('requisition_id', $id)
                    ->where('token', $token)
                    ->where('status', 'Pending')
                    ->first();

                if (!$approvalLog) {
                    throw new \Exception('Invalid or expired approval link.');
                }

                // Update status approval log
                $approvalLog->status = ($status === 'approve') ? 'Approved' : 'Rejected';
                $approvalLog->notes = $request->input('notes', null);
                $approvalLog->token = null;
                $approvalLog->save();

                $requisition = Requisition::find($approvalLog->requisition_id);

                // Jika diapprove, cek apakah ada level berikutnya
                if ($status === 'approve') {
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }

                    $requisition->status = 'In Progress';
                    $requisition->save();
                    $this->mailOtherLevel($approvalLog->requisition_id, $approvalLog->level);
                } else {
                
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }
                
                    $requisition->status = 'Rejected';
                    $requisition->save();
                }

                activity()
                    ->causedBy(User::where('nik', $approvalLog->approver_nik)->first())
                    ->performedOn($approvalLog)
                    ->event('processed approval')
                    ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                    ->log('User ' . ($approvalLog->approver_nik ?? 'Unknown') . ' has ' . $approvalLog->status . ' requisition ID: ' . $approvalLog->requisition_id);
            });

            return response()->json(['message' => 'Approval berhasil diproses.'], 200);
        } catch (\Exception $e) {
        
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'Invalid approval link') || str_contains($errorMessage, 'expired')) {
                return response()->json(['message' => $errorMessage], 400);
            } elseif (str_contains($errorMessage, 'not found')) {
                return response()->json(['message' => $errorMessage], 404);
            }
            return response()->json(['message' => 'Terjadi kesalahan saat memproses approval.'], 500);
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
}
