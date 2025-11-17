<?php

namespace App\Traits;

use App\Models\Master\TrackingPath;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
use App\Models\Requisition\Tracking;
use App\Models\User;

trait traitTracking
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
    public function generateTrackingPath($requisitionId, $category, $subCategory = null, $printBatch = null)
    {
        $query = TrackingPath::where('category', $category);

        if (!empty($subCategory)) {
            $query->where('sub_category', $subCategory);
        }

        if (!empty($printBatch)) {
            $query->where('print_batch', $printBatch);
        }

        $approvalPath = $query->firstOrFail();

        $sequence = collect($approvalPath->sequence_approvers);
        $logs = collect();

        foreach ($sequence as $index => $role) {
            // Ambil semua user dengan role ini
            $users = User::whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            })->first();

            if ($users) {
                $logs->push([
                        'requisition_id' => $requisitionId,
                        'current_position' => $users->name,
                        'last_updated'   => now(),
                        'token'          => bin2hex(random_bytes(16)),
                        'notes'          => null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                ]);
            }
        }

        // Bulk insert biar lebih cepat
        if ($logs->isNotEmpty()) {
            Tracking::insert($logs->toArray());
        }

        return $logs;
    }
}
