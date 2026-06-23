<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public array $orderData; // можно передавать массив или Eloquent модель

    public function __construct(int $orderId, array $orderData = [])
    {
        $this->orderId = $orderId;
        $this->orderData = $orderData;

        $this->onQueue('orders');
    }

    public function handle()
    {
        Log::info("[ORDERS] Началась обработка заказа #{$this->orderId}");

        // Имитация тяжёлой работы
        sleep(7);

        // Примеры того, что обычно делают в этой очереди:
        // - Резервирование товаров на складе
        // - Создание задач в ERP / 1C
        // - Отправка в службу доставки
        // - Генерация документов (чек, накладная)
        // - Уведомление бухгалтерии

        Log::info("[ORDERS] Заказ #{$this->orderId} успешно обработан");

        // Если произошла ошибка — можно бросить исключение
        // throw new \Exception('Склад не отвечает');
    }

}