<?php

namespace App\Mail;

use App\Models\Requisition\Requisition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class paymentProoferMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $requisition;
    public $uploadLink;

    public function __construct($requisition, $uploadLink) 
    {
        $this->requisition = $requisition;
        $this->uploadLink = $uploadLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Payment Proof Required - Requisition ' . $this->requisition->no_srs,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.payment-proofer-mail',
            with: [
                'requisition' => $this->requisition,
                'uploadLink' => $this->uploadLink,
            ]
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
