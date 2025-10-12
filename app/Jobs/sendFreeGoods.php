<?php

namespace App\Jobs;

use App\Mail\MailFreeGoods; // Akan dibuat
use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class sendFreeGoods implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $requisition;
    public $recipient;
    public $token;
    public $data;

    public function __construct(Requisition $requisition, User $recipient, $token = null, array $data = [])
    {
        $this->requisition = $requisition;
        $this->recipient = $recipient;
        $this->token = $token;
        $this->data = $data;
    }

    public function handle()
    {
        // Masukkan token dan requisition ke dalam data untuk digunakan di Mailer
        $this->data['token'] = $this->token;
        
        Mail::to($this->recipient->email)
            ->send(new MailFreeGoods($this->requisition, $this->recipient, $this->data));
    }
}