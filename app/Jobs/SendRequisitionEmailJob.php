<?php

namespace App\Jobs;

use App\Mail\RequisitionApprovalMail;
use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRequisitionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requisition;
    protected $approver;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Requisition\Requisition $requisition
     * @param \App\Models\User $approver
     * @return void
     */
    public function __construct(Requisition $requisition, User $approver)
    {
        $this->requisition = $requisition;
        $this->approver = $approver;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (filter_var($this->approver->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($this->approver->email)
                    ->send(new RequisitionApprovalMail($this->requisition, $this->approver));
                Log::info("Email approval untuk Requisition #{$this->requisition->id} telah dikirim ke {$this->approver->email}.");
            } else {
                Log::warning("Gagal mengirim email: Approver NIK {$this->approver->nik} tidak memiliki alamat email yang valid.");
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengirim email approval untuk Requisition #{$this->requisition->id} ke {$this->approver->email}. Error: " . $e->getMessage());
        }
    }
}
