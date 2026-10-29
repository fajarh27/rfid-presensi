<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Middleware\AuthenticateDevice;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['throttle:120,1', AuthenticateDevice::class])
    ->group(function () {
        Route::get('/ping', [AttendanceController::class, 'ping']);
        Route::post('/attendance', [AttendanceController::class, 'store']);
    });
