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

class RequisitionApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The requisition instance.
     *
     * @var \App\Models\Requisition\Requisition
     */
    public $requisition;

    /**
     * The approver instance.
     *
     * @var \App\Models\User
     */
    public $approver;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Requisition\Requisition  $requisition
     * @param  \App\Models\User  $approver
     * @return void
     */
    public function __construct(Requisition $requisition, User $approver)
    {
        $this->requisition = $requisition;
        $this->approver = $approver;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Permintaan Persetujuan Requisition Baru: ' . $this->requisition->no_srs,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.mail-sample',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
