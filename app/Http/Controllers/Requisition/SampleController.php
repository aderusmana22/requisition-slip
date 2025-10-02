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
use App\Models\Requisition\Tracking;
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
use App\Traits\ApprovalTrait;

class SampleController extends Controller
{
    use ApprovalTrait;

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
            if (in_array($userAccount, ['5300'])) {
                $allowedSubCategories[] = 'Packaging';
                $allowedSubCategories[] = 'Finished Goods';
                $allowedSubCategories[] = 'Special Order';
            }

            if ($userDepartmentName === 'QM & HSE') {
                $allowedSubCategories[] = 'Finished Goods';
            }

            if ($userDepartmentName === 'R&D') {
                $allowedSubCategories[] = 'Packaging';
                $allowedSubCategories[] = 'Finished Goods';
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
        // [DIPERBAIKI] Eager load relasi trackings
        $requisition = Requisition::with([
            'customer:id,name,address',
            'requester:nik,name,email',
            'requisitionItems:requisition_id,item_master_id,item_detail_id,material_type,quantity_required,quantity_issued',
            'requisitionItems.itemMaster:id,item_master_code,item_master_name,unit',
            'requisitionItems.itemDetail:id,item_detail_code,item_detail_name,unit',
            'requisitionSpecial',
            'approvalLogs:id,requisition_id,approver_nik,status,notes,updated_at,level',
            'approvalLogs.approver:nik,name',
            'trackings' // Load data tracking
        ])->findOrFail($id);

        $trackingHistory = [];

        // 1. Tambahkan log pembuatan
        $trackingHistory[] = [
            'status' => 'Request Created',
            'user' => $requisition->requester->name ?? 'N/A',
            'date' => $requisition->created_at->format('d M Y, H:i'),
            'notes' => 'Sample Requisition form was created.',
        ];

        // 2. Proses semua approval logs dari approval path
        foreach ($requisition->approvalLogs->where('level', '<=', 100) as $log) {
            $statusText = 'Approval ' . $log->status;
            $userText = $log->approver->name ?? 'Unknown Approver';
            $dateText = $log->updated_at ? $log->updated_at->format('d M Y, H:i') : '-';
            $notesText = $log->notes ?? '';

            if ($log->status === 'Pending') {
                 $notesText = 'Waiting for approval from ' . $userText;
            }

            $trackingHistory[] = [
                'status' => $statusText, 'user' => $userText,
                'date' => $dateText, 'notes' => $notesText,
            ];
        }

        // 3. Proses semua tracking logs untuk alur setelah approval
        foreach ($requisition->trackings as $track) {
            $trackingHistory[] = [
                'status' => 'Processing',
                'user' => $track->current_position,
                'date' => $track->last_updated ? Carbon::parse($track->last_updated)->format('d M Y, H:i') : '-',
                'notes' => $track->notes,
            ];
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
            $user = User::with('atasan')->find(Auth::id());

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
                'print_batch' => $validated['print_batch'] ?? false,
                'status' => 'Pending',
                'route_to' => 'N/A',
            ]);

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
            } else { // Finished Goods & Special Order
                foreach ($validated['items'] as $itemMasterId => $itemData) {
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'item_master_id' => $itemMasterId,
                        'material_type' => $validated['sub_category'],
                        'quantity_required' => $itemData['quantity_required'],
                        'quantity_issued' => $itemData['quantity_issued'] ?? null,
                    ]);
                }
            }

            if ($validated['sub_category'] === 'Special Order') {
                $itemMasterIds = array_keys($validated['items']);
                $productNames = ItemMaster::whereIn('id', $itemMasterIds)->pluck('item_master_name')->implode(', ');

                RequisitionSpecial::create([
                    'requisition_id' => $requisition->id,
                    'products' => $productNames,
                    'requested_date' => $validated['request_date'],
                    'end_date' => $validated['end_date'],
                    'weight_selection' => $validated['weight_selection'],
                    'packaging_selection' => $validated['packaging_selection'],
                    'sample_count' => $validated['sample_count'],
                    'purpose' => $validated['purpose'],
                    'coa_required' => $validated['coa_required'] ?? false,
                    'shipment_method' => $validated['shipment_method'],
                ]);
            }

            Log::info("Memulai proses approval untuk Requisition #{$requisition->id} menggunakan ApprovalTrait.");

            // Panggil fungsi dari trait untuk membuat semua log approval
            $this->generateApprovalLogs($user, $requisition->id, 'SAMPLE', $validated['sub_category']);

            // Cari log pertama untuk dikirim email
            $firstLog = ApprovalLog::where('requisition_id', $requisition->id)->orderBy('level', 'asc')->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    // Update 'route_to' ke approver pertama
                    $requisition->update(['route_to' => $firstApprover->name]);

                    // Kirim email hanya ke approver pertama
                    sendSample::dispatch($requisition, $firstApprover, $firstLog->token);
                    Log::info("Job email dikirim ke approver pertama: {$firstApprover->name} (NIK: {$firstApprover->nik}).");
                } else {
                    $requisition->update(['status' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                    Log::error("Approver pertama dengan NIK {$firstLog->approver_nik} tidak ditemukan.");
                }
            } else {
                // Jika tidak ada alur approval yang dihasilkan oleh trait
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada alur approval yang cocok. Auto-complete Requisition ID {$requisition->id}.");
            }

            DB::commit();
            $nextSrsNumber = $this->generateSrsNumber();

            return response()->json([
                'success' => true,
                'message' => 'Sample Requisition berhasil dibuat dan permintaan persetujuan telah dikirim.',
                'next_srs_number' => $nextSrsNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat sample requisition: ' . $e->getMessage() . ' di baris ' . $e->getLine() . ' di file ' . $e->getFile());
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
        $requisition = Requisition::with('requester')->findOrFail($id); // Eager load requester
        $user = Auth::user();
        $userDepartmentName = $user->department?->name;

        if (
            !$user->hasRole('super-admin') &&
            $userDepartmentName !== 'QM & HSE' &&
            ($request->has('source') || $request->has('production_date'))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya departemen QM & HSE yang dapat mengubah detail ini.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $isQmSubmission = $request->has('source');

            if ($isQmSubmission) {
                RequisitionSpecial::updateOrCreate(
                    ['requisition_id' => $requisition->id],
                    [
                        'source'             => $validated['source'] ?? null,
                        'description'        => $validated['description'] ?? null,
                        'production_date'    => $validated['production_date'] ?? null,
                        'preparation_method' => $validated['preparation_method'] ?? null,
                        'sample_notes'       => $validated['sample_notes'] ?? null,
                    ]
                );

                if ($requisition->status === 'Approved') {
                    // Panggil helper untuk menyelesaikan proses dan mengirim notifikasi
                    $this->notifyRequesterAsCompleted($requisition);
                    Log::info("Requisition #{$requisition->id} diselesaikan oleh QM & HSE.");
                }

            } else {
                // Logika update biasa (tidak diubah)
                $requisition->update($validated);
                $requisition->requisitionItems()->delete();

                if ($validated['sub_category'] === 'Packaging') {
                    $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
                    foreach ($validated['items'] as $itemDetailId => $itemData) {
                        if (isset($itemDetails[$itemDetailId])) {
                            $itemDetail = $itemDetails[$itemDetailId];
                            RequisitionItem::create([
                                'requisition_id'    => $requisition->id,
                                'item_master_id'    => $itemDetail->item_master_id,
                                'item_detail_id'    => $itemDetail->id,
                                'material_type'     => $itemDetail->material_type,
                                'quantity_required' => $itemData['quantity_required'],
                                'quantity_issued'   => $itemData['quantity_issued'] ?? null,
                            ]);
                        }
                    }
                } else { // Finished Goods & Special Order
                    foreach ($validated['items'] as $itemMasterId => $itemData) {
                        RequisitionItem::create([
                            'requisition_id'    => $requisition->id,
                            'item_master_id'    => $itemMasterId,
                            'material_type'     => $validated['sub_category'],
                            'quantity_required' => $itemData['quantity_required'],
                            'quantity_issued'   => $itemData['quantity_issued'] ?? null,
                        ]);
                    }
                }

                if ($validated['sub_category'] === 'Special Order') {
                    $itemMasterIds = array_keys($validated['items']);
                    $productNames = ItemMaster::whereIn('id', $itemMasterIds)->pluck('item_master_name')->implode(', ');

                    RequisitionSpecial::updateOrCreate(
                        ['requisition_id' => $requisition->id],
                        [
                            'products'            => $productNames,
                            'requested_date'      => $validated['request_date'],
                            'end_date'            => $validated['end_date'],
                            'weight_selection'    => $validated['weight_selection'],
                            'packaging_selection' => $validated['packaging_selection'],
                            'sample_count'        => $validated['sample_count'],
                            'purpose'             => $validated['purpose'],
                            'coa_required'        => $validated['coa_required'] ?? false,
                            'shipment_method'     => $validated['shipment_method'],
                        ]
                    );
                } else {
                    $requisition->requisitionSpecial()->delete();
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Sample Requisition berhasil diubah.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah sample requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan cek log.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $requisition = Requisition::findOrFail($id);
            $requisition->delete();

            return response()->json(['success' => true, 'message' => 'Sample Requisition was successfully deleted.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus requisition.'], 500);
        }
    }

    // ================ Approval via Email Link ================== //
    public function showResponseForm(Request $request, $token)
    {
        $action = $request->query('action');
        // Tambahkan 'submit' ke dalam aksi yang valid
        $validActions = ['approve', 'review', 'reject', 'submit'];

        if (!in_array($action, $validActions)) {
            return view('page.sample.invalid', ['message' => 'Invalid action or incorrect link.']);
        }

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if (!$approvalLog) {
            return view('page.sample.invalid', ['message' => 'This approval request is invalid or has already been processed.']);
        }

        $requisition = $approvalLog->requisition->load('requester', 'customer', 'requisitionItems.itemMaster', 'requisitionItems.itemDetail', 'approvalLogs.approver');

        // [DIPERBAIKI] Cek apakah ini proses warehouse atau bukan (berdasarkan level)
        $isWarehouseProcess = $approvalLog->level > 100;
        $pageTitle = $isWarehouseProcess ? str_replace('Waiting for: ', '', $approvalLog->notes ?? 'Warehouse Process') : 'Approval Action';

        $viewData = [
            'token' => $token,
            'action' => $action,
            'requisition' => $requisition,
            'pageTitle' => $pageTitle,
            'reviewRadioText' => $isWarehouseProcess ? 'Submit with Review' : 'Approve with Review',
            'submitButtonText' => $isWarehouseProcess ? 'Submit Process' : 'Submit Approval with Review',
        ];

        // Jika aksinya adalah 'submit' dari WH, perlakukan seperti 'approve' agar form auto-submit
        if ($action === 'submit') {
            $viewData['action'] = 'approve';
        }

        return view('page.sample.response-form', $viewData);
    }

    public function processApproval(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|exists:approval_logs,token',
            'action' => 'required|string|in:approve,review,reject,submit',
            'notes' => 'nullable|string|max:500|required_if:action,review,reject',
        ]);

        $approvalLog = ApprovalLog::where('token', $validated['token'])->where('status', 'Pending')->first();
        if (!$approvalLog) {
            return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'Invalid Request')->withMessage('Permintaan ini sudah diproses atau tidak valid.');
        }

        $requisitionForInfo = $approvalLog->requisition->load('customer');

        DB::beginTransaction();
        try {
            $requisition = $approvalLog->requisition->load('customer', 'requester');
            $action = $validated['action'];
            $notes = in_array($action, ['review', 'reject']) ? $validated['notes'] : ('Processed by ' . ($approvalLog->approver->name ?? 'N/A'));
            $approverName = $approvalLog->approver->name ?? 'Unknown Approver';
            $newStatus = 'In Progress';
            $isWarehouseProcess = $approvalLog->level > 100;

            $approvalLog->update([
                'status' => ($action === 'reject') ? 'Rejected' : 'Approved',
                'notes' => $notes,
                'responded_at' => now(),
            ]);

            if ($action === 'reject') {
                $newStatus = 'Rejected';
                $requisition->update(['status' => 'Rejected', 'route_to' => 'Finished (Rejected)']);
                if ($requisition->requester?->email) {
                    Mail::to($requisition->requester->email)->send(new MailRejectSample($requisition, $approverName, $notes));
                }
            }
            else if ($isWarehouseProcess) {
                // Jika ini adalah proses gudang, panggil fungsi khusus
                $newStatus = $this->handleWarehouseNextStep($requisition, $approvalLog);
            } else {
                // Jika ini adalah approval formal, cari approver selanjutnya
                $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                            ->where('level', '>', $approvalLog->level) // Cari level yang lebih tinggi
                                            ->where('level', '<=', 100) // Pastikan masih dalam alur approval formal
                                            ->orderBy('level', 'asc')
                                            ->first();

                if ($nextApprovalLog) {
                    $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprovalLog->approver->name]);
                    dispatch(new sendSample($requisition, $nextApprovalLog->approver, $nextApprovalLog->token, ['mail_type' => 'approval']));
                } else {
                    // Jika tidak ada approver lagi, approval selesai, mulai proses selanjutnya
                    $requisition->update(['status' => 'Approved']);
                    $newStatus = $this->startPostApprovalProcess($requisition);
                }
            }

            DB::commit();

            return redirect()->route('approval.success')
                ->with('card_class', $action === 'reject' ? 'reject' : 'success')
                ->with('title', 'Action Submitted')
                ->with('message', 'Your response has been successfully recorded.')
                ->with('no_srs', $requisition->no_srs)
                ->with('customer_name', $requisition->customer->name ?? 'N/A')
                ->with('action_text', ucfirst($action))
                ->with('approver_name', $approverName)
                ->with('new_status', $newStatus);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process approval: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());

            return redirect()->route('approval.success')
                ->with('card_class', 'reject')
                ->with('title', 'System Error')
                ->with('message', 'An internal system error occurred. Please contact the administrator.')
                ->with('no_srs', $requisitionForInfo->no_srs ?? 'N/A')
                ->with('customer_name', $requisitionForInfo->customer->name ?? 'N/A');
        }
    }

    private function startSpecialOrderProcess(Requisition $requisition)
    {
        Log::info("Memulai alur proses Special Order untuk Requisition #{$requisition->id}.");

        $headQaUser = User::whereHas('department', fn ($q) => $q->where('name', 'QM & HSE'))
                        ->whereHas('roles', fn ($q) => $q->where('name', 'head-QA'))
                        ->first();

        if ($headQaUser) {
            $statusText = 'Waiting for QA/QM Form';
            // [DIPERBAIKI] Catat ke tabel tracking
            Tracking::create([
                'requisition_id' => $requisition->id,
                'current_position' => $headQaUser->name,
                'notes' => $statusText,
                'last_updated' => now()
            ]);
            $requisition->update(['route_to' => 'QM & HSE']);

            $formUrl = route('sample-form.index') . "?open_form={$requisition->id}";
            dispatch(new sendSample($requisition, $headQaUser, null, ['mail_type' => 'qa_form_notification', 'form_url' => $formUrl]));
            Log::info("Notifikasi untuk mengisi form Special Order dikirim ke Head QA: {$headQaUser->name}.");
            return $statusText;
        } else {
            Log::warning("Head of Department QA/QM tidak ditemukan. Proses langsung diselesaikan.");
            return $this->notifyRequesterAsCompleted($requisition);
        }
    }

    private function startPostApprovalProcess(Requisition $requisition)
    {
        $newStatus = 'Processing';
        switch ($requisition->sub_category) {
            case 'Packaging':
                $this->startPackagingProcess($requisition);
                break;
            case 'Finished Goods':
                $this->startFinishedGoodsProcess($requisition);
                break;
            case 'Special Order':
                $newStatus = $this->startSpecialOrderProcess($requisition);
                break;
            default:
                Log::warning("Tidak ada alur proses setelah approval untuk sub-category: {$requisition->sub_category}");
                $newStatus = $this->notifyRequesterAsCompleted($requisition);
                break;
        }
        return $newStatus;
    }

    private function startPackagingProcess(Requisition $requisition)
    {
        Log::info("Memulai alur proses Packaging untuk Requisition #{$requisition->id}.");
        $inwardUser = $this->findWarehouseUser('Inward WH Supervisor', 'WH0001');
        if ($inwardUser) {
            $stepName = 'Packaging - Inward Initial Check';
            // [DIPERBAIKI] Catat ke tabel tracking
            Tracking::create([
                'requisition_id' => $requisition->id, 'current_position' => $inwardUser->name,
                'notes' => $stepName, 'last_updated' => now()
            ]);
            $this->createAndSendWarehouseLog($requisition, $inwardUser, 101, $stepName);
        }
    }

    private function startFinishedGoodsProcess(Requisition $requisition)
    {
        Log::info("Memulai alur proses Finished Goods untuk Requisition #{$requisition->id}.");
        $outwardUser = $this->findWarehouseUser('Outward WH Supervisor', 'WH0002');
        if ($outwardUser) {
            $stepName = 'Finished Goods - Outward Process';
            // [DIPERBAIKI] Catat ke tabel tracking
            Tracking::create([
                'requisition_id' => $requisition->id, 'current_position' => $outwardUser->name,
                'notes' => $stepName, 'last_updated' => now()
            ]);
            $this->createAndSendWarehouseLog($requisition, $outwardUser, 101, $stepName);
        }
    }

    private function handleWarehouseNextStep(Requisition $requisition, ApprovalLog $currentLog)
    {
        $subCategory = $requisition->sub_category;
        $nextStepFound = false;

        if ($subCategory === 'Packaging') {
            switch ($currentLog->level) {
                case 101: // Setelah Inward Initial Check
                    if ($requisition->print_batch) {
                        $materialUser = $this->findWarehouseUser('Material Support Supervisor', 'MS0001');
                        if ($materialUser) {
                            $stepName = 'Packaging - Material Batch Print';
                            Tracking::create(['requisition_id' => $requisition->id, 'current_position' => $materialUser->name, 'notes' => $stepName, 'last_updated' => now()]);
                            $this->createAndSendWarehouseLog($requisition, $materialUser, 102, $stepName);
                            $nextStepFound = true;
                        }
                    }
                    break;
                case 102: // Setelah Material Batch Print
                    $inwardUser = $this->findWarehouseUser('Inward WH Supervisor', 'WH0001');
                    if ($inwardUser) {
                        $stepName = 'Packaging - Inward Final Check';
                        Tracking::create(['requisition_id' => $requisition->id, 'current_position' => $inwardUser->name, 'notes' => $stepName, 'last_updated' => now()]);
                        $this->createAndSendWarehouseLog($requisition, $inwardUser, 103, $stepName);
                        $nextStepFound = true;
                    }
                    break;
            }
        }

        if (!$nextStepFound) {
            return $this->notifyRequesterAsCompleted($requisition);
        }

        return 'Processing';
    }

    private function createAndSendWarehouseLog(Requisition $requisition, User $recipient, int $level, string $stepName)
    {
        if (!$recipient) {
            Log::error("User untuk proses '{$stepName}' tidak ditemukan.");
            return;
        }

        $log = ApprovalLog::create([
            'requisition_id' => $requisition->id, 'approver_nik' => $recipient->nik,
            'status' => 'Pending', 'level' => $level, // Level > 100 menandakan ini proses WH
            'token' => Str::uuid()->toString(), 'notes' => "Waiting for: {$stepName}",
        ]);

        $requisition->update(['status' => 'Processing', 'route_to' => $recipient->name]);

        dispatch(new sendSample($requisition, $recipient, $log->token, [
            'mail_type' => 'warehouse_process', 'process_step' => $stepName
        ]));
    }

    private function notifyRequesterAsCompleted(Requisition $requisition)
    {
        $statusText = 'Completed';
        // [DIPERBAIKI] Catat status selesai ke tabel tracking
        Tracking::create([
            'requisition_id' => $requisition->id,
            'current_position' => 'System',
            'notes' => 'Process has been completed.',
            'last_updated' => now()
        ]);

        $requisition->update(['status' => $statusText, 'route_to' => 'Finished']);
        if ($requisition->requester?->email) {
            dispatch(new sendSample($requisition, $requisition->requester, null, [
                'mail_type' => 'completed_notification'
            ]));
        }
        Log::info("Requisition #{$requisition->id} selesai. Email notifikasi dikirim ke requester.");
        return $statusText;
    }

    private function findWarehouseUser(string $name, string $fallbackNik)
    {
        $user = User::where('name', 'like', '%' . $name . '%')->first();
        return $user ?: User::where('nik', $fallbackNik)->first();
    }

    public function showSuccessPage()
    {
        if (!session('title')) {
            return redirect('/');
        }
        return view('page.sample.response-success');
    }
}
