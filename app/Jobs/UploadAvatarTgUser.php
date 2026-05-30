<?php

namespace App\Jobs;

use App\Models\TgUser;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadAvatarTgUser implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    private int $telegramUserId;

    public function __construct(
        int $telegramUserId
    )
    {
        $this->telegramUserId = $telegramUserId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
        $proxyUrl = env('PROXY_URL');
        $response = Http::withOptions([
            'proxy' => $proxyUrl
        ])->get(
            "https://api.telegram.org/bot{$telegramBotToken}/getUserProfilePhotos",
            [
                'user_id' => $this->telegramUserId,
                'limit' => 1,
            ]
        );
        if ($response->successful()) {
            $data = $response->json();
            // Process the data as needed
            // For example, you can save the avatar URL to the database
            if (isset($data['result']['photos'][0][0]['file_id'])) {
                $fileId = $data['result']['photos'][0][0]['file_id'];
                // You can now use this file ID to get the file path or download the avatar
                // For example, you can call getFile method to get the file path
                $fileResponse = Http::withOptions([
                    'proxy' => $proxyUrl
                ])->get(
                    "https://api.telegram.org/bot{$telegramBotToken}/getFile",
                    [
                        'file_id' => $fileId,
                    ]
                );
                if ($fileResponse->successful()) {
                    $fileData = $fileResponse->json();
                    if (isset($fileData['result']['file_path'])) {
                        $avatarUrl = "https://api.telegram.org/file/bot{$telegramBotToken}/{$fileData['result']['file_path']}";
                        Storage::disk('public')->put("avatars/{$this->telegramUserId}.jpg", file_get_contents($avatarUrl));
                        TgUser::where('telegram_id', $this->telegramUserId)->update(['photo_url' => "avatars/{$this->telegramUserId}.jpg"]);
                    }
                } else {
                    Log::error("Failed to fetch file path for Telegram user ID {$this->telegramUserId}: " . $fileResponse->body());
                }
            } else {
                Log::info("No profile photos found for Telegram user ID {$this->telegramUserId}");
            }
        } else {
            Log::error("Failed to fetch user profile photos for Telegram user ID {$this->telegramUserId}: " . $response->body());
        }
    }
}
