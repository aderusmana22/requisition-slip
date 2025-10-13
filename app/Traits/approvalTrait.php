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
     * @param  string|null  $requesterDepartmentCode // Parameter Department Code
     * @return \Illuminate\Support\Collection
     */
    public function generateApprovalLogs($requester, $requisitionId, $category, $subCategory = null, $requesterDepartmentCode = null)
    {
        $query = ApprovalPath::where('category', $category);

        if (!empty($subCategory)) {
            $query->where('sub_category', $subCategory);
        }

        // Ambil satu baris ApprovalPath (firstOrFail akan throw error jika tidak ada)
        $approvalPath = $query->firstOrFail(); 
        
        $sequences = $approvalPath->sequence_approvers;
        $logs = collect();
        $targetSequence = collect();

        // 1. Tentukan kunci alur mana yang akan digunakan: "5300" (SnM) atau "NON-5300"
        $pathKey = ($requesterDepartmentCode === '5300') ? '5300' : 'NON-5300';
        
        if (isset($sequences[$pathKey])) {
            $targetSequence = collect($sequences[$pathKey]);
        } else {
             // Error jika kunci alur tidak ada di data JSON
             throw new \Exception("Approval sequence not defined in JSON for path key: {$pathKey}");
        }
        
        // 2. Iterasi melalui alur yang dipilih
        foreach ($targetSequence as $approverStep) {
            $level = $approverStep['level'] ?? 10; 
            $approverType = strtolower($approverStep['type'] ?? '');
            $approverValue = $approverStep['value'] ?? null;

            if (empty($approverType) || ($approverType !== 'atasan' && empty($approverValue))) {
                Log::warning("Skipping invalid approver step in sequence for requisition ID: {$requisitionId}. Data: " . json_encode($approverStep));
                continue;
            }

            $approvers = collect();

            if ($approverType === 'atasan') {
                // Tipe 'atasan' (SnM Manager / Atasan Requester)
                if ($requester->atasan_nik) {
                    $approvers->push(User::where('nik', $requester->atasan_nik)->first());
                }
            } elseif ($approverType === 'nik') {
                // Tipe 'nik' (Hardcoded NIK)
                 $approvers->push(User::where('nik', $approverValue)->first());
            } elseif ($approverType === 'role') {
                // Tipe 'role' (HCD Dept. Head atau Business Controller)
                $users = User::whereHas('roles', function ($q) use ($approverValue) {
                    $q->where('name', $approverValue);
                })->get();
                $approvers = $users;
            }

            // Tambahkan ke log jika user ditemukan
            foreach ($approvers->filter() as $user) {
                 $logs->push([
                    'requisition_id' => $requisitionId,
                    'approver_nik'   => $user->nik,
                    'status'         => 'Pending',
                    'level'          => $level,
                    'token'          => bin2hex(random_bytes(16)),
                    'notes'          => null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        // Bulk insert
        if ($logs->isNotEmpty()) {
            ApprovalLog::insert($logs->toArray());
        }

        return $logs;
    }
}