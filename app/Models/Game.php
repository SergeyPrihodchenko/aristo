<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'tg_user_id',
        'table_id',
        'seat_number',
    ];

    public function tgUser()
    {
        return $this->belongsTo(TgUser::class, 'tg_user_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
