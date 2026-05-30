<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function getPhotoUrlAttribute(string $value): ?string
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
