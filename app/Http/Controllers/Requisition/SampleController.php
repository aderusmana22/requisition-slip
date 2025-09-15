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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SampleController extends Controller
{
    // FUNGSI BARU: Untuk auto-generate Nomor SRS
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
            // Jika ada data, ambil nomor urut terakhir dan tambahkan 1, Contoh: "S 25 01 913" -> kita ambil "913"
            $lastParts = explode(' ', $lastRequisition->no_srs);
            $lastRunningNumber = end($lastParts); // Mengambil bagian terakhir
            $runningNumber = intval($lastRunningNumber) + 1;
        }

        // 3. Gabungkan semua bagian menjadi format yang diinginkan sprintf('%03d', $runningNumber) akan membuat nomor urut menjadi 3 digit (e.g., 1 -> 001, 13 -> 013)
        return "$prefix $year $month " . sprintf('%03d', $runningNumber);
    }

    // FUNGSI BARU: AJAX untuk mengambil Item Master berdasarkan Material Type
    public function getProductsByMaterialTypes(Request $request)
    {
        $request->validate(['material_types' => 'required|array']);

        $products = ItemMaster::whereHas('itemDetails', function ($query) use ($request) {
            $query->whereIn('material_type', $request->material_types);
        })->select('id', 'item_master_name')->distinct()->get();

        return response()->json($products);
    }

    // FUNGSI BARU: AJAX untuk mengambil Item Detail berdasarkan Item Master
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

    /**
     * Get data for DataTables.
     */
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
                $name = e($requisition->requester_name ?? 'N/A');
                $nik = e($requisition->requester_nik);

                return '
                    <div class="d-flex align-items-center">
                        <div class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-dark me-2">
                            <img src="' . $avatar . '" alt="avatar" class="img-fluid">
                        </div>
                        <div>
                            <p class="mb-0 f-w-600">' . $name . '</p>
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSampleRequisitionRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user = Auth::user();

            // 1. Siapkan data dasar yang selalu ada untuk semua sub-kategori
            $baseData = [
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
            ];

            // 2. Tentukan route_to dan tambahkan data spesifik jika sub-kategori adalah Special Order
            if ($validated['sub_category'] === 'Special Order') {
                $baseData['route_to'] = 'Atasan SnM'; // Rute khusus untuk Special Order

                // Gabungkan data spesifik untuk Special Order
                $specialOrderData = [
                    'sample_completion_date' => $validated['sample_completion_date'] ?? null,
                    'sample_weight' => $validated['sample_weight'] ?? null,
                    'sample_packaging' => $validated['sample_packaging'] ?? null,
                    'sample_quantity_details' => $validated['sample_quantity_details'] ?? null,
                    'coa_required' => $validated['coa_required'] ?? null,
                    'delivery_method' => $validated['delivery_method'] ?? null,
                ];
                $dataToCreate = array_merge($baseData, $specialOrderData);

            } else {
                // Logika route_to untuk sub-kategori lain
                $departmentName = $user->department?->name ?? 'Requester';
                $baseData['route_to'] = 'Atasan ' . $departmentName;
                $dataToCreate = $baseData;
            }

            // 3. Buat requisition dengan data yang sudah difilter
            $requisition = Requisition::create($dataToCreate);

            // 4. Proses item (tidak berubah)
            $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
            foreach ($validated['items'] as $itemDetailId => $itemData) {
                if (isset($itemDetails[$itemDetailId])) {
                    $itemDetail = $itemDetails[$itemDetailId];
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'item_detail_id' => $itemDetail->id,
                        'quantity_required' => $itemData['quantity_required'],
                        'quantity_issued' => $itemData['quantity_issued'],
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Sample Requisition created successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sample requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $requisition = Requisition::with('requisitionItems.itemDetail')->findOrFail($id);
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
        }
        else {
                // Validasi sekarang ditangani oleh UpdateSampleRequisitionRequest secara otomatis
                $validated = $request->validated();
                $requisition->update($validated);

                // Logika sync item
                $requisition->requisitionItems()->delete();
                $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
                foreach ($validated['items'] as $itemDetailId => $itemData) {
                    if (isset($itemDetails[$itemDetailId])) {
                        $itemDetail = $itemDetails[$itemDetailId];
                        RequisitionItem::create([
                            'requisition_id' => $requisition->id,
                            'item_detail_id' => $itemDetail->id,
                            'quantity_required' => $itemData['quantity_required'],
                            'quantity_issued' => $itemData['quantity_issued'],
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Requisition updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sample requisition: ' . $e->getMessage());
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
