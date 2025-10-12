<?php

namespace App\Mail;

use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class MailFreeGoods extends Mailable
{
    use Queueable, SerializesModels;

    public $requisition;
    public $recipient;
    public $data;

    public function __construct(Requisition $requisition, User $recipient, array $data = [])
    {
        $this->requisition = $requisition;
        $this->recipient = $recipient;
        $this->data = $data;
        $this->data['recipient'] = $recipient;
    }

    public function envelope()
    {
        $subject = 'Action Required: Free Goods Requisition ' . $this->requisition->no_srs;
        $mailType = $this->data['mail_type'] ?? 'approval';

        if ($mailType === 'warehouse_process') {
            $step = $this->data['process_step'] ?? 'Warehouse Process';
            $subject = "Warehouse Process: {$step} for FG: {$this->requisition->no_srs}";
        } elseif ($mailType === 'completed_notification') {
            $subject = 'Completed: Your Free Goods Requisition ' . $this->requisition->no_srs . ' is Ready';
        }

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $subject,
        );
    }

    public function content()
    {
        return new Content(
            // View email yang akan dibuat
            view: 'mail.mail-freegoods', 
            with: $this->data
        );
    }

    public function attachments()
    {
        return [];
    }
}