<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSampleRequisitionRequest;
use App\Http\Requests\UpdateSampleRequisitionRequest;
use App\Jobs\sendSample;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ApprovalTrait;

class SampleController extends Controller
{
    use ApprovalTrait;

    //======================================================================
    // PUBLIC FUNCTIONS (Controller Endpoints & AJAX Handlers)
    //======================================================================

    /**
     * Menampilkan halaman utama Sample Requisition.
     */
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

    /**
     * Menyediakan data untuk DataTables.
     */
    public function getData()
    {
        $user = Auth::user();
        $query = DB::table('requisitions')
            ->leftJoin('users', 'requisitions.requester_nik', '=', 'users.nik')
            ->leftJoin('customers', 'requisitions.customer_id', '=', 'customers.id')
            ->where('requisitions.category', 'SAMPLE')
            ->select(
                'requisitions.id', 'requisitions.requester_nik', 'requisitions.request_date',
                'requisitions.sub_category', 'requisitions.route_to', 'requisitions.status',
                'users.name as requester_name', 'users.avatar', 'customers.name as customer_name'
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
            ->editColumn('sub_category', function ($requisition) {
                $subCategory = $requisition->sub_category;
                $badgeClass = 'bg-secondary';
                if ($subCategory == 'Packaging') $badgeClass = 'bg-info';
                elseif ($subCategory == 'Finished Goods') $badgeClass = 'bg-primary';
                elseif ($subCategory == 'Special Order') $badgeClass = 'bg-secondary';
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
                $cancelBtn = '';
                $printBtn = '<a href="' . route('sample.report', $row->id) . '" target="_blank" class="btn btn-sm btn-secondary" title="Print Report"><i class="ph-bold ph-printer text-white"></i></a>';

                if ($row->status === 'Pending') {
                    $cancelBtn = '<button type="button" class="btn btn-sm btn-danger btn-cancel-requisition" data-id="' . $row->id . '" title="Cancel Requisition"><i class="ph-bold ph-x-circle text-white"></i></button>';
                }

                $qaFillBtn = '';
                if (
                    $user->department?->name === 'QM & HSE' &&
                    $row->sub_category === 'Special Order' &&
                    $row->status === 'Processing' &&
                    $row->route_to === 'Waiting for QA/QM Form'
                ) {
                    $qaFillBtn = '<button type="button" class="btn btn-sm btn-success btn-qa-form" data-id="' . $row->id . '" title="Complete QA Form"><i class="fa-solid fa-check-double text-white"></i></button>';
                }

                return "<div class='d-flex gap-1'>{$viewBtn} {$qaFillBtn} {$cancelBtn} {$printBtn}</div>";
            })
            ->rawColumns(['requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }

    /**
     * Membuat requisition baru.
     */
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

            $this->generateApprovalLogs($user, $requisition->id, 'SAMPLE', $validated['sub_category']);
            $firstLog = ApprovalLog::where('requisition_id', $requisition->id)->orderBy('level', 'asc')->first();

            if ($firstLog && $firstApprover = User::where('nik', $firstLog->approver_nik)->first()) {
                $requisition->update(['route_to' => $firstApprover->name]);
                sendSample::dispatch($requisition, $firstApprover, $firstLog->token)->delay(now()->addSeconds(3));
            } else {
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada alur approval. Auto-complete Requisition ID {$requisition->id}.");
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Sample Requisition berhasil dibuat.',
                'next_srs_number' => $this->generateSrsNumber()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat sample requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Mengupdate requisition yang ada, baik untuk edit biasa maupun submit form QA.
     */
    public function update(UpdateSampleRequisitionRequest $request, $id)
    {
        $requisition = Requisition::findOrFail($id);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Jika ada 'source', berarti ini adalah submit dari form QA/QM
            if (isset($validated['source'])) {
                RequisitionSpecial::updateOrCreate(['requisition_id' => $requisition->id], $validated);
                $this->notifyRequesterAsCompleted($requisition);
                $message = 'QM & HSE form has been successfully submitted.';
            } else { // Jika tidak, ini adalah edit biasa oleh requester
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
                        array_merge($validated, ['products' => $productNames])
                    );
                } else {
                    $requisition->requisitionSpecial()->delete();
                }
                $message = 'Sample Requisition berhasil diubah.';
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update requisition #{$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Membatalkan requisition.
     */
    public function cancelRequisition($id)
    {
        DB::beginTransaction();
        try {
            $requisition = Requisition::findOrFail($id);

            if ($requisition->status !== 'Pending') {
                return response()->json(['success' => false, 'message' => 'Cannot cancel. This requisition is already in progress.'], 403);
            }

            // [PERBAIKAN] Cari approver yang sedang menunggu SEBELUM token dihapus
            $pendingLog = ApprovalLog::where('requisition_id', $id)
                                     ->where('status', 'Pending')
                                     ->with('approver') // Muat relasi approver
                                     ->first();

            // Lanjutkan proses pembatalan
            $requisition->update(['status' => 'Cancelled', 'route_to' => 'Finished (Cancelled)']);
            ApprovalLog::where('requisition_id', $id)->where('status', 'Pending')->update(['token' => null]);

            // [PERBAIKAN] Kirim email ke approver yang tadinya menunggu
            if ($pendingLog && $pendingLog->approver) {
                dispatch(new sendSample(
                    $requisition,
                    $pendingLog->approver, // Target email adalah approver
                    null,
                    ['mail_type' => 'cancellation_notification'] // Hanya kirim tipe email
                ));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Requisition has been successfully cancelled.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal membatalkan Requisition #{$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'A system error occurred.'], 500);
        }
    }

    //======================================================================
    // EMAIL RESPONSE HANDLING
    //======================================================================

    /**
     * Menampilkan form respons dari link email (approval, review, QA form, etc.).
     */
    public function showResponseForm(Request $request, $token)
    {
        $action = $request->query('action');
        $originalAction = $action;

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        $tracking = !$approvalLog ? Tracking::where('token', $token)->whereNull('last_updated')->first() : null;

        if (!$approvalLog && !$tracking) {
            return view('page.sample.invalid', ['message' => 'This request is invalid or has been processed.']);
        }

        $requisition = ($approvalLog) ? $approvalLog->requisition : $tracking->requisition;
        $requisition->load('requester.department', 'customer', 'requisitionItems.itemMaster', 'requisitionItems.itemDetail', 'requisitionSpecial');

        $isQaForm = ($action === 'qa_form');
        $isWarehouseProcess = (bool)$tracking;

        $pageTitle = 'Approval Action';
        if ($isQaForm) $pageTitle = 'QA/QM Form Completion';
        elseif ($isWarehouseProcess) $pageTitle = $tracking->current_position ?? 'Warehouse Process';

        // Arahkan 'quick reject' ke halaman 'review' untuk mengisi notes
        if ($action === 'reject') {
            $action = 'review';
        }

        return view('page.sample.response-form', compact('token', 'action', 'originalAction', 'requisition', 'pageTitle', 'isQaForm', 'isWarehouseProcess'));
    }

    /**
     * Memproses semua jenis aksi dari form respons email.
     */
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

        if ($validated['action'] === 'qa_submit') {
            $tracking = Tracking::where('token', $validated['token'])->firstOrFail();
            return $this->processQaFormSubmit($tracking, $validated);
        }

        $approvalLog = ApprovalLog::where('token', $validated['token'])->where('status', 'Pending')->first();
        if ($approvalLog) {
            return $this->processApprovalStep($approvalLog, $validated['action'], $validated['notes'] ?? null);
        }

        $tracking = Tracking::where('token', $validated['token'])->whereNull('last_updated')->first();
        if ($tracking) {
            return $this->processWarehouseStep($tracking, $validated['notes'] ?? null);
        }

        return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'Invalid Request');
    }

    //======================================================================
    // PRIVATE FUNCTIONS (Business Logic & Helpers)
    //======================================================================

    /**
     * Menangani submit form QA/QM.
     */
    private function processQaFormSubmit(Tracking $tracking, array $validated)
    {
        DB::beginTransaction();
        try {
            RequisitionSpecial::updateOrCreate(
                ['requisition_id' => $tracking->requisition_id],
                $validated
            );
            $tracking->update(['token' => null, 'last_updated' => now(), 'notes' => 'Form has been completed by QA.']);
            $this->notifyRequesterAsCompleted($tracking->requisition);
            DB::commit();

            // Di dalam fungsi processQaFormSubmit()
            $requisition = $tracking->requisition->load('customer'); // Ambil requisition & load customer

            return redirect()->route('approval.success')->with([
                'card_class'    => 'success',
                'title'         => 'Form Submitted Successfully',
                'no_srs'        => $requisition->no_srs, // <-- TAMBAHKAN INI
                'customer_name' => $requisition->customer->name ?? 'N/A' // <-- DAN INI
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal saat submit form QM & HSE: ' . $e->getMessage());
            return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
        }
    }

    /**
     * Memproses satu langkah persetujuan (approve/reject).
     */
    private function processApprovalStep(ApprovalLog $approvalLog, string $action, ?string $notes)
    {
        DB::beginTransaction();
        try {
            $requisition = $approvalLog->requisition;
            $approverName = $approvalLog->approver->name ?? 'Approver';
            $finalNotes = $notes;
            if (empty($notes)) {
                $finalNotes = ($action === 'reject') ? 'Rejected without reason' : 'Approved by ' . $approverName;
            }

            $approvalLog->update([
                'status'       => ($action === 'reject') ? 'Rejected' : 'Approved',
                'notes'        => $finalNotes,
                'approved_at' => now(), // <-- TAMBAHKAN BARIS INI
                'token'        => null,
            ]);

            if ($action === 'reject') {
                $requisition->update(['status' => 'Rejected', 'route_to' => 'Finished (Rejected)']);
                if ($requisition->requester) {
                    dispatch(new sendSample($requisition, $requisition->requester, null, [
                        'mail_type' => 'rejection_notification',
                        'approver_name' => $approverName,
                        'rejection_notes' => $notes
                    ]))->delay(now()->addSeconds(3));;
                }
                $newStatus = 'Rejected';
            } else {
                $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                                ->where('level', '>', $approvalLog->level)
                                                ->orderBy('level', 'asc')->first();
                if ($nextApprovalLog && $nextApprovalLog->approver) {
                    $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprovalLog->approver->name]);
                    dispatch(new sendSample($requisition, $nextApprovalLog->approver, $nextApprovalLog->token))->delay(now()->addSeconds(3));
                    $newStatus = 'In Progress';
                } else {
                    $requisition->update(['status' => 'Approved']);
                    $newStatus = $this->handlePostApprovalFlow($requisition);
                }
            }
            DB::commit();
            // Di dalam fungsi processApprovalStep()
            $requisition->load('customer'); // Pastikan data customer sudah ter-load

            return redirect()->route('approval.success')->with([
                'card_class'    => $action === 'reject' ? 'reject' : 'success',
                'title'         => 'Action Submitted',
                'new_status'    => $newStatus,
                'no_srs'        => $requisition->no_srs, // <-- TAMBAHKAN INI
                'customer_name' => $requisition->customer->name ?? 'N/A' // <-- DAN INI
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal proses approval #{$approvalLog->id}: " . $e->getMessage());
            return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
        }
    }

    /**
     * Memproses satu langkah di gudang (warehouse).
     */
    private function processWarehouseStep(Tracking $tracking, ?string $notes)
    {
        DB::beginTransaction();
        try {
            $tracking->update([
                'token'        => null,
                'last_updated' => now(),
                'notes'        => $notes ?: 'Proses berhasil disubmit.',
            ]);

            $newStatus = $this->advanceWarehouseStep($tracking->requisition);
            DB::commit();

            // Di dalam fungsi processWarehouseStep()
            $requisition = $tracking->requisition->load('customer'); // Ambil requisition & load customer

            return redirect()->route('approval.success')->with([
                'card_class'    => 'success',
                'title'         => 'Action Submitted',
                'new_status'    => $newStatus,
                'no_srs'        => $requisition->no_srs, // <-- TAMBAHKAN INI
                'customer_name' => $requisition->customer->name ?? 'N/A' // <-- DAN INI
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal melanjutkan proses warehouse: " . $e->getMessage());
            return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
        }
    }

    /**
     * Menangani alur kerja SETELAH semua approval manajerial selesai.
     */
    private function handlePostApprovalFlow(Requisition $requisition)
    {
        Log::info("Approval path selesai untuk Requisition #{$requisition->id}. Memulai alur proses.");

        switch ($requisition->sub_category) {
            case 'Packaging':
            case 'Finished Goods':
                $steps = [];
                if ($requisition->sub_category === 'Packaging') {
                    $steps = $requisition->print_batch
                        ? ['Inward WH Supervisor (Initial Check)', 'Material Support Supervisor', 'Inward WH Supervisor (Final Check)']
                        : ['Inward WH Supervisor (Final Check)'];
                } elseif ($requisition->sub_category === 'Finished Goods') {
                    $steps = ['Outward WH Supervisor'];
                }

                if (empty($steps)) return $this->notifyRequesterAsCompleted($requisition);

                foreach ($steps as $stepName) {
                    Tracking::create([
                        'requisition_id'   => $requisition->id,
                        'current_position' => $stepName,
                        'token'            => Str::uuid()->toString(),
                    ]);
                }
                return $this->advanceWarehouseStep($requisition); // Langsung panggil untuk memulai langkah pertama

            case 'Special Order':
                $headQaUser = User::whereHas('department', fn ($q) => $q->where('name', 'QM & HSE'))
                            ->whereHas('roles', fn ($q) => $q->where('name', 'head-QA'))
                            ->first();

                if ($headQaUser) {
                    $stepName = 'Waiting for QA/QM Form';
                    $token = Str::uuid()->toString();

                    Tracking::create([
                        'requisition_id'   => $requisition->id,
                        'current_position' => $stepName,
                        'notes'            => "Waiting for form to be filled by {$headQaUser->name}",
                        'token'            => $token,
                    ]);

                    $requisition->update(['status' => 'Processing', 'route_to' => $stepName]);

                    // [PERBAIKAN] Buat URL form dan kirimkan ke email job
                    $formUrl = route('approval.response', ['token' => $token, 'action' => 'qa_form']);

                    dispatch(new sendSample($requisition, $headQaUser, $token, [
                        'mail_type' => 'qa_form_notification',
                        'form_url'  => $formUrl // <-- VARIABEL DITAMBAHKAN DI SINI
                    ]))->delay(now()->addSeconds(3));

                    return $stepName;
                }
                Log::warning("Head of Department QA/QM tidak ditemukan untuk Requisition #{$requisition->id}.");
                // Fallthrough untuk auto-complete jika user QA tidak ditemukan

            default:
                Log::warning("Tidak ada alur proses untuk sub-category: {$requisition->sub_category}. Menyelesaikan requisition.");
                return $this->notifyRequesterAsCompleted($requisition);
        }
    }

    /**
     * Memajukan proses ke langkah gudang berikutnya atau menyelesaikan jika sudah selesai.
     */
    private function advanceWarehouseStep(Requisition $requisition)
    {
        $nextStep = Tracking::where('requisition_id', $requisition->id)
                            ->whereNull('last_updated')
                            ->orderBy('id', 'asc')
                            ->first();

        if ($nextStep) {
            $userForNextStep = $this->findUserForStep($nextStep->current_position);
            if ($userForNextStep) {
                dispatch(new sendSample($requisition, $userForNextStep, $nextStep->token, [
                    'mail_type'    => 'warehouse_process',
                    'process_step' => $nextStep->current_position,
                ]))->delay(now()->addSeconds(3));

                $requisition->update(['status' => 'Processing', 'route_to' => $nextStep->current_position]);
                return $nextStep->current_position;
            }
            Log::error("User tidak ditemukan untuk langkah: {$nextStep->current_position} di Requisition #{$requisition->id}.");
        }

        return $this->notifyRequesterAsCompleted($requisition);
    }

    /**
     * Mengubah status menjadi 'Completed' dan mengirim notifikasi ke requester.
     */
    private function notifyRequesterAsCompleted(Requisition $requisition)
    {
        $requisition->load('requester');
        $requisition->update(['status' => 'Completed', 'route_to' => 'Finished']);

        // Hapus sisa token yang mungkin masih aktif
        Tracking::where('requisition_id', $requisition->id)->whereNotNull('token')->update(['token' => null]);

        if ($requisition->requester) {
            dispatch(new sendSample($requisition, $requisition->requester, null, ['mail_type' => 'completed_notification']))->delay(now()->addSeconds(3));
        }
        Log::info("Requisition #{$requisition->id} selesai. Notifikasi dikirim ke requester.");
        return 'Completed';
    }

    /**
     * Helper untuk membuat nomor SRS baru.
     */
    private function generateSrsNumber()
    {
        $prefix = 'S';
        $year = date('y');
        $month = date('m');
        $currentPrefix = "$prefix $year $month";
        $lastRequisition = Requisition::where('no_srs', 'LIKE', $currentPrefix . ' %')->orderBy('no_srs', 'desc')->first();
        $runningNumber = $lastRequisition ? (int)substr($lastRequisition->no_srs, -3) + 1 : 1;
        return $currentPrefix . ' ' . sprintf('%03d', $runningNumber);
    }

    /**
     * Helper untuk mencari user berdasarkan nama step proses.
     */
    private function findUserForStep(string $stepName)
    {
        if (str_contains($stepName, 'Inward'))    return $this->findWarehouseUser('Inward WH Supervisor', 'WH0001');
        if (str_contains($stepName, 'Material'))  return $this->findWarehouseUser('Material Support Supervisor', 'MS0001');
        if (str_contains($stepName, 'Outward'))   return $this->findWarehouseUser('Outward WH Supervisor', 'WH0002');
        return null;
    }

    /**
     * Helper untuk mencari user gudang berdasarkan nama atau NIK fallback.
     */
    private function findWarehouseUser(string $name, string $fallbackNik)
    {
        return User::where('name', 'like', '%' . $name . '%')->first() ?? User::where('nik', $fallbackNik)->first();
    }

    //======================================================================
    // AJAX FUNCTIONS FOR FORM
    //======================================================================

    public function getAllItemMasters()
    {
        return response()->json(ItemMaster::select('id', 'item_master_code', 'item_master_name', 'unit')->get());
    }

    public function getItemDetailsByProducts(Request $request)
    {
        $request->validate(['product_ids' => 'required|array']);
        return response()->json(ItemDetail::whereIn('item_master_id', $request->product_ids)->get());
    }

    public function show($id)
    {
        $requisition = Requisition::with([
            'customer:id,name,address',
            'requester:nik,name,email',
            'requisitionItems:requisition_id,item_master_id,item_detail_id,material_type,quantity_required,quantity_issued',
            'requisitionItems.itemMaster:id,item_master_code,item_master_name,unit',
            'requisitionItems.itemDetail:id,item_detail_code,item_detail_name,unit',
            'requisitionSpecial',
            'approvalLogs:id,requisition_id,approver_nik,status,notes,updated_at,level',
            'approvalLogs.approver:nik,name',
            'trackings'
        ])->findOrFail($id);
        return response()->json($requisition);
    }

    public function edit($id)
    {
        $requisition = Requisition::with([
            'requisitionItems.itemMaster',
            'requisitionItems.itemDetail',
            'requisitionSpecial'
        ])->findOrFail($id);

        $responseData = $requisition->toArray();
        $responseData['selected_master_ids'] = $requisition->requisitionItems->pluck('item_master_id')->unique()->values()->all();
        $responseData['attached_material_types'] = $requisition->requisitionItems->pluck('material_type')->unique()->values()->all();
        $responseData['product_options'] = ItemMaster::select('id', 'item_master_code', 'item_master_name')
            ->get()
            ->map(fn($item) => ['id' => $item->id, 'text' => "[{$item->item_master_code}] {$item->item_master_name}"])
            ->toArray();

        return response()->json($responseData);
    }

    //======================================================================
    // OTHER PUBLIC FUNCTIONS
    //======================================================================

    public function printReport($id)
    {
        $requisition = Requisition::with([
            'customer',
            'requester.department',
            'requisitionItems.itemMaster',
            'requisitionItems.itemDetail',
            'requisitionSpecial',
            // Ambil semua approval logs, tidak hanya yang 'Approved'
            'approvalLogs' => fn($q) => $q->orderBy('level', 'asc'),
            'approvalLogs.approver.roles'
        ])->findOrFail($id);

        // Siapkan data approver untuk view
        $approvals = $requisition->approvalLogs->map(function ($log) {
            $statusText = 'NOT REVIEWED';
            if ($log->status === 'Approved' && !empty($log->notes) && $log->notes !== 'Approved by ' . ($log->approver->name ?? '')) {
            $statusText = 'APPROVED WITH REVIEW';
            } elseif ($log->status === 'Approved') {
            $statusText = 'APPROVED NOT REVIEW';
            } elseif ($log->status === 'Rejected') {
            $statusText = 'NOT APPROVED';
            }

            // Ambil role pertama (atau gabungkan jika multi-role)
            $roleNames = $log->approver?->roles->pluck('name')->toArray() ?? [];
            $roleDisplay = !empty($roleNames) ? implode(', ', $roleNames) : 'N/A';

            return (object) [
            'name' => $log->approver->name ?? 'N/A',
            'position' => $roleDisplay,
            'status' => $statusText,
            'approved_at' => $log->approved_at,
            'notes' => $log->notes,
            ];
        });

        // Kirim semua data yang dibutuhkan ke view
        $data = [
            'requisition' => $requisition,
            'requester' => $requisition->requester,
            'approvals' => $approvals, // <-- VARIABEL APPROVALS DITAMBAHKAN DI SINI
            // Variabel approver lama untuk tanda tangan (jika masih diperlukan)
            'firstApprover' => $requisition->approvalLogs->first()->approver ?? null,
            'lastApprover' => $requisition->approvalLogs->last()->approver ?? null,
        ];

        $pdf = Pdf::loadView('page.sample.report', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('RS Sample - ' . $requisition->no_srs . '.pdf');
    }

    public function showSuccessPage()
    {
        return session('title') ? view('page.sample.response-success') : redirect('/');
    }
}
