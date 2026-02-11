<?php

namespace App\Traits;

use App\Models\Master\TrackingPath;
use App\Models\Requisition\Tracking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

trait traitTracking
{
    /**
     * Generate tracking logs berdasarkan Tracking Path Master.
     * * @param int|string $requisitionId
     * @param string $category
     * @param string|null $subCategory
     * @param int|bool|null $printBatch
     */
    public function generateTrackingPath($requisitionId, $category, $subCategory = null, $printBatch = false)
    {
        try {
            $query = TrackingPath::where('category', $category);

            // 1. Filter Sub Category (Lebih Aman)
            // Logika: Cari yang COCOK, atau yang GENERAL (kosong/null)
            if (!empty($subCategory)) {
                $query->where(function($q) use ($subCategory) {
                    $q->where('sub_category', $subCategory)
                      ->orWhereNull('sub_category')
                      ->orWhere('sub_category', '');
                });
            } else {
                // Jika request tidak punya sub-category, cari yang di database juga kosong
                $query->where(function($q) {
                    $q->whereNull('sub_category')
                      ->orWhere('sub_category', '');
                });
            }

            // 2. Filter Print Batch (Khusus Sample Packaging)
            if (!is_null($printBatch)) {
                $query->where('print_batch', $printBatch);
            }

            // 3. Ambil Path (Prioritaskan yang paling baru dibuat jika ada duplikat)
            $trackingPath = $query->orderBy('id', 'desc')->first();

            if (!$trackingPath) {
                Log::warning("No Tracking Path found for: {$category} - {$subCategory}");
                return collect(); // Kembalikan koleksi kosong agar tidak error
            }

            // 4. Generate Log Tracking
            $sequence = collect($trackingPath->sequence_approvers);
            $logs = collect();

            foreach ($sequence as $role) {
                // Cari User berdasarkan Role (Ambil user pertama yang punya role tersebut)
                $user = User::whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                })->first();

                if ($user) {
                    $logs->push([
                        'requisition_id'   => $requisitionId,
                        'current_position' => $user->name, 
                        'approver_nik'     => $user->nik,
                        'last_updated'     => null,
                        'notes'            => null,
                        'token'            => bin2hex(random_bytes(16)),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                } else {
                    Log::warning("User with role '{$role}' not found for Tracking Path ID: {$trackingPath->id}");
                }
            }

            // 5. Simpan ke Database (Bulk Insert)
            if ($logs->isNotEmpty()) {
                Tracking::insert($logs->toArray());
            }

            return $logs;

        } catch (\Exception $e) {
            Log::error("Error generating tracking path: " . $e->getMessage());
            return collect();
        }
    }
}