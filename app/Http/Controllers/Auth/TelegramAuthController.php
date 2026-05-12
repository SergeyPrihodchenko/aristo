<?php
// app/Http/Controllers/Auth/TelegramAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TelegramAuthController extends Controller
{
    public function ajaxAuth(Request $request)
    {
        try {
            $telegramUser = $request->all();
            
            // Валидация данных
            $checkHash = $this->checkTelegramAuthorization($telegramUser);
            
            if (!$checkHash) {
                return response()->json(['success' => false, 'error' => 'Invalid hash'], 401);
            }
            
            // Находим или создаем пользователя
            $user = User::where('telegram_id', $telegramUser['id'])->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $telegramUser['first_name'] . ' ' . ($telegramUser['last_name'] ?? ''),
                    'email' => $telegramUser['id'] . '@telegram.user',
                    'password' => Hash::make(uniqid()),
                    'telegram_id' => $telegramUser['id'],
                    'telegram_username' => $telegramUser['username'] ?? null,
                    'avatar' => $telegramUser['photo_url'] ?? null,
                    'email_verified_at' => now(),
                ]);
            }
            
            Auth::login($user);
            $request->session()->regenerate();
            
            return response()->json(['success' => true, 'user' => $user]);
            
        } catch (\Exception $e) {
            Log::error('Telegram auth error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }
    
    public function callback(Request $request)
    {
        // Обработка callback от Telegram
        // Здесь можно добавить логику после успешной авторизации
        
        return redirect()->intended('/dashboard');
    }
    
    private function checkTelegramAuthorization($authData)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        
        if (!$botToken) {
            Log::error('TELEGRAM_BOT_TOKEN not set');
            return false;
        }
        
        $checkHash = $authData['hash'] ?? '';
        unset($authData['hash']);
        
        ksort($authData);
        $dataCheckString = [];
        foreach ($authData as $key => $value) {
            $dataCheckString[] = $key . '=' . $value;
        }
        
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $hash = hash_hmac('sha256', implode("\n", $dataCheckString), $secretKey);
        
        if (strcmp($hash, $checkHash) !== 0) {
            Log::warning('Telegram hash mismatch', ['hash' => $checkHash, 'calculated' => $hash]);
            return false;
        }
        
        // Проверка, что авторизация не старше 24 часов
        if ((time() - ($authData['auth_date'] ?? 0)) > 86400) {
            Log::warning('Telegram auth date expired');
            return false;
        }
        
        return true;
    }
}