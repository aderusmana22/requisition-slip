<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\approvalpathRequest;
use App\Models\Requisition\ApprovalPath;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RequisitionPath extends Controller
{
    public function index()
    {
        return view('page.master.approval.index');
    }

    public function store(approvalpathRequest $request)
    {
        $validated = $request->validated();
        $causer = Auth::user();

        try{
            DB::transaction(function() use($validated, $causer){

                Log::info('Validated Data: ', $validated);

                if ($validated['category_id'] === 'Sample') {
                    if (empty($validated['sub_category_id'])) {
                        throw new \Exception('Sub-category is required for the Sample category.');
                    }
                }
                elseif ($validated['category_id'] === 'Complain' || $validated['category_id'] === 'Free Goods') {
                    if ($validated['sub_category_id'] !== null) {
                        throw new \Exception('Sub-category must be empty/null for Complain or Free Goods categories.');
                    }
                }

                $data = ApprovalPath::create([
                    'category' => $validated['category_id'],
                    'sub_category' => $validated['sub_category_id'],
                    'sequence_approvers' => $validated['approvers'],
                ]);

                activity()
                    ->causedBy($causer)
                    ->withProperties(['approval_path_id' => $data->id])
                    ->log('Created new approval path');

            });
            return response()->json(['message' => 'Approver successfully created'], 201);
        }catch(\Exception $e){
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function categories()
    {
        $categories = [
            'Sample',
            'Complain',
            'Free Goods',
        ];
        $subCategories = [
            'Packaging',
            'Finished Goods',
            'Special Order',
        ];
        return response()->json(['categories' => $categories, 'subCategories' => $subCategories]);
    }

    public function approverName()
    {
        $name = Role::pluck('name' ,'name');
        return response()->json(['approverName' => $name]);
    }

    public function approverList(Request $request)
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
        $totalData = ApprovalPath::count();

        // Mulai query builder
        $query = ApprovalPath::query();

        // 2. Terapkan filter pencarian jika ada input dari kotak search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('category', 'like', "%{$searchValue}%")
                    ->orWhere('sub_category', 'like', "%{$searchValue}%")
                    ->orWhere('sequence_approvers', 'like', "%{$searchValue}%");
            });
        }

        $totalFiltered = $query->count();

        if (!empty($orderColumnName)) {
            $query->orderBy($orderColumnName, $orderDirection);
        }

        $approvalPaths = $query->offset($start)
            ->limit($length)
            ->get();

        $data = $approvalPaths->map(function ($path) {
            return [
                'id' => $path->id,
                'category' => $path->category,
                'sub_category' => $path->sub_category,
                'sequence_approvers' => $path->sequence_approvers,
                'created_at' => $path->created_at,
                'updated_at' => $path->updated_at,
            ];
        });

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];

        return response()->json($response);
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $data = ApprovalPath::where('id', $id)->first();
                if ($data) {
                    $data->delete();
                }
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
