<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;

        $this->onQueue('emails');
    }

    public function handle()
    {
        Log::info("[EMAILS] Отправляем письмо на: {$this->email}");
        sleep(3); // имитация работы
    }

}