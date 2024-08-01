<?php

use App\Http\Controllers\Api\User\Announcements\AnnouncementsController;
use App\Http\Controllers\Api\User\Category\CategoryController;
use App\Http\Controllers\Api\User\Journal\JournalController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

    Route::prefix('edition')->group( function () { 
        Route::get('current', [JournalController::class, 'currentPage']);
        Route::get('archieved', [JournalController::class, 'archievedPage']);
        Route::get('show/{slug}', [JournalController::class, 'showEdition']);
        
    });

    Route::get('article/{slug}', [JournalController::class, 'showArticle']);
    Route::get('search/article', [JournalController::class, 'searchArticle']);
    
    Route::prefix('announcements')->group(function () {
        Route::get('', [AnnouncementsController::class, 'index']);
        Route::get('{slug}', [AnnouncementsController::class, 'view']);
    });

    // Route::middleware(['auth:sanctum', 'check:user'])->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/{apiToken}', [AuthController::class, 'userData']);
        });
    // });


