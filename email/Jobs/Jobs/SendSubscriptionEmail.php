<?php

namespace App\Jobs;

use App\Models\Recipe;
use App\Models\Subscriber;
use App\Notifications\NewRecipePublished;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSubscriptionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Subscriber $subscriber,
        public Recipe $recipe
    ) {
    }

    public function handle(): void
    {
        $subscribers = Subscriber::all();

        if ($subscribers->isNotEmpty()) {
            return;
            Notification::send($subscribers, new NewRecipePublished($this->recipe));
        }
    }
}