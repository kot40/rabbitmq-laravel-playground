<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\SendOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderNotificationListener
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
        SendOrderNotification::dispatch($event->order);
        Log::info("[LISTENER] Notification listener → заказ #{$event->order['id']}");
    }
}
