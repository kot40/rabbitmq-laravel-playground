<?php

namespace App\Microservices;

use App\Microservices\Handlers\DeliveryServiceHandler;
use App\Microservices\Handlers\InvoiceServiceHandler;
use App\Microservices\Handlers\OrderServiceHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Единый класс-сообщение для всех микросервисов.
 * В реальном проекте — shared-пакет, доступный каждому сервису.
 *
 * source:  кто опубликовал  (orders | invoices | delivery)
 * type:    routing key      (order.created | invoice.created | delivery.scheduled)
 * payload: данные события
 */
class ServiceEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $source,
        public readonly string $type,
        public readonly array  $payload,
    ) {}

    public function handle(): void
    {
        $queue = $this->job?->getQueue() ?? '';

        match (true) {
            str_starts_with($queue, 'ms.invoices.incoming')       => (new InvoiceServiceHandler)->handle($this),
            str_starts_with($queue, 'ms.delivery.incoming')       => (new DeliveryServiceHandler)->handle($this),
            str_starts_with($queue, 'ms.orders.invoice-updates')  => (new OrderServiceHandler)->handleInvoice($this),
            str_starts_with($queue, 'ms.orders.delivery-updates') => (new OrderServiceHandler)->handleDelivery($this),
            default => logger()->warning("[MS] Неизвестная очередь: {$queue}"),
        };
    }
}
