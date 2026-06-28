<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    protected $table = 'block_users';

    protected $fillable = [
        'tg_user_id',
    ];

    public function tgUser()
    {
        return $this->belongsTo(TgUser::class, 'tg_user_id');
    }
}
