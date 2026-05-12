<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class TelegramAuthController extends Controller
{
    public function validateInitData(Request $request)
    {
        $initData = $request->input('initData');
        
        // Парсим initData
        parse_str($initData, $data);
        
        if (!isset($data['hash'])) {
            throw new InvalidArgumentException('Invalid init data', 400);
        }
        
        $hash = $data['hash'];
        unset($data['hash']);
        
        // Сортируем ключи
        ksort($data);
        
        // Создаем строку для проверки
        $checkString = collect($data)
            ->map(fn($value, $key) => $key . '=' . 
                (is_array($value) ? json_encode($value) : $value))
            ->implode("\n");
        
        // Вычисляем hash
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $calculatedHash = bin2hex(hash_hmac('sha256', $checkString, $secretKey, true));
        
        // Проверяем
        if (!hash_equals($calculatedHash, $hash)) {
            return response()->json(['error' => 'Invalid hash'], 401);
        }
        
        // Извлекаем данные пользователя
        $userData = json_decode($data['user'] ?? '{}', true);
        
        // Находим или создаем пользователя
        $user = User::updateOrCreate(
            ['telegram_id' => $userData['id']],
            [
                'name' => $userData['first_name'] . ' ' . ($userData['last_name'] ?? ''),
                'telegram_username' => $userData['username'] ?? null,
                'avatar' => $userData['photo_url'] ?? null,
            ]
        );
        
        // Создаем токен для API
        $token = $user->createToken('telegram-auth')->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
}