<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'name',
        'seats',
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
