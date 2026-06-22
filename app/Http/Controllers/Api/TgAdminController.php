<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TgBotGetAdminLinkRequest;
use App\Models\TgUser;
use Illuminate\Http\Request;

class TgAdminController extends Controller
{
    public function getAdminLink(TgBotGetAdminLinkRequest $request)
    {
        $validated = $request->validated();
        $tgUserId = $validated['tg_user_id'];

        $tgUser = TgUser::where('telegram_id', $tgUserId)->firestOrFail();
        $user = $tgUser->user;
        if(!$user) {
            abort(404);
        }

        $adminPanelLink = asset('admin');

        return response()
        ->json([
            'adminLink' => $adminPanelLink
        ]);
    }
}
