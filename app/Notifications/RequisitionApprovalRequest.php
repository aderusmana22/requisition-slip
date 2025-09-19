<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\ApprovalLog;

class RequisitionApprovalRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $requisition;
    protected $approvalLog;

    public function __construct(Requisition $requisition, ApprovalLog $approvalLog)
    {
        $this->requisition = $requisition;
        $this->approvalLog = $approvalLog;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $approveUrl = route('requisition.approve', ['token' => $this->approvalLog->token]);
        $rejectUrl = route('requisition.reject.form', ['token' => $this->approvalLog->token]);

        return (new MailMessage)
                    ->subject('Permintaan Persetujuan Requisition: ' . $this->requisition->no_srs)
                    ->markdown('emails.requisition_approval', [
                        'requisition' => $this->requisition,
                        'approveUrl' => $approveUrl,
                        'rejectUrl' => $rejectUrl,
                        'approverName' => $notifiable->name // Mengambil nama approver
                    ]);
    }
}
