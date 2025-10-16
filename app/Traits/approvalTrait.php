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
     * Generate approval logs dari approval path.
     *
     * @param  \App\Models\User  $requester
     * @param  int|string  $requisitionId
     * @param  string  $category
     * @param  string|null  $pathSubCategory  // Nilai ini datang dari Controller (misal: 'SNM_PATH')
     * @return \Illuminate\Support\Collection
     */
    public function generateApprovalLogs($requester, $requisitionId, $category, $pathSubCategory = null) 
    {
        $query = ApprovalPath::where('category', $category);

        // KRITIS: Trait mencari path yang spesifik ('SNM_PATH' atau 'NON_SNM_PATH')
        if (!empty($pathSubCategory)) {
            $query->where('sub_category', $pathSubCategory);
        }

        $approvalPath = $query->firstOrFail(); 
        
        $targetSequence = collect($approvalPath->sequence_approvers); 
        $logs = collect();

        foreach ($targetSequence as $approverStep) {
            $level = $approverStep['level'] ?? 10; 
            $approverType = strtolower($approverStep['type'] ?? '');
            $approverValue = $approverStep['value'] ?? null;
            $approverNik = null;

            if (empty($approverType) || ($approverType !== 'atasan' && empty($approverValue))) {
                Log::warning("Skipping invalid approver step in sequence for requisition ID: {$requisitionId}. Data: " . json_encode($approverStep));
                continue;
            }

            if ($approverType === 'atasan') {
                $approverNik = $requester->atasan_nik;
            } elseif ($approverType === 'nik') {
                 $approverNik = $approverValue;
            } elseif ($approverType === 'role') {
                $user = User::whereHas('roles', function ($q) use ($approverValue) {
                    $q->where('name', $approverValue);
                })->first(); 
                
                if ($user) {
                    $approverNik = $user->nik;
                }
            }

            if ($approverNik) {
                 $logs->push([
                    'requisition_id' => $requisitionId,
                    'approver_nik'   => $approverNik,
                    'status'         => 'Pending',
                    'level'          => $level,
                    'token'          => bin2hex(random_bytes(16)), 
                    'notes'          => null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } else {
                 Log::warning("Approver not found for step '{$approverType}:{$approverValue}' in Requisition ID {$requisitionId}.");
            }
        }

        if ($logs->isNotEmpty()) {
            ApprovalLog::insert($logs->toArray());
        }

        return $logs;
    }
}