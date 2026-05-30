<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiTgUserRequest;
use App\Models\TgUser;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function createUser(ApiTgUserRequest $request)
    {
        $data = $request->validated();

        if(TgUser::where('telegram_id', $data['telegram_id'])->exists()) {
            $user = TgUser::where('telegram_id', $data['telegram_id'])->first();
        } else {
            $user = TgUser::create($data);
            try {
                dispatch(new \App\Jobs\UploadAvatarTgUser($data['telegram_id']));
            } catch (\Exception $e) {
                Log::error("Failed to dispatch UploadAvatarTgUser job: " . $e->getMessage());
                \App\Jobs\UploadAvatarTgUser::dispatchSync($data['telegram_id']);
            }
        }

        return response()->json(['user' => $user->toArray()]);
    }
}
