<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

class MicroservicesSetupCommand extends Command
{
    protected $signature   = 'ms:setup';
    protected $description = 'Объявляет exchanges, очереди и биндинги для микросервисной архитектуры';

    private function connection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            host:     config('queue.connections.rabbitmq.hosts.0.host'),
            port:     config('queue.connections.rabbitmq.hosts.0.port'),
            user:     config('queue.connections.rabbitmq.hosts.0.user'),
            password: config('queue.connections.rabbitmq.hosts.0.password'),
            vhost:    config('queue.connections.rabbitmq.hosts.0.vhost'),
        );
    }

    public function handle(): void
    {
        $conn    = $this->connection();
        $channel = $conn->channel();

        // ── Exchanges ──────────────────────────────────────────────
        $exchanges = ['ms.orders', 'ms.invoices', 'ms.delivery'];

        $this->info('Exchanges:');
        foreach ($exchanges as $exchange) {
            $channel->exchange_declare($exchange, 'topic', false, true, false);
            $this->line("  <info>✓</info> {$exchange} (topic)");
        }

        $this->newLine();

        // ── Очереди + биндинги ────────────────────────────────────
        // [queue, exchange, routing_key_pattern, description]
        $bindings = [
            ['ms.invoices.incoming',       'ms.orders',   'order.#',    'Invoice Service ← все события заказов'],
            ['ms.delivery.incoming',       'ms.orders',   'order.#',    'Delivery Service ← все события заказов'],
            ['ms.orders.invoice-updates',  'ms.invoices', 'invoice.#',  'Order Service ← все события инвойсов'],
            ['ms.orders.delivery-updates', 'ms.delivery', 'delivery.#', 'Order Service ← все события доставки'],
        ];

        $this->info('Queues & Bindings:');
        foreach ($bindings as [$queue, $exchange, $pattern, $desc]) {
            $channel->queue_declare($queue, false, true, false, false, false, new AMQPTable(['x-queue-type' => 'classic']));
            $channel->queue_bind($queue, $exchange, $pattern);
            $this->line("  <info>✓</info> [{$queue}]");
            $this->line("      ← exchange [{$exchange}] pattern [<comment>{$pattern}</comment>] — {$desc}");
        }

        $channel->close();
        $conn->close();

        $this->newLine();
        $this->info('Готово! Запусти Horizon и создай тестовый заказ:');
        $this->line('  php artisan horizon');
        $this->line('  php artisan ms:order');
    }
}
