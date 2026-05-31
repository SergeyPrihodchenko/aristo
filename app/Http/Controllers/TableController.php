<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableController extends Controller
{
    public function reserveSeat(Request $request)
    {
        $tableName = $request->input('tableName');
        $seatNumber = $request->input('seatNumber');
        $tgUserId = $request->input('tgUserId');
        
        $tgUser = \App\Models\TgUser::where('telegram_id', $tgUserId)->first();
        if (!$tgUser) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $table = \App\Models\Table::where('name', $tableName)->first();
        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found']);
        }

        $tableId = $table->id;
        $tgUserId = $tgUser->id;
        $photoUrl = $tgUser->photo_url;
        $toDayDate = now();

        $game = \App\Models\Game::where('table_id', $tableId)->where('tg_user_id', $tgUserId)->whereDate('created_at', $toDayDate);
        if($game->exists()) {
            $game->first()->update(['seat_number' => $seatNumber]);
            return response()->json(['success' => true, 'game' => $game->first(), 'photoUrl' => $photoUrl]);
        }

        $game = \App\Models\Game::create([
            'table_id' => $tableId,
            'seat_number' => $seatNumber,
            'tg_user_id' => $tgUserId,
        ]);

        return response()->json(['success' => true, 'game' => $game, 'photoUrl' => $photoUrl]);
    }

    public function releaseSeat(Request $request)
    {
        $tableName = $request->input('tableName');
        $seatNumber = $request->input('seatNumber');
        $tgUserId = $request->input('tgUserId');

        $tgUser = \App\Models\TgUser::where('telegram_id', $tgUserId)->first();
        if (!$tgUser) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $table = \App\Models\Table::where('name', $tableName)->first();

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Seat not occupied by this user']);
        }

        $tableId = $table->id;
        $tgUserId = $tgUser->id;

        // Логика снятия брони с места (например, удаление из базы данных)
        \App\Models\Game::where('table_id', $tableId)
            ->where('seat_number', $seatNumber)
            ->where('tg_user_id', $tgUserId)
            ->delete();

        return response()->json(['success' => true]);
    }
}
