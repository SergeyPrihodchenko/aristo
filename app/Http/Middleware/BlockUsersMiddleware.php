<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockUsersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tgUserID = $request->post('telegram_id');
        if ($tgUserID) {
            $tgUser = \App\Models\TgUser::where('telegram_id', $tgUserID)->first();
            if ($tgUser) {
                $isBlocked = \App\Models\BlockUser::where('tg_user_id', $tgUser->id)->exists();
                if ($isBlocked) {
                    return response()->json([
                        'message' => 'Ваш аккаунт заблокирован.',
                        'isBlocked' => true
                    ]);
                }
            }
        }
        return $next($request);
    }
}
