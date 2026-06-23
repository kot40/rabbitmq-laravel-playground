<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Jobs\SendWelcomeEmail;

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

