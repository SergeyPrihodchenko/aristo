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
        $posts = TelegramPost::query()
            ->where('is_active', true)
            ->get();

        foreach ($posts as $post) {

            if (! $telegramService->isDue($post)) {
                continue;
            }

            $telegramService->send($post);
        }

        return self::SUCCESS;
    }
}