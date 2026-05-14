<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/panel', [PanelController::class, 'index'])->name('panel');
    Route::get('/panel/data/realtime', [PanelController::class, 'realtimeData'])->name('panel.data.realtime');
    Route::get('/alarm', [AlarmController::class, 'index'])->name('alarm');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
