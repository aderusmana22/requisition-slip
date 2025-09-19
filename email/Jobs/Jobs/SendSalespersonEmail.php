<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Notifications\NewSalespersonNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendSalespersonEmail implements ShouldQueue
{
    use Queueable;

    protected $salesperson;
    /**
     * Create a new job instance.
     */
    public function __construct($salesperson)
    {
        $this->salesperson = $salesperson;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscribers = Subscriber::all();

        if ($subscribers->isNotEmpty()) {
            return;
            Notification::send($subscribers, new NewSalespersonNotification($this->salesperson));
        }
    }
}
