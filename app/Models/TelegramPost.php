<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramPost extends Model
{
    protected $fillable = [
        'title',
        'message',
        'photo',
        'chat_id',
        'scheduled_at',
        'is_sent',
    ];

        public function getPhotoUrlAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset('storage/tg-posts/' . ltrim($value, '/'));
    }
}