<?php

namespace App\Microservices\Handlers;

use App\Microservices\ServiceEvent;
use Illuminate\Support\Facades\Log;

class DeliveryServiceHandler
{
    public function handle(ServiceEvent $event): void
    {
        $order = $event->payload;

        Log::info("[DELIVERY SERVICE] Получен заказ #{$order['id']} — планирую доставку...", [
            'product' => $order['product'],
            'address' => $order['address'],
        ]);

        // Имитация создания задачи доставки (в реальности: запись в БД, передача в курьерскую службу)
        sleep(1);

        $trackingNumber  = 'TRK-' . rand(1000000, 9999999);
        $estimatedDate   = now()->addDays(3)->toDateString();

        Log::info("[DELIVERY SERVICE] Доставка запланирована {$trackingNumber} → публикую delivery.scheduled");

        // Публикуем ответное событие обратно в Order Service
        ServiceEvent::dispatch(
            source:  'delivery',
            type:    'delivery.scheduled',
            payload: [
                'order_id'       => $order['id'],
                'tracking_number' => $trackingNumber,
                'estimated_date'  => $estimatedDate,
                'address'         => $order['address'],
            ],
        )
        ->onConnection('rabbitmq-ms-delivery')
        ->onQueue('delivery.scheduled');
    }
}
