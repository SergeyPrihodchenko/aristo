<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TgUser extends Model
{
    protected $fillable = [
        'telegram_id',
        'first_name',
        'last_name',
        'username',
        'photo_url',
    ];

    protected $casts = [
        'telegram_id' => 'integer',
    ];

    public function getPhotoUrlAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset('storage/' . ltrim($value, '/'));
    }

    public function game()
    {
        return $this->hasOne(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'telegram_user_id');
    }

    public function blockUser(): BelongsTo
    {
        return $this->belongsTo(BlockUser::class, 'tg_user_id');
    }
}
