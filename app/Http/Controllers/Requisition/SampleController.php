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
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. Mulai membangun query
        $query = DB::table('requisitions')
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

        // 3. Tambahkan filter: Jika user BUKAN super-admin,
        //    maka hanya tampilkan data yang NIK requester-nya sama dengan NIK user yang login
        if (!$user->hasRole('super-admin')) {
            $query->where('requisitions.requester_nik', $user->nik);
        }

        // 4. Urutkan data berdasarkan ID secara descending (terbesar ke terkecil)
        //    Ini akan membuat data terbaru selalu di paling atas
        $query->orderBy('requisitions.id', 'desc');

        // Proses data yang sudah difilter dan diurutkan dengan DataTables
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
                $editBtn = '';
                $deleteBtn = '';
                $qaFillBtn = '';

                if ($row->status === 'Pending') {
                    $editBtn = '<button type="button" class="btn btn-sm btn-warning btn-edit-requisition" data-id="' . $row->id . '" title="Edit"><i class="fa-solid fa-pencil text-white"></i></button>';
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete-requisition" data-id="' . $row->id . '" title="Delete"><i class="fa-solid fa-trash-alt text-white"></i></button>';
                }

                if (
                    $user->department?->name === 'QM & HSE' &&
                    $row->sub_category === 'Special Order' &&
                    $row->status === 'Processing'
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
        $requisition = Requisition::with('requester')->findOrFail($id);
        $user = Auth::user();

        // Panggil validated() untuk mendapatkan data yang sudah lolos validasi dari FormRequest
        $validated = $request->validated();

        // Logika tetap sama, tetapi sekarang menggunakan $validated dari FormRequest
        if ($request->has('source')) {
            // Ini adalah blok untuk submission dari QA
            DB::beginTransaction();
            try {
                RequisitionSpecial::updateOrCreate(
                    ['requisition_id' => $requisition->id],
                    $validated // Gunakan $validated di sini
                );

                // Setelah QA submit, proses selesai.
                $this->notifyRequesterAsCompleted($requisition);
                Log::info("Requisition #{$requisition->id} diselesaikan oleh QM & HSE.");

                DB::commit();
                return response()->json(['success' => true, 'message' => 'QM & HSE form has been successfully submitted and the process is complete.']);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Gagal saat submit form QM & HSE: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'An error occurred while submitting the QM form.'], 500);
            }

        } else {
            // Ini adalah blok untuk edit biasa oleh requester
            DB::beginTransaction();
            try {
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

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Sample Requisition berhasil diubah.']);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Gagal mengubah sample requisition: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan cek log.'], 500);
            }
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
        // Tambahkan 'qa_form' sebagai action yang valid
        $validActions = ['approve', 'review', 'reject', 'submit', 'qa_form'];

        if (!in_array($action, $validActions)) {
            return view('page.sample.invalid', ['message' => 'Invalid action.']);
        }

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        $tracking = !$approvalLog ? Tracking::where('token', $token)->first() : null;

        if (!$approvalLog && !$tracking) {
            return view('page.sample.invalid', ['message' => 'This request is invalid or has been processed.']);
        }

        $requisition = $approvalLog ? $approvalLog->requisition : $tracking->requisition;
        // Load relasi yang dibutuhkan, terutama requisitionSpecial untuk form QA
        $requisition->load('requester', 'customer', 'requisitionItems.itemMaster', 'requisitionItems.itemDetail', 'approvalLogs.approver', 'requisitionSpecial');

        $isQaForm = ($action === 'qa_form');
        $isWarehouseProcess = (bool)$tracking;

        $pageTitle = 'Approval Action';
        if ($isQaForm) {
            $pageTitle = 'QA/QM Form Completion';
        } elseif ($isWarehouseProcess) {
            $pageTitle = $tracking->current_position ?? 'Warehouse Process';
        }

        // Untuk Quick Reject, kita arahkan ke halaman 'review' agar bisa isi notes
        if ($action === 'reject') {
            $action = 'review';
        }

        $viewData = [
            'token' => $token,
            'action' => $action,
            'requisition' => $requisition,
            'pageTitle' => $pageTitle,
            'isQaForm' => $isQaForm,
            'isWarehouseProcess' => $isWarehouseProcess, // Tambahkan baris ini
        ];

        return view('page.sample.response-form', $viewData);
    }

    public function processApproval(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'action' => 'required|string|in:approve,review,reject,submit,qa_submit',
            'notes' => 'nullable|string|max:500|required_if:action,review,reject',
            'source' => 'required_if:action,qa_submit|string|max:255',
            'description' => 'required_if:action,qa_submit|string|max:255',
            'production_date' => 'required_if:action,qa_submit|date',
            'preparation_method' => 'required_if:action,qa_submit|string|max:255',
            'sample_notes' => 'required_if:action,qa_submit|string|max:255',
        ]);

        $token = $validated['token'];
        $action = $validated['action'];
        $notes = $validated['notes'] ?? null;

        if ($action === 'qa_submit') {
            $tracking = Tracking::where('token', $token)->firstOrFail();
            DB::beginTransaction();
            try {
                $requisition = $tracking->requisition;
                RequisitionSpecial::updateOrCreate(
                    ['requisition_id' => $requisition->id],
                    [ // Simpan semua data dari form QA ke database
                        'source' => $validated['source'],
                        'description' => $validated['description'],
                        'production_date' => $validated['production_date'],
                        'preparation_method' => $validated['preparation_method'],
                        'sample_notes' => $validated['sample_notes'],
                    ]
                );

                $this->notifyRequesterAsCompleted($requisition);
                Log::info("Requisition #{$requisition->id} diselesaikan oleh QA via form email.");

                DB::commit();
                return redirect()->route('approval.success')
                    ->with('card_class', 'success')
                    ->with('title', 'Form Submitted Successfully')
                    ->with('message', 'The QA form has been completed and the process is finished.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Gagal saat submit form QA: ' . $e->getMessage() . ' di baris ' . $e->getLine());
                return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
            }
        }

        // --- Langkah 1: Cek token di tabel approval_logs ---
        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if ($approvalLog) {
            DB::beginTransaction();
            try {
                $requisition = $approvalLog->requisition->load('customer', 'requester');
                $approverName = $approvalLog->approver->name ?? 'Unknown Approver';
                $finalNotes = in_array($action, ['review', 'reject']) ? $notes : ('Processed by ' . $approverName);

                $approvalLog->update([
                    'status' => ($action === 'reject') ? 'Rejected' : 'Approved',
                    'notes' => $finalNotes,
                    'responded_at' => now(),
                ]);

                $newStatus = 'In Progress';

                if ($action === 'reject') {
                    $newStatus = 'Rejected';
                    $requisition->update(['status' => 'Rejected', 'route_to' => 'Finished (Rejected)']);
                    if ($requisition->requester?->email) {
                        Mail::to($requisition->requester->email)->send(new MailRejectSample($requisition, $approverName, $finalNotes));
                    }
                } else {
                    $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                                    ->where('level', '>', $approvalLog->level)
                                                    ->where('level', '<=', 100)
                                                    ->orderBy('level', 'asc')
                                                    ->first();

                    if ($nextApprovalLog) {
                        $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprovalLog->approver->name]);
                        dispatch(new sendSample($requisition, $nextApprovalLog->approver, $nextApprovalLog->token, ['mail_type' => 'approval']));
                    } else {
                        $requisition->update(['status' => 'Approved']);
                        $newStatus = $this->startPostApprovalProcess($requisition);
                    }
                }

                DB::commit();

                return redirect()->route('approval.success')
                    ->with('card_class', $action === 'reject' ? 'reject' : 'success')
                    ->with('title', 'Action Submitted')->with('message', 'Your response has been successfully recorded.')
                    ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                    ->with('action_text', ucfirst($action))->with('approver_name', $approverName)
                    ->with('new_status', $newStatus);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal memproses approval: " . $e->getMessage());
                return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
            }
        }

        // --- Langkah 2: Jika tidak ada, cek token di tabel trackings ---
        $tracking = Tracking::where('token', $token)->first();

        if ($tracking) {
            DB::beginTransaction();
            try {
                $requisition = $tracking->requisition; // Ambil requisition terkait
                Log::info("Processing warehouse step for Requisition #{$requisition->id}. Current position: {$tracking->current_position}.");

                // [PERBAIKAN] Gabungkan query update token & simpan notes jika ada
                // Ini juga memperbaiki bug di mana notes dari warehouse tidak tersimpan
                $updateData = ['token' => null];
                if ($notes) {
                    // Tambahkan notes baru ke notes yang sudah ada (jika ada)
                    $existingNotes = $tracking->notes ? $tracking->notes . "\n" : '';
                    $updateData['notes'] = $existingNotes . "- " . $notes;
                }
                $tracking->update($updateData);

                // Lanjutkan alur warehouse seperti biasa
                $newStatus = $this->advanceWarehouseStep($requisition);
                Log::info("Warehouse step advanced for Requisition #{$requisition->id}. New status/route: {$newStatus}.");

                DB::commit();

                // Redirect ke halaman sukses (tidak berubah)
                return redirect()->route('approval.success')
                    ->with('card_class', 'success')->with('title', 'Action Submitted')
                    ->with('message', 'Warehouse process step has been recorded.')
                    ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                    ->with('action_text', 'Processed')->with('approver_name', 'Warehouse Team')
                    ->with('new_status', $newStatus);

            } catch (\Exception $e) {
                DB::rollBack();
                // [DISEMPURNAKAN] Logging error lebih detail
                Log::error("Gagal melanjutkan proses warehouse: " . $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getFile());
                return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred. Please check the system logs.');
            }
        }

        // --- Langkah 3: Jika token tidak ditemukan di mana pun ---
        return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'Invalid Request')->withMessage('This approval request is invalid or has already been processed.');
    }

    private function startPostApprovalProcess(Requisition $requisition)
    {
        $newStatus = 'Processing';
        Log::info("Approval path selesai untuk Requisition #{$requisition->id}. Memulai proses warehouse/QA.");

        switch ($requisition->sub_category) {
            case 'Packaging':
                $newStatus = $this->startPackagingProcess($requisition);
                break;
            case 'Finished Goods':
                $newStatus = $this->startFinishedGoodsProcess($requisition);
                break;
            case 'Special Order':
                $newStatus = $this->startSpecialOrderProcess($requisition);
                break;
            default:
                Log::warning("Tidak ada alur proses untuk sub-category: {$requisition->sub_category}");
                $newStatus = $this->notifyRequesterAsCompleted($requisition);
                break;
        }
        return $newStatus;
    }

    private function startPackagingProcess(Requisition $requisition)
    {
        $stepName = 'Inward WH Supervisor (Initial Check)';
        $this->createOrUpdateTracking($requisition, $stepName, "Menunggu pengecekan awal.");
        return $stepName;
    }

    private function startFinishedGoodsProcess(Requisition $requisition)
    {
        $stepName = 'Outward WH Supervisor';
        $this->createOrUpdateTracking($requisition, $stepName, "Menunggu proses oleh outward.");
        return $stepName;
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

    private function startSpecialOrderProcess(Requisition $requisition)
    {
        $headQaUser = User::whereHas('department', fn ($q) => $q->where('name', 'QM & HSE'))
                    ->whereHas('roles', fn ($q) => $q->where('name', 'head-QA'))
                    ->first();

        if ($headQaUser) {
            $stepName = 'Waiting for QA/QM Form';

            // 1. Buat satu token yang akan digunakan
            $qa_token = Str::uuid()->toString();

            // 2. Simpan token tersebut ke database tracking
            Tracking::updateOrCreate(
                ['requisition_id' => $requisition->id],
                [
                    'current_position' => $stepName,
                    'notes'            => "Waiting for form to be filled by {$headQaUser->name}",
                    'last_updated'     => now(),
                    'token'            => $qa_token,
                ]
            );

            // 3. Update status utama requisition
            $requisition->update(['status' => 'Processing', 'route_to' => $stepName]);

            // 4. Buat URL email dengan token yang sama
            $formUrl = route('approval.response', ['token' => $qa_token, 'action' => 'qa_form']);

            // 5. Kirim email notifikasi ke QA
            dispatch(new sendSample($requisition, $headQaUser, null, [
                'mail_type' => 'qa_form_notification',
                'form_url' => $formUrl,
            ]))->delay(now()->addSeconds(5));;

            Log::info("Notifikasi form Special Order dikirim ke Head QA: {$headQaUser->name}.");
            return $stepName;
        } else {
            Log::warning("Head of Department QA/QM tidak ditemukan.");
            return $this->notifyRequesterAsCompleted($requisition);
        }
    }

    private function advanceWarehouseStep(Requisition $requisition)
    {
        // Muat ulang relasi tracking untuk mendapatkan data paling baru
        $requisition->load('tracking');
        $currentPosition = $requisition->tracking->current_position ?? '';

        // --- ALUR UNTUK SUB-CATEGORY: PACKAGING ---
        if ($requisition->sub_category === 'Packaging') {

            // Kasus JIKA 'Print Batch' DIPILIH (Alur 3 langkah)
            if ($requisition->print_batch) {
                // Jika langkah saat ini adalah Initial Check, langkah berikutnya adalah Material Support
                if (str_contains($currentPosition, 'Initial Check')) {
                    $stepName = 'Material Support Supervisor';
                    $this->createOrUpdateTracking($requisition, $stepName, "Menunggu proses cetak batch.");
                    return $stepName; // Mengembalikan status baru
                }
                // Jika langkah saat ini adalah Material Support, langkah berikutnya adalah Final Check
                if (str_contains($currentPosition, 'Material Support')) {
                    $stepName = 'Inward WH Supervisor (Final Check)';
                    $this->createOrUpdateTracking($requisition, $stepName, "Menunggu pengecekan akhir.");
                    return $stepName; // Mengembalikan status baru
                }
                // Jika langkah saat ini adalah Final Check (atau kondisi lain), maka proses selesai.
                return $this->notifyRequesterAsCompleted($requisition);
            }
            // Kasus JIKA 'Print Batch' TIDAK DIPILIH (Alur 1 langkah)
            else {
                // Setelah pengecekan Inward pertama (dan satu-satunya), proses langsung selesai.
                return $this->notifyRequesterAsCompleted($requisition);
            }
        }

        // --- ALUR UNTUK SUB-CATEGORY: FINISHED GOODS ---
        if ($requisition->sub_category === 'Finished Goods') {
            // Setelah proses Outward, langsung selesai.
            return $this->notifyRequesterAsCompleted($requisition);
        }

        // Fallback jika tidak ada alur yang cocok (seharusnya tidak terjadi)
        Log::warning("advanceWarehouseStep dipanggil untuk requisition #{$requisition->id} tanpa alur yang cocok.");
        return $this->notifyRequesterAsCompleted($requisition);
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
        // Update status utama requisition
        $requisition->update(['status' => $statusText, 'route_to' => 'Finished']);

        // Update tracking ke status final (tanpa membuat token baru)
        Tracking::updateOrCreate(
            ['requisition_id' => $requisition->id],
            [
                'current_position' => $statusText,
                'notes'            => "Sample process is complete and ready for the requester.",
                'last_updated'     => now(),
                'token'            => null, // Pastikan token kosong saat selesai
            ]
        );

        if ($requisition->requester?->email) {
            dispatch(new sendSample($requisition, $requisition->requester, null, [
                'mail_type' => 'completed_notification'
            ]));
        }
        Log::info("Requisition #{$requisition->id} selesai. Notifikasi dikirim ke requester.");
        return $statusText;
    }

    private function createOrUpdateTracking(Requisition $requisition, string $currentPosition, string $notes)
    {
        $token = Str::uuid()->toString(); // Buat token baru

        $tracking = Tracking::updateOrCreate(
            ['requisition_id' => $requisition->id],
            [
                'current_position' => $currentPosition,
                'notes'            => $notes,
                'last_updated'     => now(),
                'token'            => $token, // Simpan token ke database
            ]
        );

        $requisition->update(['status' => 'Processing', 'route_to' => $currentPosition]);

        // Kirim notifikasi ke user yang relevan dengan menyertakan token
        $user = $this->findUserForStep($currentPosition);
        if ($user) {
            dispatch(new sendSample($requisition, $user, $token, [
                'mail_type'    => 'warehouse_process',
                'process_step' => $currentPosition
            ]));
        }
    }

    private function findUserForStep(string $stepName)
    {
        if (str_contains($stepName, 'Inward')) {
            return $this->findWarehouseUser('Inward WH Supervisor', 'WH0001');
        }
        if (str_contains($stepName, 'Material')) {
            return $this->findWarehouseUser('Material Support Supervisor', 'MS0001');
        }
        if (str_contains($stepName, 'Outward')) {
            return $this->findWarehouseUser('Outward WH Supervisor', 'WH0002');
        }
        return null;
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
