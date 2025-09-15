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
use Illuminate\Support\Facades\Log;

use function Illuminate\Log\log;

class ComplainController extends Controller
{
    public function index()
    {
        return view('page.complain.index');
    }

    public function store(StoreComplainRequest $request)
    {
        try{
            $validated = $request->validated();
            Log::info('Validated Data: ', $validated);
            $user = Auth::user();
            $casuer = User::where('nik', $user->nik)->first();

            if (!$user) {
                return response()->json(['message' => 'User belum login.'], 401);
            }
            if (!$user->atasan) {
                return response()->json(['message' => 'Atasan tidak ditemukan. Coba hubungi admin.'], 400);
            }
    
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

            activity()
                ->causedBy($casuer)
                ->performedOn($requisition)
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                ->log('user ' . $casuer->name . ' Membuat Requisition Complain dengan ID: ' . $requisition->id);

            return response()->json(['message' => 'Form Requisition berhasil disimpan.'], 200);
        }catch(\Exception $e){
            Log::error('Gagal menyimpan requisition: ' . $e->getMessage());
            return response()->json(['message' => 'Terdapat kesalahan dalam menyimpan form Requisition. Silakan coba lagi.'], 500);
        }
    }

    public function getData(Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw = $request->input('draw');

        $totalData = Requisition::count();

        $query = Requisition::query();

        $totalFiltered = $totalData;

        // Ambil data untuk halaman saat ini menggunakan offset dan limit
        $data = $query->offset($start)->limit($length)->get();

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
            $lastSequence = (int) substr($lastRecordThisYear->series_number, -4);
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
}
