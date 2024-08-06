<?php

use App\Http\Controllers\Api\User\Announcements\AnnouncementsController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\Category\CategoryController;
use App\Http\Controllers\Api\User\Journal\JournalController;
use Illuminate\Support\Facades\Route;

Route::prefix('edition')->group(function () {
    Route::get('current/{from}/{cred?}', [JournalController::class, 'currentPage']);
    Route::get('archieved/{from}/{cred?}', [JournalController::class, 'archievedPage']);
    Route::get('show/{from}/{slug}/{cred?}', [JournalController::class, 'showEdition']);
});

Route::get('article/{from}/{slug}/{cred?}', [JournalController::class, 'showArticle']);
Route::get('search/article/{from}', [JournalController::class, 'searchArticle']);

Route::prefix('announcements')->group(function () {
    Route::get('{from}', [AnnouncementsController::class, 'index']);
    Route::get('{from}/{slug}', [AnnouncementsController::class, 'view']);
});

Route::get('home/{from}', [JournalController::class, 'getHomeData']);

Route::middleware(['auth:sanctum', 'check:author_law,author_economic'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('get-user-data', [AuthController::class, 'userData']);

        // submission
        Route::get('submission-list/{from}', [JournalController::class, 'getUserArticles']);
        Route::get('submission/{from}/{uuidArticle}', [JournalController::class, 'getUserArticle']);
        Route::post('submission/{from}/{step}', [JournalController::class, 'submitSubmission']);
        Route::delete('submission/{from}/{uuid}', [JournalController::class, 'deleteSubmission']);

        // edit profile
        Route::post('profile/{from}/{type}', [AuthController::class, 'editProfile']);
    });
});
