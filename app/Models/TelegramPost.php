<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramPost extends Model
{
    protected $fillable = [
        'title',
        'message',
        'photo',

        'schedule_type',
        'weekday',
        'day_of_month',
        'publish_time',
        'scheduled_at',

        'last_sent_at',
        'is_active',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'publish_time' => 'datetime:H:i',
        'is_active' => 'boolean',
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