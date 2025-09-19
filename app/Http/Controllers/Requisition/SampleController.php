<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSampleRequisitionRequest;
use App\Http\Requests\UpdateSampleRequisitionRequest;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Master\ItemDetail;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\Requisition\RequisitionSpecial;
use App\Jobs\SendRequisitionEmailJob; // Import Job yang baru dibuat
use App\Models\Requisition\ApprovalPath; // Import model ApprovalPath
use App\Models\User;
use App\Models\Requisition\ApprovalLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SampleController extends Controller
{
    // ... (fungsi-fungsi lainnya tidak berubah: generateSrsNumber, getProductsByMaterialTypes, dll) ...
    private function generateSrsNumber()
    {
        $prefix = 'S';
        $year = date('y');
        $month = date('m');
        $currentPrefix = "$prefix $year $month"; // Contoh: "S 25 09"

        // CARI NOMOR TERAKHIR BERDASARKAN KOLOM no_srs, BUKAN created_at
        $lastRequisition = Requisition::where('no_srs', 'LIKE', $currentPrefix . ' %')
                                    ->orderBy('no_srs', 'desc') // Urutkan berdasarkan no_srs itu sendiri
                                    ->first();

        $runningNumber = 1; // Default nomor urut adalah 1
        if ($lastRequisition) {
            $lastParts = explode(' ', $lastRequisition->no_srs);
            $lastRunningNumber = end($lastParts); // Ambil bagian nomor urut (e.g., "002")
            $runningNumber = intval($lastRunningNumber) + 1; // Tambah 1
        }

        // Gabungkan kembali dengan format 3 digit
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
        $userAccount = Auth::user()->department->code ?? null;
        $userDepartmentName = Auth::user()->department?->name ?? null;
        $allowedSubCategories = [];
        if (in_array($userAccount, ['5300', '5302'])) {
            $allowedSubCategories[] = 'Packaging';
        }
        if (in_array($userAccount, ['5300', '5302', '5303'])) {
            $allowedSubCategories[] = 'Finished Good';
        }
        if ($userAccount == '5300') {
            $allowedSubCategories[] = 'Special Order';
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
            ->addColumn('requester_info', function ($requisition) { // HANYA GUNAKAN addColumn
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
                return '
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-info btn-view-requisition" data-id="' . $row->id . '" title="Lihat Detail">
                            <i class="fa-solid fa-eye text-white"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning btn-edit-requisition" data-id="' . $row->id . '" title="Edit">
                            <i class="fa-solid fa-pencil text-white"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete-requisition" data-id="' . $row->id . '" title="Delete">
                            <i class="fa-solid fa-trash-alt text-white"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $requisition = Requisition::with([
            'customer',
            'requester',
            'requisitionItems.itemMaster',
            'requisitionItems.itemDetail',
            'requisitionSpecial'
        ])->findOrFail($id);

        $trackingHistory = [];

        $trackingHistory[] = [
            'status' => 'Permintaan Dibuat',
            'user' => $requisition->requester->name ?? 'N/A',
            'date' => $requisition->created_at->format('d M Y, H:i'),
            'notes' => 'Formulir Sample Requisition berhasil dibuat dan disimpan sebagai draf.',
            'icon' => 'fa-solid fa-file-circle-plus',
            'color' => 'bg-secondary'
        ];

        if ($requisition->status != 'Draft') {
            $trackingHistory[] = [
                'status' => 'Dikirim ke ' . $requisition->route_to,
                'user' => $requisition->requester->name ?? 'N/A',
                'date' => $requisition->updated_at->format('d M Y, H:i'),
                'notes' => 'Menunggu persetujuan dari atasan.',
                'icon' => 'fa-solid fa-paper-plane',
                'color' => 'bg-primary'
            ];
        }

        if (in_array($requisition->status, ['Approved', 'Completed'])) {
            $trackingHistory[] = [
                'status' => 'Disetujui',
                'user' => $requisition->route_to, // Nama approver seharusnya dinamis
                'date' => $requisition->updated_at->addMinutes(rand(5, 55))->format('d M Y, H:i'), // Waktu approval (simulasi)
                'notes' => 'Permintaan sampel telah disetujui dan akan diproses lebih lanjut.',
                'icon' => 'fa-solid fa-circle-check',
                'color' => 'bg-success'
            ];
        } else if (in_array($requisition->status, ['Rejected', 'Cancelled'])) {
            $trackingHistory[] = [
                'status' => 'Ditolak',
                'user' => $requisition->route_to,
                'date' => $requisition->updated_at->addMinutes(rand(5, 55))->format('d M Y, H:i'),
                'notes' => 'Permintaan sampel ditolak.',
                'icon' => 'fa-solid fa-circle-xmark',
                'color' => 'bg-danger'
            ];
        }

        $responseData = $requisition->toArray();
        $responseData['tracking_history'] = $trackingHistory; // Tambahkan data tracking ke response

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
                'status' => 'Draft', // Status awal adalah Draft
                'route_to' => 'N/A',
            ]);
            Log::info("Requisition #{$requisition->id} berhasil dibuat sebagai Draft.");

            // ... (logika create RequisitionSpecial dan RequisitionItem tetap sama) ...
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
            } else { // Ini sekarang berlaku untuk 'Finished Good' DAN 'Special Order'
                foreach ($validated['items'] as $itemMasterId => $itemData) {
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'item_master_id' => $itemMasterId,
                        'item_detail_id' => null,
                        'material_type' => $validated['sub_category'], // Simpan nama sub-kategori
                        'quantity_required' => $itemData['quantity_required'],
                        'quantity_issued' => $itemData['quantity_issued'] ?? null,
                    ]);
                }
            }


            // =========== PERUBAHAN LOGIKA APPROVAL DIMULAI DI SINI ===========
            Log::info("Mencari approval path untuk: SAMPLE / {$validated['sub_category']}");
            // 1. Cari Approval Path berdasarkan Kategori dan Sub Kategori
            $approvalPath = ApprovalPath::where('category', 'SAMPLE')
                ->where('sub_category', $validated['sub_category'])
                ->first();

            // 2. Periksa apakah path ditemukan dan memiliki approver
            if ($approvalPath && !empty($approvalPath->sequence_approvers)) {
                Log::info("Approval path ditemukan untuk Requisition #{$requisition->id}. Approver NIKs: " . implode(', ', $approvalPath->sequence_approvers));

                $firstApproverNik = $approvalPath->sequence_approvers[0];
                $firstApprover = User::where('nik', $firstApproverNik)->first();

                if ($firstApprover) {
                    Log::info("Approver pertama (NIK: {$firstApproverNik}) ditemukan: {$firstApprover->name}.");

                    // 3. Update status requisition dan arahkan ke approver pertama
                    $requisition->update([
                        'status' => 'Pending',
                        'route_to' => $firstApprover->name
                    ]);

                    // 4. Buat log approval pertama
                    ApprovalLog::create([
                        'requisition_id' => $requisition->id,
                        'approver_nik'   => $firstApprover->nik,
                        'status'         => 'Pending',
                        'level'          => 1, // Urutan pertama
                        'token'          => Str::uuid()->toString(),
                    ]);
                    Log::info("ApprovalLog berhasil dibuat untuk Requisition #{$requisition->id}.");

                    // 5. Kirim email ke approver pertama menggunakan Job
                    SendRequisitionEmailJob::dispatch($requisition, $firstApprover);
                    Log::info("Job pengiriman email untuk Requisition #{$requisition->id} telah di-dispatch ke queue.");

                } else {
                    // Jika NIK approver ada di path tapi user tidak ditemukan di database
                    $requisition->update(['status' => 'Completed', 'route_to' => 'Error: Approver Not Found']);
                    Log::warning("Approver dengan NIK {$firstApproverNik} tidak ditemukan untuk Requisition ID {$requisition->id}.");
                }
            } else {
                // Jika tidak ada approval path yang di-setting, otomatis selesaikan
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada approval path yang cocok untuk Sample/{$validated['sub_category']}. Auto-complete Requisition ID {$requisition->id}.");
            }

            // =========== AKHIR PERUBAHAN LOGIKA APPROVAL ===========


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
            } else { // Ini sekarang berlaku untuk 'Finished Good' DAN 'Special Order'
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

}
