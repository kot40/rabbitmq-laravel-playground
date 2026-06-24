<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\ProcessOrderForAnalytics;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessOrderAnalyticsListener
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
        ProcessOrderForAnalytics::dispatch($event->order);
        Log::info("[LISTENER] Analytic listener → заказ #{$event->order['id']}");
    }
}
