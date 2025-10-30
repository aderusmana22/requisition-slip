<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ComplainLogController extends Controller
{
    public function index()
    {
        return view('page.complain.logs.index');
    }

    public function getData()
    {
        $activity = Activity::where('log_name', 'complain')->with(['causer', 'subject'])->get();

        return response()->json(['data' => $activity]);
    }
}
