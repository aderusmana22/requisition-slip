<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSampleRequisitionRequest;
use App\Http\Requests\UpdateSampleRequisitionRequest;
use App\Jobs\sendSample;
use App\Mail\MailRejectSample;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Master\ItemDetail;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\Requisition\RequisitionSpecial;
use App\Models\Requisition\ApprovalPath;
use App\Models\User;
use App\Models\Requisition\ApprovalLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class SampleController extends Controller
{
    private function generateSrsNumber()
    {
        $prefix = 'S';
        $year = date('y');
        $month = date('m');
        $currentPrefix = "$prefix $year $month";

        $lastRequisition = Requisition::where('no_srs', 'LIKE', $currentPrefix . ' %')
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

    public function getProductsByMaterialTypes(Request $request)
    {
        $request->validate(['material_types' => 'required|array']);

        $products = ItemMaster::whereHas('itemDetails', function ($query) use ($request) {
            $query->whereIn('material_type', $request->material_types);
        })->select('id', 'item_master_name')->distinct()->get();

        return response()->json($products);
    }

    public function getAllItemMasters()
    {
        $masters = ItemMaster::select('id', 'item_master_code', 'item_master_name', 'unit')->get();
        return response()->json($masters);
    }

    public function getItemDetailsByProducts(Request $request)
    {
        $request->validate(['product_ids' => 'required|array']);

        // Ambil semua item detail yang terkait dengan item master yang dipilih
        $details = ItemDetail::whereIn('item_master_id', $request->product_ids)->get();

        return response()->json($details);
    }

    public function index()
    {
        $customers = Customer::all();
        $materialTypes = ItemDetail::distinct()->pluck('material_type');
        $generatedSrs = $this->generateSrsNumber();
        $user = Auth::user();
        $userAccount = $user->department->code ?? null;
        $userDepartmentName = $user->department?->name ?? null;
        $allowedSubCategories = [];

        if ($user->hasRole('super-admin')) {
            $allowedSubCategories = ['Packaging', 'Finished Goods', 'Special Order'];
        } else {
            if (in_array($userAccount, ['5300', '5302'])) {
                $allowedSubCategories[] = 'Packaging';
                $allowedSubCategories[] = 'Finished Goods';
            }

            if ($userDepartmentName === 'Sales & Marketing') {
                $allowedSubCategories[] = 'Special Order';
            }
        }

        $allowedSubCategories = array_unique($allowedSubCategories);

        return view('page.sample.index', compact(
            'customers', 'materialTypes', 'allowedSubCategories',
            'generatedSrs', 'userAccount', 'userDepartmentName'));
    }

    public function getData()
    {
        $requisitions = DB::table('requisitions')
            ->leftJoin('users', 'requisitions.requester_nik', '=', 'users.nik')
            ->leftJoin('customers', 'requisitions.customer_id', '=', 'customers.id')
            ->where('requisitions.category', 'SAMPLE')
            ->select(
                'requisitions.id',
                'requisitions.requester_nik',
                'requisitions.request_date',
                'requisitions.sub_category',
                'requisitions.route_to',
                'requisitions.status',
                'users.name as requester_name',
                'users.avatar',
                'customers.name as customer_name'
            );

        return DataTables::of($requisitions)
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
            ->editColumn('sub_category', function ($requisition) {
                $subCategory = $requisition->sub_category;
                $badgeClass = 'bg-secondary';
                if ($subCategory == 'Packaging') $badgeClass = 'bg-info';
                elseif ($subCategory == 'Finished Good') $badgeClass = 'bg-primary';
                elseif ($subCategory == 'Special Order') $badgeClass = 'bg-warning text-dark';
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
                $editBtn = '<button type="button" class="btn btn-sm btn-warning btn-edit-requisition" data-id="' . $row->id . '" title="Edit"><i class="fa-solid fa-pencil text-white"></i></button>';
                $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete-requisition" data-id="' . $row->id . '" title="Delete"><i class="fa-solid fa-trash-alt text-white"></i></button>';

                $qaFillBtn = '';
                if (
                    $user->department?->name === 'QM & HSE' &&
                    $row->sub_category === 'Special Order' &&
                    $row->status === 'Approved'
                ) {
                    $qaFillBtn = '<button type="button" class="btn btn-sm btn-success btn-qa-form" data-id="' . $row->id . '" title="Complete QA Form"><i class="fa-solid fa-check-double text-white"></i></button>';
                    $editBtn = '';
                    $deleteBtn = '';
                }

                return "<div class='d-flex gap-1'>{$viewBtn} {$qaFillBtn} {$editBtn} {$deleteBtn}</div>";
            })
            ->rawColumns(['requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $requisition = Requisition::with([
            'customer:id,name,address',
            'requester:nik,name,email',
            'requisitionItems:requisition_id,item_master_id,item_detail_id,quantity_required,quantity_issued',
            'requisitionItems.itemMaster:id,item_master_code,item_master_name,unit',
            'requisitionItems.itemDetail:id,item_detail_code,item_detail_name,unit',
            'requisitionSpecial',
            'approvalLogs:id,requisition_id,approver_nik,status,notes,updated_at',
            'approvalLogs.approver:nik,name'
        ])->findOrFail($id);

        $trackingHistory = [];

        $trackingHistory[] = [
            'status' => 'Request Created',
            'user' => $requisition->requester->name ?? 'N/A',
            'date' => $requisition->created_at->format('d M Y, H:i'),
            'notes' => 'Sample Requisition form was created.',
            'icon' => 'fa-solid fa-file-circle-plus',
            'color' => 'bg-secondary'
        ];

        foreach ($requisition->approvalLogs as $log) {
            $statusText = '';
            $userText = $log->approver->name ?? 'Unknown Approver';
            $dateText = $log->updated_at ? $log->updated_at->format('d M Y, H:i') : $requisition->updated_at->format('d M Y, H:i');
            $notesText = $log->notes ?? '';
            $icon = 'fa-solid fa-clock';
            $color = 'bg-info';

            switch ($log->status) {
                case 'Pending':
                    $statusText = 'Sent for Approval';
                    $notesText = 'Waiting for approval from ' . $userText;
                    $icon = 'fa-solid fa-paper-plane';
                    $color = 'bg-primary';
                    break;
                case 'Approved':
                    $statusText = 'Approved';
                    $notesText = $log->notes ?: 'Approved by ' . $userText;
                    $icon = 'fa-solid fa-circle-check';
                    $color = 'bg-success';
                    break;
                case 'Rejected':
                    $statusText = 'Rejected';
                    $notesText = $log->notes ?: 'Rejected by ' . $userText;
                    $icon = 'fa-solid fa-circle-xmark';
                    $color = 'bg-danger';
                    break;
            }

            if ($statusText) {
                 $trackingHistory[] = [
                    'status' => $statusText,
                    'user'   => $userText,
                    'date'   => $dateText,
                    'notes'  => $notesText,
                    'icon'   => $icon,
                    'color'  => $color,
                ];
            }
        }

        $responseData = $requisition->toArray();
        $responseData['tracking_history'] = $trackingHistory;

        return response()->json($responseData);
    }

    public function store(StoreSampleRequisitionRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user = Auth::user();

            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'customer_id' => $validated['customer_id'],
                'no_srs' => $this->generateSrsNumber(),
                'account' => $validated['account'],
                'cost_center' => $validated['cost_center'],
                'request_date' => $validated['request_date'],
                'category' => 'SAMPLE',
                'sub_category' => $validated['sub_category'],
                'objectives' => $validated['objectives'],
                'estimated_potential' => $validated['estimated_potential'],
                'status' => 'Draft',
                'route_to' => 'N/A',
            ]);

            Log::info("Requisition #{$requisition->id} berhasil dibuat sebagai Draft.");

            if ($validated['sub_category'] === 'Special Order') {
                RequisitionSpecial::create([
                    'requisition_id' => $requisition->id,
                    'requested_date' => $validated['requested_date'] ?? null,
                    'weight_selection' => $validated['weight_selection'] ?? null,
                    'packaging_selection' => $validated['packaging_selection'] ?? null,
                    'sample_count' => $validated['sample_count'] ?? null,
                    'coa_required' => $validated['coa_required'] ?? false,
                    'shipment_method' => $validated['shipment_method'] ?? null,
                ]);
            }

            if ($validated['sub_category'] === 'Packaging') {
                $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
                foreach ($validated['items'] as $itemDetailId => $itemData) {
                    if (isset($itemDetails[$itemDetailId])) {
                        $itemDetail = $itemDetails[$itemDetailId];
                        RequisitionItem::create([
                            'requisition_id' => $requisition->id,
                            'item_master_id' => $itemDetail->item_master_id,
                            'item_detail_id' => $itemDetail->id,
                            'material_type' => $itemDetail->material_type,
                            'quantity_required' => $itemData['quantity_required'],
                            'quantity_issued' => $itemData['quantity_issued'] ?? null,
                        ]);
                    }
                }
            } else {
                foreach ($validated['items'] as $itemMasterId => $itemData) {
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'item_master_id' => $itemMasterId,
                        'item_detail_id' => null,
                        'material_type' => $validated['sub_category'],
                        'quantity_required' => $itemData['quantity_required'],
                        'quantity_issued' => $itemData['quantity_issued'] ?? null,
                    ]);
                }
            }

            Log::info("Mencari approval path untuk: SAMPLE / {$validated['sub_category']}");
            $approvalPath = ApprovalPath::where('category', 'SAMPLE')
                ->where('sub_category', $validated['sub_category'])
                ->first();

            if ($approvalPath && !empty($approvalPath->sequence_approvers)) {
                Log::info("Approval path ditemukan untuk Requisition #{$requisition->id}. Approver NIKs: " . implode(', ', $approvalPath->sequence_approvers));

                $firstApproverNik = $approvalPath->sequence_approvers[0];
                $firstApprover = User::where('nik', $firstApproverNik)->first();

                if ($firstApprover) {
                    Log::info("Approver pertama (NIK: {$firstApproverNik}) ditemukan: {$firstApprover->name}.");

                    $requisition->update([
                        'status' => 'Pending',
                        'route_to' => $firstApprover->name
                    ]);

                    $approvalLog = ApprovalLog::create([
                        'requisition_id' => $requisition->id,
                        'approver_nik'   => $firstApprover->nik,
                        'status'         => 'Pending',
                        'level'          => 1,
                        'token'          => Str::uuid()->toString(),
                    ]);
                    Log::info("ApprovalLog berhasil dibuat untuk Requisition #{$requisition->id}.");

                    sendSample::dispatch($requisition, $firstApprover, $approvalLog->token);
                    Log::info("Job pengiriman email untuk Requisition #{$requisition->id} telah di-dispatch ke queue.");

                } else {
                    $requisition->update(['status' => 'Completed', 'route_to' => 'Error: Approver Not Found']);
                    Log::warning("Approver dengan NIK {$firstApproverNik} tidak ditemukan untuk Requisition ID {$requisition->id}.");
                }
            } else {
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada approval path yang cocok untuk Sample/{$validated['sub_category']}. Auto-complete Requisition ID {$requisition->id}.");
            }

            DB::commit();
            Log::info("Transaksi untuk Requisition #{$requisition->id} berhasil di-commit.");
            $nextSrsNumber = $this->generateSrsNumber();

            return response()->json([
                'success' => true,
                'message' => 'Sample Requisition berhasil dibuat dan permintaan persetujuan telah dikirim.',
                'next_srs_number' => $nextSrsNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat sample requisition: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan cek log.'], 500);
        }
    }

    public function edit($id)
    {
        $requisition = Requisition::with([
            'requisitionItems.itemMaster',
            'requisitionItems.itemDetail',
            'requisitionSpecial'
        ])->findOrFail($id);

        $responseData = $requisition->toArray();

        $selectedMasterIds = $requisition->requisitionItems->pluck('item_master_id')->unique()->values()->all();
        $attachedMaterialTypes = $requisition->requisitionItems->pluck('material_type')->unique()->values()->all();

        $productOptions = [];
        if ($requisition->sub_category === 'Packaging') {
            if (!empty($attachedMaterialTypes)) {
                $productOptions = ItemMaster::whereHas('itemDetails', function ($query) use ($attachedMaterialTypes) {
                    $query->whereIn('material_type', $attachedMaterialTypes);
                })->select('id', 'item_master_name as text')->get()->toArray();
            }
        } else if ($requisition->sub_category === 'Finished Good' || $requisition->sub_category === 'Special Order') {
            $productOptions = ItemMaster::select('id', 'item_master_code', 'item_master_name')
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'text' => "[{$item->item_master_code}] {$item->item_master_name}"])
                ->toArray();
        }

        $responseData['attached_material_types'] = $attachedMaterialTypes;
        $responseData['selected_master_ids'] = $selectedMasterIds;
        $responseData['product_options'] = $productOptions;

        return response()->json($responseData);
    }

    public function update(UpdateSampleRequisitionRequest $request, $id)
    {
        $requisition = Requisition::findOrFail($id);
        $user = Auth::user();
        $userDepartmentName = $user->department?->name;
        $validated = $request->validated();

        if (
            $validated['sub_category'] === 'Special Order' &&
            !$user->hasRole('super-admin') &&
            $userDepartmentName !== 'QM & HSE' &&
            ($request->has('sample_origin') || $request->has('production_date'))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya departemen QM & HSE yang dapat mengubah detail ini.'
            ], 403);
        }
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $requisition->update($validated);

            $requisition->requisitionItems()->delete();

            if ($validated['sub_category'] === 'Packaging') {
                $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
                foreach ($validated['items'] as $itemDetailId => $itemData) {
                    if (isset($itemDetails[$itemDetailId])) {
                        $itemDetail = $itemDetails[$itemDetailId];
                        RequisitionItem::create([
                            'requisition_id' => $requisition->id,
                            'item_master_id' => $itemDetail->item_master_id,
                            'item_detail_id' => $itemDetail->id,
                            'material_type' => $itemDetail->material_type,
                            'quantity_required' => $itemData['quantity_required'],
                            'quantity_issued' => $itemData['quantity_issued'] ?? null,
                        ]);
                    }
                }
            } else {
                foreach ($validated['items'] as $itemMasterId => $itemData) {
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'item_master_id' => $itemMasterId,
                        'item_detail_id' => null,
                        'material_type' => $validated['sub_category'],
                        'quantity_required' => $itemData['quantity_required'],
                        'quantity_issued' => $itemData['quantity_issued'] ?? null,
                    ]);
                }
            }

            if ($validated['sub_category'] === 'Special Order' && $requisition->requisitionSpecial) {
                $requisition->requisitionSpecial->update([
                    'requested_date'      => $validated['requested_date'] ?? null,
                    'weight_selection'    => $validated['weight_selection'] ?? null,
                    'packaging_selection' => $validated['packaging_selection'] ?? null,
                    'sample_count'        => $validated['sample_count'] ?? null,
                    'coa_required'        => $validated['coa_required'] ?? false,
                    'shipment_method'     => $validated['shipment_method'] ?? null,
                ]);
            }

            if ($userDepartmentName === 'QM & HSE' && $requisition->status === 'Approved') {
                $requisition->update(['status' => 'Completed']);
                Log::info("Requisition #{$requisition->id} diselesaikan oleh QM & HSE.");
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Sample Requisition berhasil diubah.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah sample requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $requisition = Requisition::findOrFail($id);
            $requisition->delete();

            return response()->json(['success' => true, 'message' => 'Requisition telah berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus requisition.'], 500);
        }
    }

    // ================ Approval via Email Link ================== //
    public function showResponseForm(Request $request, $token)
    {
        $action = $request->query('action');
        $validActions = ['approve', 'review', 'reject'];

        if (!in_array($action, $validActions)) {
            return view('page.sample.invalid', ['message' => 'Invalid action or incorrect link.']);
        }

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if (!$approvalLog) {
            return view('page.sample.invalid', ['message' => 'This approval request is invalid or has already been processed.']);
        }

        $requisition = $approvalLog->requisition->load([
            'requester',
            'customer',
            'requisitionItems.itemMaster',
            'requisitionItems.itemDetail',
            'approvalLogs.approver'
        ]);

        if ($action === 'approve') {
            return view('page.sample.response-form', [
                'token' => $token,
                'action' => $action,
                'requisition' => $requisition,
            ]);
        }

        return view('page.sample.response-form', [
            'token' => $token,
            'action' => $action,
            'requisition' => $requisition,
        ]);
    }

    public function processApproval(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|exists:approval_logs,token',
            'action' => 'required|string|in:approve,review,reject',
            'notes' => 'nullable|string|max:500|required_if:action,review,reject',
        ], [
            'token.exists' => 'Approval token not found or already used.',
            'notes.required_if' => 'Notes are required for review or reject actions.',
        ]);

        $approvalLog = ApprovalLog::where('token', $validated['token'])->where('status', 'Pending')->first();
        if (!$approvalLog) {
            return response()->json(['success' => false, 'message' => 'This request has already been processed.'], 422);
        }

        DB::beginTransaction();
        try {
            $requisition = $approvalLog->requisition->load('customer');
            $action = $validated['action'];
            $notes = ($action === 'approve') ? 'Approved without notes' : $validated['notes'];
            $approverName = $approvalLog->approver->name;

            $newStatus = '';

            $approvalLog->update([
                'status' => ($action === 'reject') ? 'Rejected' : 'Approved',
                'notes' => $notes,
                'responded_at' => now(),
            ]);

            if ($action === 'reject') {
                $requisition->update([
                    'status' => 'Rejected',
                    'route_to' => 'Finished (Rejected)'
                ]);
                $newStatus = 'Rejected';

                $requester = $requisition->requester;
                if ($requester && $requester->email) {
                    Mail::to($requester->email)->send(new MailRejectSample($requisition, $approverName, $notes));
                }
            } else {
                $approvalPath = ApprovalPath::where('category', $requisition->category)
                    ->where('sub_category', $requisition->sub_category)
                    ->first();

                $approvers = $approvalPath->sequence_approvers ?? [];
                $nextLevel = $approvalLog->level + 1;

                if (isset($approvers[$nextLevel - 1])) {
                    $nextApproverNik = $approvers[$nextLevel - 1];
                    $nextApprover = User::where('nik', $nextApproverNik)->first();

                    if ($nextApprover) {
                        $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprover->name]);
                        $newStatus = 'In Progress';
                        $newLog = ApprovalLog::create([
                            'requisition_id' => $requisition->id, 'approver_nik' => $nextApprover->nik,
                            'status' => 'Pending', 'level' => $nextLevel, 'token' => Str::uuid()->toString(),
                        ]);
                        sendSample::dispatch($requisition, $nextApprover, $newLog->token);
                    } else {
                        throw new \Exception("Next approver user (NIK: {$nextApproverNik}) not found.");
                    }
                } else {
                    $requisition->update(['status' => 'Approved', 'route_to' => 'Finished (Approved)']);
                    $newStatus = 'Approved';
                }
                if ($requisition->sub_category === 'Special Order') {
                    $qaUsers = User::whereHas('department', function ($query) {
                        $query->where('name', 'QM & HSE');
                    })->get();

                    if ($qaUsers->isNotEmpty()) {
                        Log::info("Notifikasi untuk melengkapi form dikirim ke {$qaUsers->count()} user QM & HSE untuk Requisition #{$requisition->id}.");
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => 'Your response has been successfully recorded.',
                'no_srs'        => $requisition->no_srs,
                'customer_name' => $requisition->customer->name ?? 'N/A',
                'approver_name' => $approverName,
                'new_status'    => $newStatus,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process approval: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
            return response()->json(['success' => false, 'message' => 'A system error occurred. Please contact the administrator.'], 500);
        }
    }
}
