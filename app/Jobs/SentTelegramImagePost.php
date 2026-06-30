<?php

namespace App\Jobs;

use App\Models\TelegramPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SentTelegramImagePost implements ShouldQueue
{
    use Queueable;

    private TelegramPost $telegramPost;

    /**
     * Create a new job instance.
     */
    public function __construct(TelegramPost $telegramPost)
    {
        $this->telegramPost = $telegramPost;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(!$this->telegramPost->is_sent) {
            // Send the post to Telegram
            $this->sendToTelegram($this->telegramPost);
        } else {
            $this->telegramPost->update(['is_sent' => false]);
            $this->telegramPost->save();
        }
    }

    private function sendToTelegram(TelegramPost $post): void
    {
        $url = config('services.tgUrlPhotoPost');
        $response = Http::withOptions([
            'proxy' => config('services.proxy')
        ])
        ->attach(
            'photo',
            fopen($post->photo, 'r'),
            basename($post->photo)
        )->post($url, [
            'chat_id' => $post->chat_id,
            'caption' => $post->message,
            'parse_mode' => 'HTML',
        ]);

        if ($response->successful()) {
            Log::info('Telegram post sent successfully', [
                'post_id' => $post->id,
                'response' => $response->body(),
            ]);
        } else {
            // Handle the error, e.g., log it or retry later
            Log::error('Failed to send Telegram post', [
                'post_id' => $post->id,
                'response' => $response->body(),
            ]);
        }
    }
}
