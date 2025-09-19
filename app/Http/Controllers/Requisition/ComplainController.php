<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplainRequest;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ComplainController extends Controller
{
    public function index()
    {
        return view('page.complain.index');
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
            DB::transaction(function () use ($validated, $user) {
        
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

            return response()->json(['message' => 'Form Requisition complain berhasil dibuat.'], 201);
        }catch(\Exception $e){
            Log::error('Gagal menyimpan requisition: ' . $e->getMessage());
            return response()->json(['message' => 'Terdapat kesalahan dalam menyimpan form Requisition. Silakan coba lagi.'], 500);
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
        $items = ItemMaster::with('details')->get();
        return response()->json(['items' => $items]);
    }

    public function getFormDetail($id){
        try {
            // Eager load relasi yang dibutuhkan: customer dan items beserta detail dari item
            // 'items' adalah nama relasi pivot, 'items.detail' mengambil detail produk dari pivot
            $complain = Requisition::with(['customer', 'requisitionItems.itemMaster.details'])->findOrFail($id);

            return response()->json($complain);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Complain data not found.'], 404);
        } catch (\Exception $e) {
            // Log error jika perlu: Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred on the server.'], 500);
        }
    }
}
