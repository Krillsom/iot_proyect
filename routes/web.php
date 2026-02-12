<?php

use App\Http\Controllers\ProfileController;
use App\Contexts\MqttIngestion\Http\Controllers\MqttDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard principal con monitoreo IoT MQTT (usando CQRS)
Route::get('/dashboard', [MqttDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // API para dashboard en tiempo real (MQTT Ingestion Context)
    Route::get('/api/dashboard/live', [MqttDashboardController::class, 'liveData'])->name('dashboard.live');
    Route::get('/api/dashboard/devices', [MqttDashboardController::class, 'devices'])->name('dashboard.devices');
    Route::get('/api/dashboard/triangulation', [MqttDashboardController::class, 'triangulation'])->name('dashboard.triangulation');
    Route::get('/api/dashboard/device/{deviceId}/readings', [MqttDashboardController::class, 'deviceReadings'])->name('dashboard.device.readings');
});

require __DIR__.'/auth.php';

