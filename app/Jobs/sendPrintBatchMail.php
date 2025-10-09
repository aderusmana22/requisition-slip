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
use Illuminate\Support\Facades\Mail;
use App\Mail\printBatchMail;

class sendPrintBatchMail implements ShouldQueue
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
        // Load requisition dengan relasi yang diperlukan
        $requisitionWithData = Requisition::with(['customer', 'requester', 'requisitionItems.itemMaster'])
            ->find($this->requisition->id);

        $quickOkLink = route('complain.warehouse.approval', [
            'id' => $this->approvalLog->requisition_id,
            'token' => $this->approvalLog->token,
            'status' => 'approve',
        ]);

        $okWithReviewLink = route('complain.warehouse.review', [
            'id' => $this->approvalLog->requisition_id,
            'token' => $this->approvalLog->token,
        ]);

        Mail::to($this->approver->email)->send(new printBatchMail(
            $this->approver,
            $requisitionWithData ?? $this->requisition,
            $this->approvalLog,
            $quickOkLink,
            $okWithReviewLink
        ));
    }
}
