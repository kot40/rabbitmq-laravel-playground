<?php

namespace App\Microservices\Handlers;

use App\Microservices\ServiceEvent;
use Illuminate\Support\Facades\Log;

class InvoiceServiceHandler
{
    public function handle(ServiceEvent $event): void
    {
        $order = $event->payload;

        Log::info("[INVOICE SERVICE] Получен заказ #{$order['id']} — создаю инвойс...", [
            'product' => $order['product'],
            'price'   => $order['price'],
        ]);

        // Имитация создания инвойса (в реальности: сохранение в БД, генерация PDF)
        sleep(1);

        $invoiceNumber = 'INV-' . strtoupper(substr(md5($order['id']), 0, 8));
        $invoiceUrl    = "https://invoices.example.com/{$invoiceNumber}.pdf";

        Log::info("[INVOICE SERVICE] Инвойс {$invoiceNumber} создан → публикую invoice.created");

        // Публикуем ответное событие обратно в Order Service
        ServiceEvent::dispatch(
            source:  'invoices',
            type:    'invoice.created',
            payload: [
                'order_id'       => $order['id'],
                'invoice_number' => $invoiceNumber,
                'invoice_url'    => $invoiceUrl,
                'amount'         => $order['price'],
            ],
        )
        ->onConnection('rabbitmq-ms-invoices')
        ->onQueue('invoice.created');
    }
}
