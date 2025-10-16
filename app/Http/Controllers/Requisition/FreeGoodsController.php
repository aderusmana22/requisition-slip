<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFreeGoodsRequest;
use App\Http\Requests\UpdateFreeGoodsRequest;
use App\Jobs\sendFreeGoods; 
use App\Mail\MailRejectFreeGoods; 
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem; // <== PASTIKAN INI ADA
use App\Models\Requisition\Tracking;
use App\Models\User;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath; // <== KRITIS: BARIS INI DITAMBAHKAN
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; 
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ApprovalTrait; 
use Illuminate\Support\Str;

class FreeGoodsController extends Controller
{
    use ApprovalTrait;

    //======================================================================
    // PUBLIC FUNCTIONS (Controller Endpoints & AJAX Handlers)
    //======================================================================

    private function generateFgNumber()
    {
        // Format: FG YY MM XXX
        $prefix = 'FG';
        $year = date('y');
        $month = date('m');
        $currentPrefix = "$prefix $year $month";

        $lastRequisition = Requisition::where('category', 'FREE GOODS')
                                    ->where('no_srs', 'LIKE', $currentPrefix . ' %')
                                    ->orderBy('no_srs', 'desc')
                                    ->first();

        $runningNumber = 1;
        if ($lastRequisition) {
            $lastParts = explode(' ', $lastRequisition->no_srs);
            $lastRunningNumber = end($lastParts);
            $runningNumber = intval($lastRunningNumber) + 1;
        }

        return $currentPrefix . ' ' . sprintf('%03d', $runningNumber);
    }

    public function getAllItemMasters()
    {
        $masters = ItemMaster::select('id', 'item_master_code', 'item_master_name', 'unit')->get();
        return response()->json($masters);
    }

    public function index()
    {
        $customers = Customer::all();
        $generatedFg = $this->generateFgNumber();
        $user = Auth::user();
        $userDepartmentName = $user->department?->name ?? null;

        return view('page.freegoods.index', compact(
            'customers', 'generatedFg', 'userDepartmentName'));
    }

