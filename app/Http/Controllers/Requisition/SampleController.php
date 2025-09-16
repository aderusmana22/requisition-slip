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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SampleController extends Controller
{
    private function generateSrsNumber()
    {
        // 1. Definisikan format yang baru
        $prefix = 'S';
        $year = date('y');   // Format: 25 (untuk tahun 2025)
        $month = date('m');  // Format: 09 (untuk bulan September)

        // 2. Cari nomor terakhir yang dibuat pada bulan dan tahun ini
        $lastRequisition = Requisition::whereYear('created_at', date('Y'))
                                    ->whereMonth('created_at', date('m'))
                                    ->orderBy('id', 'desc') // Urutkan berdasarkan ID terbaru
                                    ->first();

        $runningNumber = 1; // Nomor awal jika tidak ada data sebelumnya
        if ($lastRequisition) {
            $lastParts = explode(' ', $lastRequisition->no_srs);
            $lastRunningNumber = end($lastParts); // Mengambil bagian terakhir
            $runningNumber = intval($lastRunningNumber) + 1;
        }

        // 3. Gabungkan semua bagian menjadi format yang diinginkan sprintf('%03d', $runningNumber) akan membuat nomor urut menjadi 3 digit (e.g., 1 -> 001, 13 -> 013)
        return "$prefix $year $month " . sprintf('%03d', $runningNumber);
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
                'status' => 'Pending',
                'route_to' => $validated['sub_category'] === 'Special Order' ? 'Atasan SnM' : 'Atasan ' . ($user->department?->name ?? 'Requester'),
            ]);

            // **MODIFIED**: Save to requisition_specials if it's a Special Order
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

           if ($validated['sub_category'] === 'Finished Good') {
                foreach ($validated['items'] as $itemMasterId => $itemData) {
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'item_master_id' => $itemMasterId,
                        'item_detail_id' => null,
                        'material_type' => 'Finished Good', // <-- TAMBAHKAN INI
                        'quantity_required' => $itemData['quantity_required'],
                        'quantity_issued' => $itemData['quantity_issued'] ?? null,
                    ]);
                }
            } else { // For Packaging and Special Order
                $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
                foreach ($validated['items'] as $itemDetailId => $itemData) {
                    if (isset($itemDetails[$itemDetailId])) {
                        $itemDetail = $itemDetails[$itemDetailId];
                        RequisitionItem::create([
                            'requisition_id' => $requisition->id,
                            'item_master_id' => $itemDetail->item_master_id,
                            'item_detail_id' => $itemDetail->id,
                            'material_type' => $itemDetail->material_type, // <-- TAMBAHKAN INI
                            'quantity_required' => $itemData['quantity_required'],
                            'quantity_issued' => $itemData['quantity_issued'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Sample Requisition berhasil dibuat dan dikirim ke Atasan.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat sample requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $requisition = Requisition::with([
            'requisitionItems.itemDetail',
            'requisitionItems.itemMaster',
            'requisitionSpecial'
        ])->findOrFail($id);
        return response()->json($requisition);
    }

    public function update(UpdateSampleRequisitionRequest $request, $id)
    {
        $requisition = Requisition::findOrFail($id);
        $user = Auth::user();

        DB::beginTransaction();
        try {
            if ($user->department?->name === 'QA/QM' && $requisition->route_to === 'Atasan QA/QM') {
                $validatedQa = $request->validate([
                    'sample_origin' => 'required|string',
                    'sample_description_batch' => 'nullable|string',
                    'sample_description_wb' => 'nullable|string',
                    'sample_description_tank' => 'nullable|string',
                    'production_date' => 'required|date',
                    'sample_preparation' => 'required|string',
                    'qa_notes' => 'nullable|string',
                ]);

                $requisition->update($validatedQa);
                $requisition->route_to = 'Atasan QA/QM';
                $requisition->save();
            } else {
                $validated = $request->validated();
                $requisition->update($validated);

                // Hapus item lama sebelum menambahkan yang baru
                $requisition->requisitionItems()->delete();

                // Logika baru yang disesuaikan seperti di method store
                if ($validated['sub_category'] === 'Finished Good') {
                    foreach ($validated['items'] as $itemMasterId => $itemData) {
                        RequisitionItem::create([
                            'requisition_id' => $requisition->id,
                            'item_master_id' => $itemMasterId,
                            'item_detail_id' => null,
                            'quantity_required' => $itemData['quantity_required'],
                            'quantity_issued' => $itemData['quantity_issued'] ?? null,
                        ]);
                    }
                } else { // Untuk Packaging dan Special Order
                    $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
                    foreach ($validated['items'] as $itemDetailId => $itemData) {
                        if (isset($itemDetails[$itemDetailId])) {
                            $itemDetail = $itemDetails[$itemDetailId];
                            RequisitionItem::create([
                                'requisition_id' => $requisition->id,
                                'item_master_id' => $itemDetail->item_master_id,
                                'item_detail_id' => $itemDetail->id,
                                'quantity_required' => $itemData['quantity_required'],
                                'quantity_issued' => $itemData['quantity_issued'] ?? null,
                            ]);
                        }
                    }
                }

                // Logika untuk update RequisitionSpecial jika ada
                if ($validated['sub_category'] === 'Special Order' && $requisition->requisitionSpecial) {
                    $requisition->requisitionSpecial->update([
                        'requested_date' => $validated['sample_completion_date'] ?? null,
                        'weight_selection' => $validated['sample_weight'] ?? null,
                        'packaging_selection' => $validated['sample_weight'] ?? null,
                        'sample_count' => $validated['sample_weight'] ?? null,
                        'purpose' => $validated['sample_weight'] ?? null,
                        'coa_required' => $validated['sample_weight'] ?? null,
                        'shipment_method' => $validated['sample_weight'] ?? null,
                        'source' => $validated['sample_weight'] ?? null,
                        'sample_notes' => $validated['sample_weight'] ?? null,
                        'production_date' => $validated['sample_weight'] ?? null,
                        'description' => $validated['sample_weight'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Sample Requisition berhasil diubah.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah sample requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $requisition = Requisition::findOrFail($id);
            $requisition->delete();

            return response()->json(['success' => true, 'message' => 'Requisition has been deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete requisition.'], 500);
        }
    }
}
