<?php

use App\Http\Controllers\Api\TelegramAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/api/telegram/init-data', [TelegramAuthController::class, 'validateInitData']);
