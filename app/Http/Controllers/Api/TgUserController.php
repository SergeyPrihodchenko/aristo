<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiTgUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TgUserController extends Controller
{
    public function addUserData(ApiTgUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tgUser = \App\Models\TgUser::create($validated);

        $pass = rand(100000, 999999);

        $user = new User([
            'name' => $tgUser->first_name . ' ' . $tgUser->last_name,
            'email' => $tgUser->username . '@example.com',
            'password' => bcrypt($pass),
        ]);

        $user->save();

        return response()->json($tgUser, 201);
    }
}
