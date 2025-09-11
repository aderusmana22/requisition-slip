<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplainRequest;
use App\Models\Master\Customer;
use App\Models\Master\Department;
use App\Models\Requisition\Requisition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplainController extends Controller
{
    public function index()
    {
        return view('page.complain.index');
    }

    public function store(StoreComplainRequest $request)
    {
        $validated = $request->validated();

        $requisition = new Requisition();

        return redirect()->route('complain-form.index');
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

    public function getCustomerData(Customer $customer)
    {
        return response()->json($customer);
    }
}
