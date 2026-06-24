<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderForEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $order;

    public function __construct(array $order)
    {
        $this->order = $order;
        $this->onQueue('emails');                    // очередь
    }

    public function handle()
    {
        Log::info("[EMAIL] ✅ Отправка письма по заказу #{$this->order['id']}");
    }

}