<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Requisition\Requisition;

class RequisitionSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $requisition;

    /**
     * Create a new notification instance.
     */
    public function __construct(Requisition $requisition)
    {
        $this->requisition = $requisition;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Anda bisa membuat link untuk melihat detail requisition jika ada halamannya
        // $viewUrl = route('requisition.show', $this->requisition->id);

        return (new MailMessage)
                    ->subject('Konfirmasi Pengajuan Sample Requisition: ' . $this->requisition->no_srs)
                    ->markdown('emails.requester_submitted', [
                        'requisition' => $this->requisition,
                        'requesterName' => $notifiable->name,
                        // 'viewUrl' => $viewUrl
                    ]);
    }
}
