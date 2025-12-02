<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ComplainImage;
use App\Models\Requisition\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
            view: 'mail.complain-mail',
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

        $isHeadQA = $this->approver->hasRole('head-QA');
        Log::info('Checking role for user ' . $this->approver->id . ': isHeadQA = ' . ($isHeadQA ? 'true' : 'false'));

        if ($isHeadQA) {
            Log::info('User is head-QA. Proceeding to find images for Requisition ID: ' . $this->requisition->id);

            $complainImages = ComplainImage::where('requisition_id', $this->requisition->id)->get();

            Log::info('Found ' . $complainImages->count() . ' complaint images.');

            foreach ($complainImages as $index => $image) {
                $imagePath = $image->image_path;

                if (Storage::disk('public')->exists($imagePath)) {
                    $fullPath = Storage::disk('public')->path($imagePath);
                    $fileName = 'complain_' . $this->requisition->no_srs . '_' . ($index + 1);

                    $attachments[] = Attachment::fromPath($fullPath)
                        ->as($fileName)
                        ->withMime('image/jpeg');
                    Log::info('Successfully attached image: ' . $fileName);
                } else {
                    Log::warning('Attachment file not found in storage: ' . $imagePath);
                }
            }
        } else {
            Log::info('User is NOT head-QA. Skipping image attachments.');
        }

        return $attachments;
    }
}
