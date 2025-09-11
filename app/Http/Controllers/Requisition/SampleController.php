<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSampleRequisitionRequest;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\Requisition;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SampleController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->get();

        // === LOGIKA BARU UNTUK FILTER SUB CATEGORY ===
        $userDepartment = Auth::user()->department->name ?? null;

        // Definisikan aturan bisnis Anda di sini
        $rules = [
            'Packaging'     => ['Engineering & Maintenance','Sales & Marketing', 'R&D'],
            'Finished Good' => ['Engineering & Maintenance','R&D', 'QA', 'Sales & Marketing'],
            'Special Order' => ['Engineering & Maintenance','Sales & Marketing'],
        ];

        $allowedSubCategories = [];
        // Loop melalui aturan dan cek apakah departemen user ada di dalamnya
        foreach ($rules as $subCategory => $allowedDepartments) {
            if (in_array($userDepartment, $allowedDepartments)) {
                // Jika diizinkan, tambahkan ke daftar untuk dikirim ke view
                $allowedSubCategories[] = ['id' => $subCategory, 'text' => $subCategory];
            }
        }

        // Kirim data customers dan sub category yang sudah difilter ke view
        return view('page.sample.index', compact('customers', 'allowedSubCategories'));
    }

    public function getData()
    {
        $requisitions = Requisition::with(['customer', 'requester'])
            ->where('category', 'SAMPLE')->select('requisitions.*');

        return DataTables::of($requisitions)
            ->addIndexColumn()
            ->addColumn('requester_name', fn($req) => $req->requester->name ?? $req->requester_nik)
            ->addColumn('customer_name', fn($req) => $req->customer->name ?? 'N/A')
            ->editColumn('request_date', fn($req) => \Carbon\Carbon::parse($req->request_date)->format('d M Y'))
            ->editColumn('status', function ($req) {
                $badges = ['PENDING' => 'bg-warning text-dark', 'APPROVED' => 'bg-success', 'REJECTED' => 'bg-danger'];
                return '<span class="badge ' . ($badges[$req->status] ?? 'bg-secondary') . '">' . $req->status . '</span>';
            })
            ->addColumn('action', function ($req) {
                return '<div class="d-flex gap-2">
                            <button class="btn btn-sm btn-warning btn-edit" data-id="' . $req->id . '"><i class="fas fa-pencil-alt text-white"></i></button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="' . $req->id . '"><i class="fas fa-trash-alt"></i></button>
                        </div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store(StoreSampleRequisitionRequest $request)
    {
        // 1. Ambil data user yang login beserta departemennya
        $user = Auth::user();
        // Gunakan 'load' untuk efisiensi query, pastikan relasi 'department' ada di model User
        $user->load('department');

        // Ambil nama departemen dari user. Beri nilai default jika tidak ada.
        $departmentName = $user->department->name ?? 'Unknown';

        // 2. Tentukan tujuan approval (route to) berdasarkan departemen dan sub category
        $routeTo = '';

        // Logika utama berdasarkan departemen
        if ($departmentName === 'SnM') {
            switch ($request->sub_category) {
                case 'Packaging':
                    $routeTo = 'SnM Requester Manager';
                    break;
                case 'Finished Good':
                    $routeTo = 'Marketing Head'; // Mungkin ini tetap? Sesuaikan jika perlu
                    break;
                case 'Special Order':
                    $routeTo = 'SnM Requester Manager';
                    break;
            }
        } elseif ($departmentName === 'RnD') {
            // Jika dari RnD, semua sub category akan ke RnD Manager
            $routeTo = 'RnD Manager';

        } elseif ($departmentName === 'QA') {
            // Contoh lain: jika dari QA, semua sub category akan ke QA Manager
            $routeTo = 'QA Manager';
        }

        // 3. Fallback jika departemen tidak terdefinisi di logika di atas
        // Ini akan merutekan ke atasan langsung user tersebut (perlu implementasi lebih lanjut)
        if (empty($routeTo)) {
            $routeTo = 'Atasan Requester'; // Default fallback
        }

        // Proses penyimpanan ke database (kode ini tidak berubah)
        DB::beginTransaction();
        try {
            $requisition = Requisition::create([
                'requester_nik'     => $user->nik,
                'customer_id'       => $request->customer_id,
                'no_srs'            => $request->no_srs,
                'account'           => $request->account,
                'cost_center'       => $request->cost_center,
                'request_date'      => $request->request_date,
                'category'          => 'SAMPLE',
                'sub_category'      => $request->sub_category,
                'route_to'          => $routeTo, // Menggunakan $routeTo dari logika baru
                'status'            => 'PENDING',
                'objectives'        => $request->objectives,
                'estimated_potential' => $request->estimated_potential,
            ]);

            foreach ($request->items as $itemData) {
                $requisition->requisitionItems()->create([
                    'item_master_id'     => $itemData['item_master_id'],
                    'quantity_required'  => $itemData['quantity_required'],
                    'quantity_issued'    => $itemData['quantity_issued'] ?? 0,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Sample Requisition berhasil dibuat!']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating sample requisition: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function searchItems(Request $request)
    {
        $term = $request->input('term', '');
        $items = ItemMaster::where(fn($q) => $q->where('item_master_code', 'LIKE', "%{$term}%")->orWhere('item_master_name', 'LIKE', "%{$term}%"))
            ->limit(15)->get();

        return response()->json($items->map(fn($item) => [
            'id' => $item->id, 'text' => "{$item->item_master_code} - {$item->item_master_name}", 'unit' => $item->unit,
        ]));
    }
}
