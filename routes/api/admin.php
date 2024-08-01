<?php

use App\Http\Controllers\Api\Admin\Announcements\AnnouncementsController;
use App\Http\Controllers\Api\Admin\Article\ArticleController;
use App\Http\Controllers\Api\Admin\Category\CategoryController;
use App\Http\Controllers\Api\Admin\Edition\EditionController;
use App\Http\Controllers\Api\Admin\Journals\JournalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check:super_admin,admin'])->group(function () {
    Route::prefix('edition')->group(function () {
        route::get('', [EditionController::class, 'index']);
        Route::post('', [EditionController::class, 'store']);
        Route::put('status/{slug}', [EditionController::class, 'updateStatus']);
        Route::prefix('{slug}')->group(function () {
            Route::get('', [EditionController::class, 'show']);
            Route::put('', [EditionController::class, 'update']);
            Route::delete('', [EditionController::class, 'delete']);
            Route::prefix('article')->group(function () {
                Route::post('', [ArticleController::class, 'store']);
                Route::get('{articleSlug}', [ArticleController::class,'index']);
                Route::put('{articleSlug}', [ArticleController::class,'update']);
                Route::delete('{articleSlug}', [ArticleController::class,'delete']);
            });
        });
    });

    Route::prefix('announcements')->group(function () {
        route::get('', [AnnouncementsController::class, 'index']);
        route::post('', [AnnouncementsController::class, 'store']);
        route::put('', [AnnouncementsController::class, 'update']);
        route::delete('{id}', [AnnouncementsController::class, 'destroy']);
    });
    
    Route::prefix('pdf')->group(function () {
        Route::post('extract-text', [ArticleController::class, 'getAbstract']);
    });
});


