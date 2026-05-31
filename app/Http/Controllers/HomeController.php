<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $tables = \App\Models\Table::all();
        $occupiedSeats = \App\Models\Game::with('tgUser', 'table')->get()->map(function($game) {
            return [
                'tableName' => $game->table->name,
                'seatNumber' => $game->seat,
                'photoUrl' => $game->tgUser->photo_url,
                'user_id' => $game->tgUser->id,
            ];
        });
        return Inertia('Welcome', [
            'tableOptions' => $tables->map(fn($table) => [
                'id' => $table->id,
                'name' => $table->name,
                'seats' => $table->seats,
            ]),
            'occupiedSeats' => $occupiedSeats,
        ]);
    }
}
