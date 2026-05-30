<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/telegram/create-user', [\App\Http\Controllers\TelegramController::class, 'createUser'])->name('telegram.create-user');
Route::post('/telegram/get-avatar', [\App\Http\Controllers\TelegramController::class, 'getAvatar'])->name('telegram.get-avatar');

Route::get('/logs', [\App\Http\Controllers\LogViewerController::class, 'index'])->name('logs');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';