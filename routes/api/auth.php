<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group( function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('', [AuthController::class, 'index']);
        Route::get('logout', [AuthController::class, 'logout']);
    });
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});
