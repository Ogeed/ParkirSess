<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\HistoryController;
use App\Http\Controllers\Dashboard\DeviceController;
use App\Http\Controllers\Dashboard\AlertController;
use App\Http\Controllers\Dashboard\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices');
    Route::put('/devices/{id}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::get('/devices/{id}/reset-token', [DeviceController::class, 'resetToken'])->name('devices.reset-token');
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts');
    Route::post('/alerts/{id}/acknowledge', [AlertController::class, 'acknowledge'])->name('alerts.acknowledge');
    Route::post('/alerts/acknowledge-all', [AlertController::class, 'acknowledgeAll'])->name('alerts.acknowledge-all');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/threshold', [SettingsController::class, 'updateThreshold'])->name('settings.update-threshold');
    Route::post('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.update-account');
});

Route::redirect('/', '/dashboard');
