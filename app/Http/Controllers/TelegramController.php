<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiTgUserRequest;
use App\Models\TgUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function createUser(ApiTgUserRequest $request)
    {
        $validated = $request->validated();

        if(TgUser::where('telegram_id', $validated['telegram_id'])->exists()) {
            $user = TgUser::where('telegram_id', $validated['telegram_id'])->first();
        } else {
            $user = TgUser::create($validated);
            try {
                dispatch(new \App\Jobs\UploadAvatarTgUser($validated['telegram_id']));
            } catch (\Exception $e) {
                Log::error("Failed to dispatch UploadAvatarTgUser job: " . $e->getMessage());
                \App\Jobs\UploadAvatarTgUser::dispatchSync($validated['telegram_id']);
            }
        }

        return response()->json(['user' => $user->toArray()]);
    }

    public function getAvatar(Request $request)
    {
        $telegram_id = $request->post('telegram_id');
        $user = TgUser::where('telegram_id', $telegram_id)->firstOrFail();
        $photoUrl = $user->photo_url;
        return response()->json(['photo_url' => $photoUrl]);
    }
}
