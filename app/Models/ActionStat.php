<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionStat extends Model
{
    protected $fillable = [
        'tg_user_id',
        'entrances',
        'booking',
    ];

    public function tgUser()
    {
        return $this->belongsTo(TgUser::class);
    }
}
