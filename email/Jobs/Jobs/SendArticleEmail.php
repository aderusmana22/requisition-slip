<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Notifications\NewArticlePublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendArticleEmail implements ShouldQueue
{
    use Queueable;

    protected $articles;
    /**
     * Create a new job instance.
     */
    public function __construct($articles)
    {
        $this->articles = $articles;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscribers = Subscriber::all();

        if ($subscribers->isNotEmpty()) {
            Notification::send($subscribers, new NewArticlePublished($this->articles));
        }
    }
}
