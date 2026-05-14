<?php

use App\Http\Controllers\Api\DashboardDataController;
use App\Http\Controllers\Api\SensorDataController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardDataController::class, 'show'])->name('dashboard.data');

Route::any('/sensors/gps', [SensorDataController::class, 'storeGps'])->name('api.sensors.gps.store');
Route::any('/sensors/accelerometer', [SensorDataController::class, 'storeAccelerometer'])->name('api.sensors.accelerometer.store');
Route::get('/sensors/latest', [SensorDataController::class, 'latest'])->name('api.sensors.latest');
Route::get('/sensors', [SensorDataController::class, 'index'])->name('api.sensors.index');