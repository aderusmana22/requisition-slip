<?php

namespace App\Jobs;

use App\Mail\mailSample;
use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendSample implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requisition;
    protected $approver;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Requisition\Requisition $requisition
     * @param \App\Models\User $approver
     * @return void
     */
    public function __construct(Requisition $requisition, User $approver, $token)
    {
        $this->requisition = $requisition;
        $this->approver = $approver;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $approveUrl = route('approval.response', ['token' => $this->token, 'action' => 'approve']);
            $reviewUrl  = route('approval.response', ['token' => $this->token, 'action' => 'review']);
            $rejectUrl  = route('approval.response', ['token' => $this->token, 'action' => 'reject']);

            $data = [
                'requisition'   => $this->requisition,
                'approver'      => $this->approver,
                'token'         => $this->token,
                'approve_url'   => $approveUrl,
                'review_url'    => $reviewUrl,
                'reject_url'    => $rejectUrl,
            ];

            Mail::to($this->approver->email)->send(new mailSample($this->requisition, $this->approver, $data));

            Log::info("Email approval untuk Requisition #{$this->requisition->id} berhasil dikirim ke {$this->approver->email}.");

        } catch (\Exception $e) {
            Log::error("Gagal mengirim email approval untuk Requisition #{$this->requisition->id}. Error: " . $e->getMessage());
        }
    }
}
