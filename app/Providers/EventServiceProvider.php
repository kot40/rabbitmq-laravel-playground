<?php

namespace App\Providers;

use App\Listeners\ProcessOrderAnalyticsListener;
use App\Listeners\SendOrderEmailListener;
use App\Listeners\SendOrderNotificationListener;
use Illuminate\Support\ServiceProvider;
use App\Events\OrderCreated;

class EventServiceProvider extends ServiceProvider
{
    protected array $listen = [
        OrderCreated::class => [
            SendOrderEmailListener::class,
            SendOrderNotificationListener::class,
            ProcessOrderAnalyticsListener::class,
        ],
    ];
    
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
