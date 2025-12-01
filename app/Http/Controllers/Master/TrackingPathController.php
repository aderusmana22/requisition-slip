<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\TrackingPath;
use App\Models\Requisition\ApprovalPath; // [UPDATE] Model ini digunakan untuk mengambil list Kategori dari DB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class TrackingPathController extends Controller
{
    public function index()
    {
        return view('page.master.trackingPath.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|string',
            'sub_category_id' => 'nullable|string',
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'string',
            'print_batch' => 'nullable|string',
        ]);
        $causer = Auth::user();

        $subCategory = $validated['sub_category_id'] ?? null;

        $existingPath = TrackingPath::where('category', $validated['category_id'])
            ->where('sub_category', $subCategory)
            ->exists();

        if ($existingPath) {
            return response()->json(['message' => 'Error: An approval path for this category and sub-category already exists.'], 422);
        }

        try {
            $data = null;
            DB::transaction(function () use ($validated, $subCategory, &$data) {
                $data = TrackingPath::create([
                    'category' => $validated['category_id'],
                    'sub_category' => $subCategory,
                    'sequence_approvers' => $validated['approvers'],
                    'print_batch' => $validated['print_batch'] ?? null,
                ]);
            });

            if (!$data) {
                throw new \RuntimeException('Failed to create approval path');
            }

            $logMessage = "Membuat alur persetujuan tracking baru untuk {$data->category}" . ($data->sub_category ? " - {$data->sub_category}" : "") . ".";
            $properties = [
                'category' => $data->category,
                'sub_category' => $data->sub_category,
                'approvers' => $data->sequence_approvers,
                'print_batch' => $data->print_batch ?? null,
            ];

            activity()
                ->causedBy($causer)
                ->performedOn($data)
                ->useLog('path - ' . strtolower($data->category))
                ->event('create')
                ->withProperties($properties)
                ->log($logMessage);

            return response()->json(['message' => 'Approver successfully created'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $approvalPath = TrackingPath::findOrFail($id);

        // Mengubah format sequence_approvers agar sesuai dengan value di Select2
        $approverRoles = $approvalPath->sequence_approvers;

        return response()->json([
            'category_id' => $approvalPath->category,
            'sub_category_id' => $approvalPath->sub_category,
            'approver_user_ids' => $approverRoles,
            'print_batch' => $approvalPath->print_batch,
        ]);
    }

    public function update(Request $request, $id)
    {
        $approvalPath = TrackingPath::findOrFail($id);

        // Validasi sederhana untuk update
        $validated = $request->validate([
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'string',
            'print_batch' => 'string|nullable',
        ]);

        $causer = Auth::user();

        try {
            $oldApprovers = $approvalPath->sequence_approvers;
            $oldPrintBatch = $approvalPath->print_batch ?? null;

            DB::transaction(function () use ($validated, $approvalPath) {
                $approvalPath->update(['sequence_approvers' => $validated['approvers'], 'print_batch' => $validated['print_batch'] ?? null]);
            });

            // Logging
            $logMessage = "Memperbarui alur persetujuan untuk {$approvalPath->category}" . ($approvalPath->sub_category ? " - {$approvalPath->sub_category}" : "") . ".";
            $properties = [
                'category' => $approvalPath->category,
                'sub_category' => $approvalPath->sub_category,
                'old_print_batch' => $oldPrintBatch,
                'new_print_batch' => $validated['print_batch'] ?? null,
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
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function categories()
    {
        // [UPDATE - MENGGUNAKAN DB]
        // Mengambil kategori unik dari tabel approval_paths agar dinamis dan konsisten
        $categories = ApprovalPath::select('category')
            ->distinct()
            ->pluck('category');

        // Mengambil sub-kategori unik dari tabel approval_paths
        $subCategories = ApprovalPath::select('sub_category')
            ->whereNotNull('sub_category')
            ->where('sub_category', '!=', '')
            ->distinct()
            ->pluck('sub_category');

        // Mengambil path yang sudah ada di tracking_paths untuk validasi frontend (mencegah duplikasi)
        $existingPaths = TrackingPath::select('category', 'sub_category')->get();

        return response()->json([
            'categories' => $categories,
            'subCategories' => $subCategories,
            'existingPaths' => $existingPaths
        ]);
    }

    public function approverName()
    {
        $name = Role::pluck('name', 'name');
        $name['atasan'] = 'atasan';
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
        $totalData = TrackingPath::count();

        // Mulai query builder
        $query = TrackingPath::query();

        // Terapkan filter pencarian jika ada input dari kotak search
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
                'sub_category' => $path->sub_category ?? '-',
                'sequence_approvers' => $path->sequence_approvers,
                'print_batch' => $path->print_batch ?? '-',
                'created_at' => $path->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $path->updated_at->format('Y-m-d H:i:s'),
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
        $causer = Auth::user();
        try {
            DB::transaction(function () use ($id, $causer) {
                $data = TrackingPath::findOrFail($id);

                $logMessage = "Menghapus alur persetujuan untuk {$data->category}" . ($data->sub_category ? " - {$data->sub_category}" : "") . ".";
                $properties = [
                    'category' => $data->category,
                    'sub_category' => $data->sub_category,
                    'print_batch' => $data->print_batch ?? null,
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