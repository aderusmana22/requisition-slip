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
use Illuminate\Validation\Rule;

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

        // Ambil sub_category dari request
        $subCategory = $validated['sub_category_id'] ?? null;

        // Validasi Duplikat: Pastikan kombinasi category dan sub_category belum ada
        $existingPath = ApprovalPath::where('category', $validated['category_id'])
            ->where('sub_category', $subCategory)
            ->exists();

        if ($existingPath) {
            return response()->json(['message' => 'Error: An approval path for this category and sub-category already exists.'], 422);
        }

        try {
            $data = null;
            DB::transaction(function() use($validated, $subCategory, &$data){
                $data = ApprovalPath::create([
                    'category' => $validated['category_id'],
                    'sub_category' => $subCategory,
                    'sequence_approvers' => $validated['approvers'],
                ]);
            });

            if (!$data) {
                throw new \RuntimeException('Failed to create approval path');
            }

            // Logging Activity
            $logMessage = "Membuat alur persetujuan baru untuk {$data->category}" . ($data->sub_category ? " - {$data->sub_category}" : "") . ".";
            $properties = [
                'category' => $data->category,
                'sub_category' => $data->sub_category,
                'approvers' => $data->sequence_approvers,
            ];

            activity()
                ->causedBy($causer)
                ->performedOn($data)
                ->useLog('path - ' . strtolower($data->category))
                ->event('create')
                ->withProperties($properties)
                ->log($logMessage);

            return response()->json(['message' => 'Approver successfully created'], 201);
        } catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $approvalPath = ApprovalPath::findOrFail($id);

        return response()->json([
            'category_id' => $approvalPath->category,
            'sub_category_id' => $approvalPath->sub_category,
            'approver_user_ids' => $approvalPath->sequence_approvers, 
        ]);
    }

    public function update(Request $request, $id)
    {
        $approvalPath = ApprovalPath::findOrFail($id);

        // Validasi update: hanya approvers yang boleh diubah (category/sub_category di-disable di UI)
        $validated = $request->validate([
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'string',
        ]);

        $causer = Auth::user();

        try {
            $oldApprovers = $approvalPath->sequence_approvers;

            DB::transaction(function() use($validated, $approvalPath){
                $approvalPath->update(['sequence_approvers' => $validated['approvers']]);
            });

            // Logging Activity
            $logMessage = "Memperbarui alur persetujuan untuk {$approvalPath->category}" . ($approvalPath->sub_category ? " - {$approvalPath->sub_category}" : "") . ".";
            $properties = [
                'category' => $approvalPath->category,
                'sub_category' => $approvalPath->sub_category,
                'old_approvers' => $oldApprovers,
                'new_approvers' => $validated['approvers'],
            ];

            activity()
                ->causedBy($causer)
                ->performedOn($approvalPath)
                ->useLog('path - ' . strtolower($approvalPath->category))
                ->event('update')
                ->withProperties($properties)
                ->log($logMessage);

            return response()->json(['message' => 'Approver successfully updated'], 200);
        } catch(\Exception $e){
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Menyediakan data kategori dan sub-kategori untuk dropdown Select2 di Modal.
     */
    public function categories()
    {
        $categories = [
            'Sample',
            'Complain',
            'Free Goods',
        ];

        // Sub-kategori khusus untuk kategori 'Sample'
        $subCategories = [
            'Packaging',
            'Finished Goods',
            'Special Order',
        ];

        // Mengambil kombinasi path yang sudah terdaftar untuk validasi di sisi client
        $existingPaths = ApprovalPath::select('category', 'sub_category')->get();

        return response()->json([
            'categories' => $categories, 
            'subCategories' => $subCategories, 
            'existingPaths' => $existingPaths
        ]);
    }

    public function approverName()
    {
        $name = Role::pluck('name' ,'name')->toArray();
        $name['atasan'] = 'atasan'; // Menambahkan opsi 'atasan' manual
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

        $orderColumnName = $request->input("columns.{$orderColumnIndex}.name");

        $totalData = ApprovalPath::count();
        $query = ApprovalPath::query();

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
        } else {
            $query->orderBy('created_at', 'desc'); // Default sorting
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
                'created_at' => $path->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $path->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function destroy($id)
    {
        $causer = Auth::user();
        try {
            DB::transaction(function () use ($id, $causer) {
                $data = ApprovalPath::findOrFail($id);

                $logMessage = "Menghapus alur persetujuan untuk {$data->category}" . ($data->sub_category ? " - {$data->sub_category}" : "") . ".";
                $properties = [
                    'category' => $data->category,
                    'sub_category' => $data->sub_category,
                    'deleted_approvers' => $data->sequence_approvers,
                ];

                activity()
                    ->causedBy($causer)
                    ->performedOn($data)
                    ->useLog('path - ' . strtolower($data->category))
                    ->event('delete')
                    ->withProperties($properties)
                    ->log($logMessage);

                $data->delete();
            });
            return response()->json(['message' => 'Approver successfully deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}