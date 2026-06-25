<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TopicOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $routingKey,
        public array $order,
    ) {}

    public function handle(): void
    {
        $queue = $this->job?->getQueue() ?? 'unknown';
        Log::info("[TOPIC] Consumer на очереди [{$queue}] получил событие [{$this->routingKey}]", [
            'order' => $this->order,
        ]);

        echo "[TOPIC] Queue: {$queue} | RoutingKey: {$this->routingKey} | Order: #{$this->order['id']}\n";
    }
}
