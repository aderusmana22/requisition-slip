<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Notifications\NewDistributorNotification;
use App\Notifications\NewSalespersonNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendDistributorEmail implements ShouldQueue
{
    use Queueable;

    protected $distributor;
    /**
     * Create a new job instance.
     */
    public function __construct($distributor)
    {
        $this->distributor = $distributor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscribers = Subscriber::all();

        if ($subscribers->isNotEmpty()) {
            return;
            Notification::send($subscribers, new NewDistributorNotification($this->distributor));
        }
    }
}
