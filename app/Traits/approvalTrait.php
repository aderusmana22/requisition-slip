<?php

namespace App\Traits;

use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait ApprovalTrait
{
    /**
     * Generate approval logs dari approval path, mendukung logika kondisional.
     *
     * @param  \App\Models\User  $requester
     * @param  int|string  $requisitionId
     * @param  string  $category
     * @param  string|null  $subCategory
     * @param  string|null  $requesterDepartmentCode // Parameter baru untuk logika kondisional
     * @return \Illuminate\Support\Collection
     */
    public function generateApprovalLogs($requester, $requisitionId, $category, $subCategory = null, $requesterDepartmentCode = null)
    {
        $query = ApprovalPath::where('category', $category);

        if (!empty($subCategory)) {
            $query->where('sub_category', $subCategory);
        }

        // --- BARIS YANG MENGHASILKAN ERROR JIKA DATA TIDAK DITEMUKAN ---
        // Memaksa Laravel mencari rute, jika tidak ada, akan throw error.
        $approvalPath = $query->firstOrFail(); 
        // ---------------------------------------------------

        $sequences = $approvalPath->sequence_approvers;
        $logs = collect();
        $targetSequence = collect();

        // 1. Tentukan alur mana yang akan digunakan berdasarkan Department Code
        // Cek jika requester adalah dari SnM (kode 5300)
        if ($requesterDepartmentCode === '5300' && isset($sequences['5300'])) {
            $targetSequence = collect($sequences['5300']);
        } elseif (isset($sequences['NON-5300'])) {
            $targetSequence = collect($sequences['NON-5300']);
        } else {
             // Fallback jika tidak ditemukan alur spesifik
             throw new \Exception("Approval sequence not defined for Category: {$category}, Sub: {$subCategory}, Dept: {$requesterDepartmentCode}");
        }
        
        // 2. Iterasi melalui alur yang dipilih
        foreach ($targetSequence as $approverStep) {
            $level = $approverStep['level'] ?? 1; // Ambil level dari data JSON
            $approverType = strtolower($approverStep['type'] ?? '');
            $approverValue = $approverStep['value'] ?? null; // NIK, Role Name, atau null jika 'atasan'

            // Lewati jika tipe atau nilai tidak valid
            if (empty($approverType) || ($approverType !== 'atasan' && empty($approverValue))) {
                Log::warning("Skipping invalid approver step in sequence for requisition ID: {$requisitionId}. Data: " . json_encode($approverStep));
                continue;
            }

            if ($approverType === 'atasan') {
                // Tipe 'atasan' (Manager Requester)
                if ($requester->atasan_nik) {
                    $logs->push([
                        'requisition_id' => $requisitionId,
                        'approver_nik'   => $requester->atasan_nik,
                        'status'         => 'pending',
                        'level'          => $level,
                        'token'          => bin2hex(random_bytes(16)),
                        'notes'          => null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            } elseif ($approverType === 'nik') {
                // Tipe 'nik' (Hardcoded NIK)
                 $logs->push([
                    'requisition_id' => $requisitionId,
                    'approver_nik'   => $approverValue,
                    'status'         => 'pending',
                    'level'          => $level,
                    'token'          => bin2hex(random_bytes(16)),
                    'notes'          => null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } elseif ($approverType === 'role') {
                // Tipe 'role'
                // Ambil semua user dengan role ini
                $users = User::whereHas('roles', function ($q) use ($approverValue) {
                    $q->where('name', $approverValue);
                })->get();

                foreach ($users as $user) {
                    $logs->push([
                        'requisition_id' => $requisitionId,
                        'approver_nik'   => $user->nik,
                        'status'         => 'pending',
                        'level'          => $level,
                        'token'          => bin2hex(random_bytes(16)),
                        'notes'          => null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }
        }

        // Bulk insert biar lebih cepat
        if ($logs->isNotEmpty()) {
            ApprovalLog::insert($logs->toArray());
        }

        return $logs;
    }
    
   
}