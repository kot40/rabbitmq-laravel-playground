<?php

namespace App\Console\Commands;

use App\Jobs\TopicOrderJob;
use Illuminate\Console\Command;

class TopicPublishCommand extends Command
{
    protected $signature = 'topic:publish';
    protected $description = 'Публикует тестовые события с разными routing keys в topic exchange';

    // routing key (= имя очереди при dispatch) → описание → ожидаемые очереди-получатели
    private array $events = [
        [
            'key'      => 'order.europe.new',
            'label'    => 'Новый заказ из Европы',
            'expected' => ['topic.orders.europe', 'topic.orders.new', 'topic.orders.all'],
        ],
        [
            'key'      => 'order.asia.new',
            'label'    => 'Новый заказ из Азии',
            'expected' => ['topic.orders.new', 'topic.orders.all'],
        ],
        [
            'key'      => 'order.europe.cancel',
            'label'    => 'Отмена заказа из Европы',
            'expected' => ['topic.orders.europe', 'topic.orders.all'],
        ],
        [
            'key'      => 'order.asia.cancel',
            'label'    => 'Отмена заказа из Азии',
            'expected' => ['topic.orders.all'],
        ],
    ];

    public function handle(): void
    {
        $this->info('Публикую события в topic exchange через Laravel dispatch...');
        $this->newLine();

        $orderId = 1000;

        foreach ($this->events as $event) {
            $orderId++;

            // onQueue() → это routing key в topic exchange
            TopicOrderJob::dispatch($event['key'], ['id' => $orderId, 'amount' => rand(100, 9999)])
                ->onConnection('rabbitmq')
                ->onQueue($event['key']);

            $this->line("  <info>→</info> [{$event['key']}] — {$event['label']}");
            $this->line("      Попадёт в: " . implode(', ', array_map(
                fn($q) => "<comment>{$q}</comment>",
                $event['expected']
            )));
        }

        $this->newLine();
        $this->info('Готово! Смотри логи в запущенных consumer-ах.');
    }
}
