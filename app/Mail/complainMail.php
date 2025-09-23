<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\ApprovalLog;
use App\Models\User;

class complainMail extends Mailable
{
    use Queueable, SerializesModels;

    public $approver;
    public $requisition;
    public $approvalLog;
    public $approveLink;
    public $approveWithReviewLink;
    public $rejectLink;

    /**
     * Create a new message instance.
     */
    public function __construct(User $approver, Requisition $requisition, ApprovalLog $approvalLog, $approveLink, $approveWithReviewLink, $rejectLink)
    {
        $this->approver = $approver;
        $this->requisition = $requisition;
        $this->approvalLog = $approvalLog;
        $this->approveLink = $approveLink;
        $this->approveWithReviewLink = $approveWithReviewLink;
        $this->rejectLink = $rejectLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Persetujuan Requisition Complain: ' . $this->requisition->no_srs,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.complain-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
