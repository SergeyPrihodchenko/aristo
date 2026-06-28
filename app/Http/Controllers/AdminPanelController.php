<?php

namespace App\Http\Controllers;

use App\Models\TgUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPanelController extends Controller
{
    public function index(Request $request)
    {
        $tgUser = TgUser::where(
            'telegram_id',
            $request->query('tg_user_id')
        )->first();

        if (! $tgUser || ! $tgUser->user) {
            abort(403);
        }

        $expectedToken = hash(
            'sha256',
            $tgUser->telegram_id . config('app.key')
        );

        if (! hash_equals(
            $expectedToken,
            $request->query('token')
        )) {
            abort(403);
        }

        Auth::login($tgUser->user);

        $request->session()->regenerate();

        return redirect('/admin');
    }
}
