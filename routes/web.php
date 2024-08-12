<?php

use App\Http\Controllers\Backbone\AnnouncementController;
use App\Http\Controllers\Backbone\ArticleController;
use App\Http\Controllers\Backbone\AuthBackboneController;
use App\Http\Controllers\Backbone\DashboardController;
use App\Http\Controllers\Backbone\EditionController;
use App\Http\Controllers\Backbone\SubmissionController;
use App\Http\Controllers\Backbone\UserAccessController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware(['auth:sanctum', 'check:admin_law,admin_economy,author_law,author_economy'])->group(function () {
    // dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // editions
    Route::resource('editions', EditionController::class);

    // articles
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('{editionId}', [ArticleController::class, 'index'])->name('index');
        Route::get('{editionId}/create', [ArticleController::class, 'create'])->name('create');
        Route::post('{editionId}', [ArticleController::class, 'store'])->name('store');
        Route::get('{editionId}/{article}', [ArticleController::class, 'show'])->name('show');
        Route::get('{editionId}/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('{editionId}/{article}', [ArticleController::class, 'update'])->name('update');
        Route::delete('{editionId}/{article}', [ArticleController::class, 'destroy'])->name('destroy');
    });

    // submissions
    Route::resource('submissions', SubmissionController::class);

    // announcements
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::resource('/', AnnouncementController::class);
    });

    // user access management
    Route::prefix('users-access')->name('users-access.')->group(function () {
        Route::resource('/', UserAccessController::class);
    });
});

// Authorization for backend
Route::redirect('/', Auth::check() ? 'dashboard' : 'auth-backbone/login');

Route::prefix('auth-backbone')->name('auth-backbone.')->controller(AuthBackboneController::class)->group(function () {
    Route::get('login', 'loginPage')->name('login');
    Route::post('submitLogin', 'submitLogin')->name('submitLogin');
    Route::post('logout', 'logout')->name('logout');
});
