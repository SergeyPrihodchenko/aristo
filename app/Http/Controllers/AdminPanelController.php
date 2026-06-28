<?php

namespace App\Http\Controllers;

use App\Models\TgUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPanelController extends Controller
{
    public function index(Request $request)
    {
        // Проверяем токен авторизации в запросе
        $tgUser = TgUser::where('telegram_id', $request->query('tg_user_id'))->first();
        if (!$tgUser || !$tgUser->user) {
            return response()->json([
                'message' => 'Unauthorized',
                'authorized' => false
                ], 401);
        }
        $token = $request->query('token');
        $expectedToken = hash('sha256', $tgUser->telegram_id . env('APP_KEY'));
        if (!hash_equals($expectedToken, $token)) {
            return response()->json([
                'message' => 'Unauthorized',
                'authorized' => false
                ], 401);
        }

        Auth::guard('web')->login($tgUser->user);

        $request->session()->regenerate();

        return response()
        ->json([
            'authorized' => true
        ]);
    }
}
