<?php

use App\Jobs\ProcessOrderJob;
use App\Jobs\SendEmailJob;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Jobs\SendWelcomeEmail;
use App\Events\OrderCreated;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/send', function () {
    // Отправляем задачу в очередь RabbitMQ
    SendWelcomeEmail::dispatch('test@example.com');

    return 'Сообщение успешно отправлено в RabbitMQ! Проверь Management UI.';
});


Route::get('/send-many', function () {
    Log::info('=== Начинаем отправку 10 сообщений в RabbitMQ ===');
    
    for ($i = 1; $i <= 10; $i++) {
        \App\Jobs\SendWelcomeEmail::dispatch("user{$i}@example.com");
        Log::info("Отправлено сообщение #{$i}");
    }

    Log::info('=== Отправка завершена ===');

    return 'Отправлено 10 сообщений! Проверь логи и RabbitMQ UI.';
});


Route::get('/send-test', function () {
    try {
        Log::info('=== Тест отправки в RabbitMQ ===');

        SendWelcomeEmail::dispatch('debug@example.com');

        // Дополнительная проверка
        $connection = \Illuminate\Support\Facades\Queue::connection('rabbitmq');
        Log::info('Соединение c RabbitMQ успешно создано');

        return 'Тестовое сообщение отправлено. Проверь логи!';
    } catch (\Exception $e) {
        Log::error('Ошибка при отправке: ' . $e->getMessage());
        return 'Ошибка: ' . $e->getMessage();
    }
});


Route::get('/queue-work', function () {
    // Для теста — лучше запускать в отдельном терминале
    return 'Запусти php artisan queue:work';
});


Route::get('/send-emails', function () {
    for ($i = 1; $i <= 6; $i++) {
        SendEmailJob::dispatch("user{$i}@example.com");
        // SendEmailJob::dispatch("user{$i}@example.com")->onQueue('emails');
    }
    return 'Отправлено 6 email jobs';
});


// the same as above but with named parameters
Route::get('/send-notifications', function () {
    for ($i = 1; $i <= 8; $i++) {
        \App\Jobs\SendNotificationJob::dispatch(
            message: "Ваш заказ #{$i} успешно обработан и готов к отправке!",
            userId: "user{$i}"
        );
    }

    return 'Отправлено 8 уведомлений в очередь notifications';
});


Route::get('/process-orders', function () {
    for ($i = 1; $i <= 3; $i++) {
        ProcessOrderJob::dispatch($i);
    }
    return 'Отправлено 3 order jobs';
});


Route::get('/order-created', function () {
    $order = [
        'id'          => rand(10000, 99999),
        'user_id'     => 42,
        'user_email'  => 'customer@example.com',
        'amount'      => 2990,
        'status'      => 'created',
    ];

    // Одно событие → разлетается на 3 разные очереди!
    event(new OrderCreated($order));

    return '✅ Событие OrderCreated отправлено → должно уйти в 3 очереди';
});
