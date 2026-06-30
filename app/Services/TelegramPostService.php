<?php

namespace App\Services;

use App\Models\TelegramPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramPostService
{
    public function send(TelegramPost $post): bool
    {
        $url = config('services.tgUrlPhotoPost');

        $response = Http::withOptions([
            'proxy' => config('services.proxy'),
        ])
        ->attach(
            'photo',
            fopen($post->photo, 'r'),
            basename($post->photo)
        )
        ->post($url, [
            'chat_id' => $post->chat_id,
            'caption' => $post->message,
            'parse_mode' => 'HTML',
        ]);

        if ($response->successful()) {

            $post->update([
                'last_sent_at' => now(),
            ]);

            Log::info('Telegram post sent successfully', [
                'post_id' => $post->id,
            ]);

            return true;
        }

        Log::error('Failed to send Telegram post', [
            'post_id' => $post->id,
            'response' => $response->body(),
        ]);

        return false;
    }

    public function isDue(TelegramPost $post): bool
    {
        $now = now();

        return match ($post->schedule_type) {

            'once' =>
                !$post->last_sent_at &&
                $post->scheduled_at <= $now,

            'daily' =>
                $post->publish_time->format('H:i') === $now->format('H:i'),

            'weekly' =>
                $post->weekday == $now->dayOfWeekIso &&
                $post->publish_time->format('H:i') === $now->format('H:i'),

            'monthly' =>
                $post->day_of_month == $now->day &&
                $post->publish_time->format('H:i') === $now->format('H:i'),

            default => false,
        };
    }
}