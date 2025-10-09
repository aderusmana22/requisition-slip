<?php

namespace App\Traits;

use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
use App\Models\User;

trait ApprovalTrait
{
    /**
     * Generate approval logs dari approval path.
     *
     * @param  \App\Models\User  $requester
     * @param  int|string  $requisitionId
     * @param  string  $category
     * @param  string|null  $subCategory
     * @return \Illuminate\Support\Collection
     */
    public function generateApprovalLogs($requester, $requisitionId, $category, $subCategory = null)
    {
        $query = ApprovalPath::where('category', $category);

        if (!empty($subCategory)) {
            $query->where('sub_category', $subCategory);
        }

        $approvalPath = $query->firstOrFail();

        $sequence = collect($approvalPath->sequence_approvers);
        $logs = collect();

        foreach ($sequence as $index => $role) {
            $level = $index + 1;

            if (strtolower($role) === 'atasan') {
                // Ambil NIK atasan requester
                if ($requester->atasan_nik) {
                    $logs->push([
                        'requisition_id' => $requisitionId,
                        'approver_nik'   => $requester->atasan_nik,
                        'status'         => 'Pending',
                        'level'          => $level,
                        'token'          => bin2hex(random_bytes(16)),
                        'notes'          => null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            } else {
                // Ambil semua user dengan role ini
                $users = User::whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
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
