<?php

namespace App\Console\Commands;

use App\Models\TelegramPost;
use Illuminate\Console\Command;
use App\Services\TelegramPostService;

class SendScheduledTelegramPosts extends Command
{
    protected $signature = 'telegram:send-scheduled';

    protected $description = 'Отправка запланированных Telegram-публикаций';

    public function handle(TelegramPostService $telegramService): int
    {
        $now = now();

        $posts = TelegramPost::query()
            ->where('is_active', true)
            ->where(function ($query) use ($now) {

                // Разовая публикация
                $query->where(function ($q) use ($now) {
                    $q->where('schedule_type', 'once')
                    ->where('scheduled_at', '<=', $now)
                    ->whereNull('last_sent_at');
                })

                // Ежедневная
                ->orWhere(function ($q) use ($now) {
                    $q->where('schedule_type', 'daily')
                    ->whereTime('publish_time', $now->format('H:i:00'));
                })

                // Еженедельная
                ->orWhere(function ($q) use ($now) {
                    $q->where('schedule_type', 'weekly')
                    ->where('weekday', $now->dayOfWeekIso)
                    ->whereTime('publish_time', $now->format('H:i:00'));
                })

                // Ежемесячная
                ->orWhere(function ($q) use ($now) {
                    $q->where('schedule_type', 'monthly')
                    ->where('day_of_month', $now->day)
                    ->whereTime('publish_time', $now->format('H:i:00'));
                });
            })
            ->get();

        foreach ($posts as $post) {
            if ($telegramService->isDue($post)) {
                $telegramService->send($post);
            }
        }

        return self::SUCCESS;
    }
}