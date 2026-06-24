<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderForAnalytics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $order;

    public function __construct(array $order)
    {
        $this->order = $order;
        $this->onQueue('analytics');
    }

    public function handle()
    {
        Log::info("[ANALYTICS] 📊 Заказ #{$this->order['id']} сохранён в аналитику");
    }

}