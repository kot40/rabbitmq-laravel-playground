<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

class TopicExchangeSetupCommand extends Command
{
    protected $signature = 'topic:setup';
    protected $description = 'Объявляет topic exchange и очереди с wildcard-биндингами';

    // exchange → routing pattern → queue name
    private array $bindings = [
        'order.europe.*' => 'topic.orders.europe',  // только европейские события
        'order.*.new'    => 'topic.orders.new',      // только новые заказы (любой регион)
        'order.#'        => 'topic.orders.all',      // вообще все события по заказам
    ];

    public function handle(): void
    {
        $connection = new AMQPStreamConnection(
            host: config('queue.connections.rabbitmq.hosts.0.host'),
            port: config('queue.connections.rabbitmq.hosts.0.port'),
            user: config('queue.connections.rabbitmq.hosts.0.user'),
            password: config('queue.connections.rabbitmq.hosts.0.password'),
            vhost: config('queue.connections.rabbitmq.hosts.0.vhost'),
        );

        $channel = $connection->channel();

        // Используем тот же exchange что в config/queue.php → options.exchange.name
        $exchange = config('queue.connections.rabbitmq.options.exchange.name', 'events');

        // Объявляем topic exchange
        $channel->exchange_declare(
            exchange: $exchange,
            type: 'topic',
            passive: false,
            durable: true,
            auto_delete: false,
        );
        $this->info("Exchange [{$exchange}] (topic) объявлен");

        // Объявляем очереди и биндим с wildcard routing key
        foreach ($this->bindings as $pattern => $queueName) {
            $channel->queue_declare(
                queue: $queueName,
                passive: false,
                durable: true,
                exclusive: false,
                auto_delete: false,
                nowait: false,
                arguments: new AMQPTable(['x-queue-type' => 'classic']),
            );

            $channel->queue_bind(
                queue: $queueName,
                exchange: $exchange,
                routing_key: $pattern,
            );

            $this->line("  Queue [{$queueName}] <comment>←</comment> pattern [<info>{$pattern}</info>]");
        }

        $channel->close();
        $connection->close();

        $this->newLine();
        $this->info('Готово! Теперь запусти 3 consumer-а в трёх терминалах:');
        $this->line('  php artisan queue:work rabbitmq --queue=topic.orders.europe');
        $this->line('  php artisan queue:work rabbitmq --queue=topic.orders.new');
        $this->line('  php artisan queue:work rabbitmq --queue=topic.orders.all');
        $this->newLine();
        $this->info('Потом отправь тестовые сообщения:');
        $this->line('  php artisan topic:publish');
    }
}
