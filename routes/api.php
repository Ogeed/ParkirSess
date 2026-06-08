<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\SensorDataController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('sensor', function (Request $request) {
    return Limit::perMinute(60)->by($request->input('device_id') ?? $request->ip());
});

Route::prefix('v1')->group(function () {

    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::get('/sensor-data', [SensorDataController::class, 'index']);
        Route::get('/sensor-data/latest', [SensorDataController::class, 'latest']);
        Route::get('/sensor-data/chart', [SensorDataController::class, 'chart']);

        Route::get('/devices', [DeviceController::class, 'index']);
        Route::get('/devices/{id}/status', [DeviceController::class, 'status']);
        Route::put('/devices/{id}', [DeviceController::class, 'update']);
        Route::delete('/devices/{id}', [DeviceController::class, 'destroy']);
    });

    Route::post('/devices/register', [DeviceController::class, 'register']);

    Route::middleware('esp.token')->group(function () {
        Route::post('/sensor-data', [SensorDataController::class, 'store'])->middleware('throttle:sensor');
    });
});
