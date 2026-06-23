<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $userId,
        public string $message,
        public string $type = 'push'   // по умолчанию push
    ) {
        // Конструктор пустой — свойства автоматически присваиваются
        $this->onQueue('notifications');
    }

    public function handle()
    {
        // Здесь будет реальная логика отправки уведомления
        Log::info("[NOTIFICATIONS] [{$this->type}] Пользователю {$this->userId}: {$this->message}");

        // Пример имитации работы
        sleep(3);

        // TODO: отправка через Firebase, OneSignal, Twilio и т.д.
    }

}
