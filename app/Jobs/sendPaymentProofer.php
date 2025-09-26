<?php

namespace App\Jobs;

use App\Mail\paymentProoferMail;
use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendPaymentProofer implements ShouldQueue
{
    use Queueable;
    protected Requisition $requisition;
    protected $payment;
    protected $emailType;

    /**
     * Create a new job instance.
     */
    public function __construct(Requisition $requisition, $payment = null, $emailType = 'rejection_warning')
    {
        $this->requisition = $requisition;
        $this->payment = $payment;
        $this->emailType = $emailType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $requester = User::where('nik', $this->requisition->requester_nik)->first();
            
            if (!$requester) {
                Log::error("Requester not found for NIK: {$this->requisition->requester_nik}");
                return;
            }

            // menentukan jenis email
            if ($this->payment === null && $this->emailType === 'rejection_warning') {
                // Kirim email peringatan rejection
                Log::info("Sending rejection warning email to: {$requester->email} for requisition: {$this->requisition->id}");
                Mail::to($requester->email)->send(new paymentProoferMail($this->requisition, null, 'rejection_warning'));
            } elseif ($this->payment !== null && $this->emailType === 'payment_confirmation') {
                // Kirim email konfirmasi payment dengan attachment
                Log::info("Sending payment confirmation email to: {$requester->email} for requisition: {$this->requisition->id}");
                Mail::to($requester->email)->send(new paymentProoferMail($this->requisition, $this->payment, 'payment_confirmation'));
            } else {
                Log::warning("Invalid email type or payment combination for requisition: {$this->requisition->id}");
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to send payment proofer email: " . $e->getMessage());
            throw $e;
        }
    }
}
