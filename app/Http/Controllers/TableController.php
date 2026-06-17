<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Table;
use App\Models\TgUser;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function reserveSeat(Request $request)
    {
        $validated = $request->validate([
            'tableName' => ['required', 'string'],
            'seatNumber' => ['required', 'integer', 'min:1'],
            'tgUserId' => ['required', 'integer'],
        ]);

        $tableName = $validated['tableName'];
        $seatNumber = $validated['seatNumber'];
        $telegramId = $validated['tgUserId'];
        
        $tgUser = TgUser::where('telegram_id', $telegramId)->first();
        if (!$tgUser) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $table = Table::where('name', $tableName)->first();
        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found']);
        }

        if ($seatNumber > $table->seats) {
            return response()->json(['success' => false, 'message' => 'Seat number out of table range']);
        }

        $occupiedByOther = Game::where('table_id', $table->id)
            ->where('seat_number', $seatNumber)
            ->where('tg_user_id', '!=', $tgUser->id)
            ->exists();

        if ($occupiedByOther) {
            return response()->json(['success' => false, 'message' => 'Seat already occupied']);
        }

        // Keep one active seat per user: remove previous reservation before creating a new one.
        Game::where('tg_user_id', $tgUser->id)->delete();

        $game = Game::create([
                'table_id' => $table->id,
                'seat_number' => $seatNumber,
                'tg_user_id' => $tgUser->id,
        ]);

        return response()->json(['success' => true, 'game' => $game, 'photoUrl' => $tgUser->photo_url]);
    }

    public function releaseSeat(Request $request)
    {
        $validated = $request->validate([
            'tableName' => ['required', 'string'],
            'seatNumber' => ['required', 'integer', 'min:1'],
            'tgUserId' => ['required', 'integer'],
        ]);

        $tableName = $validated['tableName'];
        $seatNumber = $validated['seatNumber'];
        $telegramId = $validated['tgUserId'];

        $tgUser = TgUser::where('telegram_id', $telegramId)->first();
        if (!$tgUser) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $table = Table::where('name', $tableName)->first();

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found']);
        }

        $deleted = Game::where('table_id', $table->id)
            ->where('seat_number', $seatNumber)
            ->where('tg_user_id', $tgUser->id)
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Seat not occupied by this user']);
        }

        return response()->json(['success' => true]);
    }
}
