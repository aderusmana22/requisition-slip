<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\ApprovalLog;
use App\Models\User;
use App\Notifications\RequisitionApprovalRequest;
use Illuminate\Support\Facades\Notification;

class SendRequisitionApprovalEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $approver;
    protected $requisition;
    protected $approvalLog;

    /**
     * Create a new job instance.
     */
    public function __construct(User $approver, Requisition $requisition, ApprovalLog $approvalLog)
    {
        $this->approver = $approver;
        $this->requisition = $requisition;
        $this->approvalLog = $approvalLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Mengirim notifikasi ke approver yang dituju
        Notification::send($this->approver, new RequisitionApprovalRequest($this->requisition, $this->approvalLog));
    }
}
