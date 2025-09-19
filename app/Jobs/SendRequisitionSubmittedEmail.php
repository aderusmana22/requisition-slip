<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Requisition\Requisition;
use App\Models\User;
use App\Notifications\RequisitionSubmittedNotification;
use Illuminate\Support\Facades\Notification;

class SendRequisitionSubmittedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requester;
    protected $requisition;

    /**
     * Create a new job instance.
     */
    public function __construct(User $requester, Requisition $requisition)
    {
        $this->requester = $requester;
        $this->requisition = $requisition;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Mengirim notifikasi ke requester
        Notification::send($this->requester, new RequisitionSubmittedNotification($this->requisition));
    }
}
