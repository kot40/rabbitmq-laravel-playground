<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\ProcessOrderForEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderEmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        ProcessOrderForEmail::dispatch($event->order);
        Log::info("[LISTENER] Email listener → заказ #{$event->order['id']}");
    }
}
