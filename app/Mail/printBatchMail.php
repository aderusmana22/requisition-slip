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

class printBatchMail extends Mailable
{
    use Queueable, SerializesModels;

    public $approver;
    public $requisition;
    public $approvalLog;
    public $quickOkLink;
    public $okWithReviewLink;

    /**
     * Create a new message instance.
     */
    public function __construct(User $approver, Requisition $requisition, ApprovalLog $approvalLog, $quickOkLink, $okWithReviewLink)
    {
        $this->approver = $approver;
        $this->requisition = $requisition;
        $this->approvalLog = $approvalLog;
        $this->quickOkLink = $quickOkLink;
        $this->okWithReviewLink = $okWithReviewLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $levelDescription = $this->getLevelDescription($this->approvalLog->level);
        
        return new Envelope(
            subject: 'Warehouse Approval Required - ' . $levelDescription . ': ' . $this->requisition->no_srs,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.print-batch-mail',
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

    /**
     * Get level description for warehouse approval
     */
    private function getLevelDescription($level)
    {
        switch ($level) {
            case 100:
                return 'WH Supervisor - Initial Check';
            case 101:
                return 'Material Supervisor - Material Review';
            case 102:
                return 'WH Supervisor - Final Approval';
            default:
                return 'Warehouse Level ' . $level;
        }
    }
}