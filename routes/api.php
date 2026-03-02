<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/auth')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('refresh', [AdminAuthController::class, 'refresh'])->middleware('throttle:10,1');
    Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin_api');
});