    public function getData()
    {
        $user = Auth::user();

        $query = DB::table('requisitions')
            ->leftJoin('users', 'requisitions.requester_nik', '=', 'users.nik')
            ->leftJoin('customers', 'requisitions.customer_id', '=', 'customers.id')
            ->where('requisitions.category', 'FREE GOODS')
            ->select(
                'requisitions.id',
                'requisitions.requester_nik',
                'requisitions.request_date',
                'requisitions.cost_center', 
                'requisitions.sub_category',
                'requisitions.route_to',
                'requisitions.status',
                'users.name as requester_name',
                'users.avatar',
                'customers.name as customer_name'
            );

        if (!$user->hasRole('super-admin')) {
             $query->where('requisitions.requester_nik', $user->nik);
        }

        $query->orderBy('requisitions.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('requester_info', function ($requisition) {
                $avatar = $requisition->avatar ? asset($requisition->avatar) : asset('assets/images/logo/sinarmeadow.png');
                $nik = e($requisition->requester_nik);

                return '
                    <div class="d-flex align-items-center">
                        <div class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-dark me-2">
                            <img src="' . $avatar . '" alt="avatar" class="img-fluid">
                        </div>
                        <div>
                            <small class="text-muted">' . $nik . '</small>
                        </div>
                    </div>
                ';
            })
            ->editColumn('request_date', fn($req) => Carbon::parse($req->request_date)->format('d M Y'))
            ->editColumn('cost_center', fn($req) => e($req->cost_center) ?? '-') 
            ->editColumn('sub_category', function ($requisition) {
                $subCategory = $requisition->sub_category;
                $badgeClass = 'bg-primary';
                return '<span class="badge ' . $badgeClass . '">' . e($subCategory) . '</span>';
            })
            ->editColumn('route_to', fn($req) => '<span class="badge bg-warning text-dark"><i class="ph-bold ph-user-switch me-1"></i>' . e($req->route_to) . '</span>')
            ->editColumn('status', function ($requisition) {
                $status = $requisition->status;
                $badgeClass = 'bg-primary text-white';
                if (in_array($status, ['Submitted', 'Pending'])) $badgeClass = 'bg-primary';
                elseif (in_array($status, ['Approved', 'Completed'])) $badgeClass = 'bg-success';
                elseif (in_array($status, ['Rejected', 'Cancelled'])) $badgeClass = 'bg-danger';
                elseif ($status == 'In Progress') $badgeClass = 'bg-info';
                return '<span class="badge ' . $badgeClass . '">' . e($status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $user = Auth::user();

                $viewBtn = '<button type="button" class="btn btn-sm btn-info btn-view-requisition" data-id="' . $row->id . '" title="Show Detail"><i class="fa-solid fa-eye text-white"></i></button>';
                $editBtn = '';
                $deleteBtn = '';

                if ($row->status === 'Pending') {
                    $editBtn = '<button type="button" class="btn btn-sm btn-warning btn-edit-requisition" data-id="' . $row->id . '" title="Edit"><i class="fa-solid fa-pencil text-white"></i></button>';
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete-requisition" data-id="' . $row->id . '" title="Delete"><i class="fa-solid fa-trash-alt text-white"></i></button>';
                }

                return "<div class='d-flex gap-1'>{$viewBtn} {$editBtn} {$deleteBtn}</div>";
            })
            ->rawColumns(['requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }

    
    public function store(StoreFreeGoodsRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            
            // KRITIS: Pastikan 'department' di-load untuk logika conditional
            $user = User::with('atasan', 'department')->find(Auth::id());
            $userAccount = $user->department->code ?? null;
            

           
            if ($userAccount === '5300') {
                $pathSubCategory = 'SNM_PATH'; 
                $subCategoryLabel = 'SnM Request'; 
            } else {
                $pathSubCategory = 'NON_SNM_PATH'; 
                $subCategoryLabel = 'General Request'; 
            }
            
            
            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'customer_id' => $validated['customer_id'],
                'no_srs' => $this->generateFgNumber(),
                'account' => $validated['account'],
                'cost_center' => $validated['cost_center'] ?? null,
                'request_date' => $validated['request_date'],
                'category' => 'FREE GOODS',
                'sub_category' => $subCategoryLabel, 
                'objectives' => $validated['objectives'],
                'estimated_potential' => $validated['estimated_potential'],
                'status' => 'Pending',
                'route_to' => 'N/A',
            ]);

            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'item_master_id' => $itemMasterId,
                    'material_type' => $subCategoryLabel,
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued' => $itemData['quantity_issued'] ?? null,
                ]);
            }

            Log::info("Memulai proses approval untuk Free Goods Requisition #{$requisition->id}. Path: {$pathSubCategory}");

            $this->generateApprovalLogs(
                $user, 
                $requisition->id, 
                'FREE GOODS', 
                $pathSubCategory // Mengirimkan sub_category path yang spesifik
            );

            $firstLog = ApprovalLog::where('requisition_id', $requisition->id)->orderBy('level', 'asc')->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    $requisition->update(['route_to' => $firstApprover->name]);
                    sendFreeGoods::dispatch($requisition, $firstApprover, $firstLog->token); 
                } else {
                    $requisition->update(['status' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                    Log::error("Approver pertama dengan NIK {$firstLog->approver_nik} tidak ditemukan.");
                }
            } else {
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada alur approval Free Goods yang cocok. Auto-complete Requisition ID {$requisition->id}.");
            }

            DB::commit();
            $nextFgNumber = $this->generateFgNumber();

            return response()->json([
                'success' => true,
                'message' => 'Free Goods Requisition berhasil dibuat dan permintaan persetujuan telah dikirim.',
                'next_fg_number' => $nextFgNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat Free Goods requisition: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function update(UpdateFreeGoodsRequest $request, $id)
    {
        $requisition = Requisition::findOrFail($id);
        $validated = $request->validated();
        
        $subCategoryLabel = $requisition->sub_category ?? 'General Request'; 

        DB::beginTransaction();
        try {
            
            $requisition->update($validated);
            $requisition->requisitionItems()->delete();

            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id'    => $requisition->id,
                    'item_master_id'    => $itemMasterId,
                    'material_type'     => $subCategoryLabel,
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued'   => $itemData['quantity_issued'] ?? null,
                ]);
            }
            
            $message = 'Free Goods Requisition berhasil diubah.';
            
            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah Free Goods requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan cek log.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $requisition = Requisition::findOrFail($id);
            $requisition->delete();

            return response()->json(['success' => true, 'message' => 'Free Goods Requisition was successfully deleted.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus requisition.'], 500);
        }
    }

    public function showResponseForm(Request $request, $token)
    {
        $action = $request->query('action');
        $validActions = ['approve', 'review', 'reject', 'submit'];

        if (!in_array($action, $validActions)) {
            return view('page.freegoods.invalid', ['message' => 'Invalid action.']);
        }

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        $tracking = !$approvalLog ? Tracking::where('token', $token)->whereNull('last_updated')->first() : null;

        if (!$approvalLog && !$tracking) {
            return view('page.freegoods.invalid', ['message' => 'This request is invalid or has been processed.']);
        }

        // ===================================================================
        // [SOLUSI FINAL NOT FOUND PADA QUICK APPROVE]
        // ===================================================================
        if ($action === 'approve' && !$tracking) { 
            $request->merge(['token' => $token, 'action' => 'approve', 'notes' => 'Approved via quick action link.']);
            return $this->processApproval($request);
        }
        // ===================================================================

        $requisition = $approvalLog ? $approvalLog->requisition : $tracking->requisition;
        $requisition->load('requester.department', 'customer', 'requisitionItems.itemMaster', 'approvalLogs.approver');

        $isWarehouseProcess = (bool)$tracking;
        $pageTitle = $isWarehouseProcess ? ($tracking->current_position ?? 'Warehouse Process') : 'Approval Action';

        if ($action === 'reject') {
            $action = 'review'; // Force reject to review form to add notes
        }

        $viewData = [
            'token' => $token,
            'action' => $action,
            'requisition' => $requisition,
            'pageTitle' => $pageTitle,
            'isWarehouseProcess' => $isWarehouseProcess,
        ];

        return view('page.freegoods.response-form', $viewData);
    }

    public function processApproval(Request $request)
    {
        // 1. Logika untuk membedakan Quick Action dari Form Submit
        if ($request->input('action') === 'approve' && 
            $request->input('notes') === 'Approved via quick action link.') 
        {
            $validated = $request->all();
        } else {
             // Validasi normal jika datang dari form submit (POST)
            $validated = $request->validate([
                'token' => 'required|string',
                'action' => 'required|string|in:approve,review,reject,submit',
                'notes' => 'nullable|string|max:500|required_if:action,review,reject',
            ]);
        }
        
        $token = $validated['token'];
        $action = $validated['action'];
        $notes = $validated['notes'] ?? null;

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if ($approvalLog) {
            return $this->processApprovalStep($approvalLog, $action, $notes);
        }
        
        $tracking = Tracking::where('token', $token)->whereNull('last_updated')->first();

        if ($tracking) {
            // Logika processWarehouseStep
            DB::beginTransaction();
            try {
                $requisition = $tracking->requisition;
                Log::info("Processing warehouse step for Free Goods Requisition #{$requisition->id}. Current position: {$tracking->current_position}.");

                $updateData = ['token' => null, 'last_updated' => now()]; 
                
                $defaultNote = "Proses {$tracking->current_position} berhasil disubmit tanpa notes.";
                $updateData['notes'] = $notes ?: $defaultNote;

                $tracking->update($updateData);

                $newStatus = $this->advanceWarehouseStep($requisition); 

                DB::commit();

                $requisition->load('customer'); 
                
                return redirect()->route('fg.approval.success')
                    ->with('card_class', 'success')->with('title', 'Warehouse Step Completed')
                    ->with('message', 'Warehouse process step has been recorded.')
                    ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                    ->with('action_text', 'Processed')->with('approver_name', $tracking->current_position)
                    ->with('new_status', $newStatus);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal melanjutkan proses warehouse Free Goods: " . $e->getMessage());
                return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred. Please check the system logs.');
            }
        }

        return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'Invalid Request')->withMessage('This approval request is invalid or has already been processed.');
    }
    
    //======================================================================
    // PRIVATE/HELPER FUNCTIONS (Core Logic)
    //======================================================================

    /**
     * Memproses satu langkah persetujuan (approve/reject).
     */
    private function processApprovalStep(ApprovalLog $approvalLog, string $action, ?string $notes)
    {
        DB::beginTransaction();
        try {
            $requisition = $approvalLog->requisition->load('customer', 'requester'); 
            $approverName = $approvalLog->approver->name ?? 'Approver';
            $finalNotes = $notes;
            
            $logStatus = ($action === 'reject') ? 'Rejected' : 'Approved';
            $cardClass = ($logStatus === 'Rejected') ? 'reject' : 'success';
            
            if ($logStatus === 'Rejected') {
                $finalNotes = $notes ?: 'Rejected without reason';
            } elseif (empty($notes)) {
                $finalNotes = 'Approved by ' . $approverName;
            }

            $approvalLog->update([
                'status'       => $logStatus,
                'notes'        => $finalNotes,
                'responded_at' => now(), 
                'token'        => null, 
            ]);

            $title = 'Action Submitted';
            $actionText = ucfirst($logStatus);
            $newStatus = 'In Progress';

            if ($logStatus === 'Rejected') {
                $requisition->update(['status' => 'Rejected', 'route_to' => 'Finished (Rejected)']);
                
                if ($requisition->requester?->email) {
                    Mail::to($requisition->requester->email)->send(new MailRejectFreeGoods($requisition, $approverName, $finalNotes));
                }
                $title = 'Requisition Rejected';
                $newStatus = 'Rejected';

            } else { // Approved
                if (!str_starts_with($finalNotes, 'Approved by')) {
                    $title = 'Approved with Review';
                    $actionText = 'Approved with Review';
                }
                
                $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                                ->where('level', '>', $approvalLog->level)
                                                ->orderBy('level', 'asc')->first();

                if ($nextApprovalLog) {
                    $nextApprover = User::where('nik', $nextApprovalLog->approver_nik)->first();
                    if ($nextApprover) {
                        $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprover->name]);
                        dispatch(new sendFreeGoods($requisition, $nextApprover, $nextApprovalLog->token, ['mail_type' => 'approval']));
                        $newStatus = "Waiting for {$nextApprover->name}";
                    } else {
                        Log::error("Approver berikutnya dengan NIK {$nextApprovalLog->approver_nik} tidak ditemukan.");
                        $requisition->update(['status' => 'Error', 'route_to' => 'Error: Approver Not Found']);
                        $newStatus = 'Error';
                        $cardClass = 'reject';
                    }
                } else {
                    $requisition->update(['status' => 'Approved']);
                    $newStatus = $this->startPostApprovalProcess($requisition); 
                }
            }

            DB::commit();

            return redirect()->route('fg.approval.success') 
                ->with('card_class', $cardClass)
                ->with('title', $title)
                ->with('message', 'Your response has been successfully recorded.')
                ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                ->with('action_text', $actionText)
                ->with('approver_name', $approverName)
                ->with('new_status', $newStatus);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal proses approval Free Goods #{$approvalLog->id}: " . $e->getMessage());
            return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred during approval process.');
        }
    }

    private function startPostApprovalProcess(Requisition $requisition)
    {
        $newStatus = 'Processing';
        Log::info("Approval path selesai untuk Free Goods Requisition #{$requisition->id}. Memulai proses warehouse.");

        $stepName = 'Outward WH Supervisor';
        $this->createOrUpdateTracking($requisition, $stepName, "Menunggu proses oleh outward.");
        
        return $stepName;
    }

    private function advanceWarehouseStep(Requisition $requisition)
    {
        $requisition->load('tracking');
        $currentPosition = $requisition->tracking->current_position ?? '';

        if (str_contains($currentPosition, 'Outward WH Supervisor')) {
            return $this->notifyRequesterAsCompleted($requisition);
        }

        Log::warning("advanceWarehouseStep dipanggil untuk FG requisition #{$requisition->id} tanpa alur yang cocok.");
        return $this->notifyRequesterAsCompleted($requisition);
    }

    private function notifyRequesterAsCompleted(Requisition $requisition)
    {
        $statusText = 'Completed';
        $requisition->update(['status' => $statusText, 'route_to' => 'Finished']);

        Tracking::updateOrCreate(
            ['requisition_id' => $requisition->id],
            [
                'current_position' => $statusText,
                'notes'            => "Free Goods process is complete and ready for the requester.",
                'last_updated'     => now(),
                'token'            => null, 
            ]
        );

        if ($requisition->requester?->email) {
            dispatch(new sendFreeGoods($requisition, $requisition->requester, null, [
                'mail_type' => 'completed_notification'
            ]));
        }
        Log::info("Free Goods Requisition #{$requisition->id} selesai. Notifikasi dikirim ke requester.");
        return $statusText;
    }

    private function createOrUpdateTracking(Requisition $requisition, string $currentPosition, string $notes)
    {
        $token = Str::uuid()->toString(); 

        Tracking::updateOrCreate(
            ['requisition_id' => $requisition->id],
            [
                'current_position' => $currentPosition,
                'notes'            => $notes,
                'last_updated'     => null, 
                'token'            => $token, 
            ]
        );

        $requisition->update(['status' => 'Processing', 'route_to' => $currentPosition]);

        $user = $this->findUserForStep($currentPosition);
        if ($user) {
            dispatch(new sendFreeGoods($requisition, $user, $token, [ 
                'mail_type'    => 'warehouse_process',
                'process_step' => $currentPosition
            ]));
        }
    }

    private function findUserForStep(string $stepName)
    {
        if (str_contains($stepName, 'Outward WH Supervisor')) {
            $user = User::where('name', 'like', '%' . 'Outward WH Supervisor' . '%')->first();
            return $user ?: User::where('nik', 'WH0002')->first();
        }
        return null;
    }

    public function edit($id)
    {
        $requisition = Requisition::with([
            'requisitionItems.itemMaster',
        ])->findOrFail($id);

        $responseData = $requisition->toArray();

        $selectedMasterIds = $requisition->requisitionItems->pluck('item_master_id')->unique()->values()->all();

        $productOptions = ItemMaster::select('id', 'item_master_code', 'item_master_name')
            ->get()
            ->map(fn($item) => ['id' => $item->id, 'text' => "[{$item->item_master_code}] {$item->item_master_name}"])
            ->toArray();

        $responseData['selected_master_ids'] = $selectedMasterIds;
        $responseData['product_options'] = $productOptions;

        return response()->json($responseData);
    }

    public function show($id)
    {
        $requisition = Requisition::with([
            'customer:id,name,address',
            'requester:nik,name,email',
            'requisitionItems:requisition_id,item_master_id,quantity_required,quantity_issued',
            'requisitionItems.itemMaster:id,item_master_code,item_master_name,unit',
            'approvalLogs:id,requisition_id,approver_nik,status,notes,updated_at,level',
            'approvalLogs.approver:nik,name',
            'trackings' 
        ])->findOrFail($id);

        $trackingHistory = [];
        // ... (Logika detail tracking history blm disertakan)

        $responseData = $requisition->toArray();
        $responseData['tracking_history'] = $trackingHistory;

        return response()->json($responseData);
    }

    public function showSuccessPage()
    {
        if (!session('title')) {
            return redirect('/');
        }
        return view('page.freegoods.response-success');
    }
}