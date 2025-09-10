<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Models\Requisition\Requisition;
use Illuminate\Http\Request;

class ComplainController extends Controller
{
    public function index()
    {
        return view('page.complain.index');
    }

    public function store(Request $request)
    {
        // Validate and store the complain form data
        // ...
        $validated = $request->validate();

        return redirect()->route('complain-form.index')->with('success', 'Complain form submitted successfully.');
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
}
