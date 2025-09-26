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
    public $payment;
    public $emailType;

    /**
     * Create a new message instance.
     */
    public function __construct(Requisition $requisition, $payment = null, $emailType = 'rejection_warning')
    {
        $this->requisition = $requisition;
        $this->payment = $payment;
        $this->emailType = $emailType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Tentukan subject berdasarkan tipe email
        $subject = match($this->emailType) {
            'rejection_warning' => 'Payment Proof Required - Requisition need payment proof',
            'payment_confirmation' => 'Payment Proof Received',
            default => 'Payment Notification'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.payment-proofer-mail',
            with: [
                'requisition' => $this->requisition,
                'payment' => $this->payment,
                'emailType' => $this->emailType,
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
        $attachments = [];
        
        // Jika email konfirmasi payment dan ada payment data
        if ($this->emailType === 'payment_confirmation' && $this->payment && $this->payment->document_url) {
            try {
                $filePath = storage_path('app/public/' . $this->payment->document_url);
                
                // Pastikan file exists sebelum menambahkan attachment
                if (file_exists($filePath)) {
                    $fileName = 'payment_proof_' . $this->requisition->id . '_' . basename($this->payment->document_url);
                    
                    $attachments[] = Attachment::fromPath($filePath)
                        ->as($fileName)
                        ->withMime('application/octet-stream');
                }
            } catch (\Exception $e) {
                Log::error("Failed to attach payment proof: " . $e->getMessage());
            }
        }

        return $attachments;
    }
}
