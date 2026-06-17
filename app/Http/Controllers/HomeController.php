<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $tables = \App\Models\Table::all();
        
        $occupiedSeats = \App\Models\Game::with('tgUser', 'table')->get()->map(function($game) {
            return [
                'tableName' => optional($game->table)->name,
                'seatNumber' => $game->seat_number,
                'photoUrl' => optional($game->tgUser)->photo_url,
                'userId' => optional($game->tgUser)->id,
                'telegramId' => optional($game->tgUser)->telegram_id,
            ];
        });
        
        return Inertia::render('Welcome', [
            'tableOptions' => $tables->map(fn($table) => [
                'id' => $table->id,
                'name' => $table->name,
                'seats' => $table->seats,
            ]),
            'occupiedSeats' => $occupiedSeats,
        ]);
    }
}