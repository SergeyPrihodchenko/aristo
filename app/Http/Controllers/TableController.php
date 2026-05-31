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

        \App\Models\Game::create([
            'table_id' => $tableId,
            'seat_number' => $seatNumber,
            'tg_user_id' => $tgUserId,
        ]);

        return response()->json(['success' => true]);
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

        // Логика снятия брони с места (например, удаление из базы данных)
        \App\Models\Game::where('table_id', $tableId)
            ->where('seat_number', $seatNumber)
            ->where('tg_user_id', $tgUserId)
            ->delete();

        return response()->json(['success' => true]);
    }
}
