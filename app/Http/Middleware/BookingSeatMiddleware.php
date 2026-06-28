<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingSeatMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Записываем все бронирования за текущий день в приложение пользователей в таблицу action_stats
        $tgUserID = $request->post('telegram_id');
        if ($tgUserID) {
            $tgUser = \App\Models\TgUser::where('telegram_id', $tgUserID)->first();
            if ($tgUser) {
                $actionStat = \App\Models\ActionStat::
                where('tg_user_id', $tgUser->id)
                ->whereDate('created_at', now()->toDateString())
                ->first();
                if ($actionStat->bookings == 0) {
                    $actionStat->bookings = 1;
                    $actionStat->save();
                }
            }
        }
        return $next($request);
    }
}
