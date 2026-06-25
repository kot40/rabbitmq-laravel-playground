<?php

namespace App\Microservices\Handlers;

use App\Microservices\ServiceEvent;
use Illuminate\Support\Facades\Log;

class OrderServiceHandler
{
    // Получили ответ от Invoice Service
    public function handleInvoice(ServiceEvent $event): void
    {
        $data = $event->payload;

        Log::info("[ORDER SERVICE] ✅ Инвойс получен для заказа #{$data['order_id']}", [
            'invoice_number' => $data['invoice_number'],
            'invoice_url'    => $data['invoice_url'],
            'amount'         => $data['amount'],
        ]);

        // В реальности: order->update(['invoice_number' => ..., 'status' => 'invoiced'])
    }

    // Получили ответ от Delivery Service
    public function handleDelivery(ServiceEvent $event): void
    {
        $data = $event->payload;

        Log::info("[ORDER SERVICE] ✅ Доставка запланирована для заказа #{$data['order_id']}", [
            'tracking_number' => $data['tracking_number'],
            'estimated_date'  => $data['estimated_date'],
        ]);

        // В реальности: order->update(['tracking_number' => ..., 'status' => 'shipped'])
    }
}
