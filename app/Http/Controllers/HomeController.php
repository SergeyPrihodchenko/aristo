<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Optional;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $tables = \App\Models\Table::all();
        
        $occupiedSeats = \App\Models\Game::with('tgUser', 'table')->get()->map(function($game) {
            return [
                'tableName' => optional($game->table)->name,
                'seatNumber' => $game->seat,
                'photoUrl' => optional($game->tgUser)->photo_url,
                'user_id' => optional($game->tgUser)->id,
            ];
        });
        
        return Inertia::render('Welcome', [
            'tableOptions' => $tables->map(fn($table) => [
                'id' => $table->id,
                'name' => $table->name,
                'seats' => $table->seats,
            ]),
            'occupiedSeats' => $occupiedSeats,
            'userAgent' => $userAgent
        ]);
    }
}