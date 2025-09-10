<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Department;
use App\Models\Master\Customer;

class SampleController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        $customers = Customer::all();

        return view('page.sample.index', compact('customers', 'departments'));
    }
}
