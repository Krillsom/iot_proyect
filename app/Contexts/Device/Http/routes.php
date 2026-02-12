<?php

use App\Contexts\Device\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

// API Routes
Route::middleware(['auth'])->prefix('api/devices')->group(function () {
    Route::get('/', [DeviceController::class, 'index'])->name('api.devices.index');
    Route::post('/', [DeviceController::class, 'store'])->name('api.devices.store');
    Route::get('/{uuid}', [DeviceController::class, 'show'])->name('api.devices.show');
    Route::put('/{uuid}', [DeviceController::class, 'update'])->name('api.devices.update');
    Route::delete('/{uuid}', [DeviceController::class, 'destroy'])->name('api.devices.destroy');
});
