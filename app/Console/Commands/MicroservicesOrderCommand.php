<?php

namespace App\Console\Commands;

use App\Microservices\ServiceEvent;
use Illuminate\Console\Command;

class MicroservicesOrderCommand extends Command
{
    protected $signature   = 'ms:order {product? : Название товара}';
    protected $description = 'Симулирует создание заказа в Order Service';

    public function handle(): void
    {
        $product = $this->argument('product') ?? 'iPhone 16 Pro';

        $order = [
            'id'       => rand(10000, 99999),
            'product'  => $product,
            'price'    => rand(500, 2000),
            'customer' => 'John Doe',
            'email'    => 'john@example.com',
            'address'  => 'Berlin, Alexanderplatz 1',
        ];

        $this->info("[ORDER SERVICE] Создан заказ #{$order['id']}: {$order['product']} — €{$order['price']}");
        $this->line('  Публикую order.created → exchange: ms.orders');
        $this->newLine();

        // Order Service публикует событие в свой exchange
        // Invoice Service и Delivery Service подписаны — получат автоматически
        ServiceEvent::dispatch(
            source:  'orders',
            type:    'order.created',
            payload: $order,
        )
        ->onConnection('rabbitmq-ms-orders')
        ->onQueue('order.created');

        $this->line('  <info>→</info> ms.invoices.incoming  (Invoice Service обработает)');
        $this->line('  <info>→</info> ms.delivery.incoming  (Delivery Service обработает)');
        $this->newLine();
        $this->line('Смотри логи: <comment>php artisan pail</comment>');
    }
}
