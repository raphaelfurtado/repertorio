<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\RepertoireShareController;
use App\Models\Repertoire;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [GoogleAuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/google', [GoogleAuthController::class, 'authenticate'])->name('auth.google');
});

Route::post('/logout', [GoogleAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/r/{token}', [RepertoireShareController::class, 'show'])
    ->name('repertoires.public');

Route::middleware('auth')->group(function () {
    Route::view('/app', 'dashboard')->name('dashboard');

    Route::get('/repertorios/{repertoire}', function (Repertoire $repertoire) {
        abort_unless($repertoire->user_id === auth()->id(), 403);

        return view('repertoires.edit', [
            'repertoire' => $repertoire,
        ]);
    })->name('repertoires.edit');
});
