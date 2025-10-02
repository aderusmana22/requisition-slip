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
    protected $recipient;
    protected $token;
    protected $mailData;

    /**
     * Create a new job instance.
     * Konstruktor diubah agar lebih fleksibel, menerima array data.
     *
     * @param \App\Models\Requisition\Requisition $requisition
     * @param \App\Models\User $recipient Penerima email
     * @param string|null $token Token untuk aksi (bisa null untuk notifikasi)
     * @param array $mailData Data tambahan untuk email
     * @return void
     */
    public function __construct(Requisition $requisition, User $recipient, ?string $token, array $mailData = [])
    {
        $this->requisition = $requisition;
        $this->recipient = $recipient;
        $this->token = $token;
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Tentukan tipe email dari data yang dikirim, defaultnya 'approval'
            $mailType = $this->mailData['mail_type'] ?? 'approval';

            $dataForMail = $this->mailData;

            // Siapkan URL aksi berdasarkan tipe email
            if ($mailType === 'approval') {
                $dataForMail['approve_url'] = route('approval.response', ['token' => $this->token, 'action' => 'approve']);
                $dataForMail['review_url']  = route('approval.response', ['token' => $this->token, 'action' => 'review']);
                $dataForMail['reject_url']  = route('approval.response', ['token' => $this->token, 'action' => 'reject']);
            } elseif ($mailType === 'warehouse_process') {
                $dataForMail['submit_url'] = route('approval.response', ['token' => $this->token, 'action' => 'submit']);
                $dataForMail['review_url'] = route('approval.response', ['token' => $this->token, 'action' => 'review']);
            }

            // Kirim email menggunakan Mailable cerdas yang sudah kita buat
            Mail::to($this->recipient->email)->send(new mailSample($this->requisition, $this->recipient, $dataForMail));

            Log::info("Email (Tipe: {$mailType}) untuk Requisition #{$this->requisition->id} berhasil dikirim ke {$this->recipient->email}.");

        } catch (\Exception $e) {
            Log::error("Gagal mengirim email untuk Requisition #{$this->requisition->id}. Error: " . $e->getMessage() . " on line " . $e->getLine());
        }
    }
}
