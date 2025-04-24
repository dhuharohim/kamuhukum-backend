<?php

use App\Http\Controllers\Backbone\AnnouncementController;
use App\Http\Controllers\Backbone\ArticleController;
use App\Http\Controllers\Backbone\AuthBackboneController;
use App\Http\Controllers\Backbone\DashboardController;
use App\Http\Controllers\Backbone\EditionController;
use App\Http\Controllers\Backbone\SubmissionController;
use App\Http\Controllers\Backbone\UserAccessController;
use App\Http\Controllers\Backbone\CmsController;
use App\Http\Controllers\FacebookWebhookController;
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


Route::middleware(['auth:sanctum', 'check:admin_law,admin_economy,editor_economy,editor_law'])->group(function () {
    // dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // editions
    Route::resource('editions', EditionController::class);

    // articles
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('{editionId}', [ArticleController::class, 'index'])->name('index');
        Route::get('{editionId}/create', [ArticleController::class, 'create'])->name('create');
        Route::get('{editionId}/generate-doi', [ArticleController::class, 'generateDoi'])->name('generateDoi');
        Route::post('{editionId}/generate-doi-for-selected-articles', [ArticleController::class, 'generateDoiForSelectedArticles'])->name('generateDoiForSelectedArticles');
        Route::post('{editionId}', [ArticleController::class, 'store'])->name('store');
        Route::get('{editionId}/{article}', [ArticleController::class, 'show'])->name('show');
        Route::get('{editionId}/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('{editionId}/{article}', [ArticleController::class, 'update'])->name('update');
        Route::delete('{editionId}/{article}', [ArticleController::class, 'destroy'])->name('destroy');
        Route::post('{articleId}/send-comment', [ArticleController::class, 'sendComment'])->name('sendComment');
    });

    // submissions
    Route::resource('submissions', SubmissionController::class);
    Route::post('assign-editor', [SubmissionController::class, 'assignEditor'])->name('submissions.assignEditor');
    Route::get('get-editors/{articleId}', [SubmissionController::class, 'getEditors'])->name('submissions.getEditors');
    Route::post('remove-editor/{editorId}', [SubmissionController::class, 'removeEditor'])->name('submissions.removeEditor');
    Route::get('get-editors-avail/{articleId}', [SubmissionController::class, 'getEditorAvail'])->name('submissions.getEditorsAvail');
});

Route::middleware(['auth:sanctum', 'check:admin_law,admin_economy'])->group(function () {
    // announcements
    Route::resource('announcements', AnnouncementController::class);

    // user access management
    Route::resource('users-access', UserAccessController::class);

    // content management system
    Route::post('cms/upload-image', [CmsController::class, 'uploadImage'])->name('cms.upload-image');
    Route::resource('cms', CmsController::class);
});

// Authorization for backend
Route::redirect('/', Auth::check() ? 'dashboard' : 'auth-backbone/login');

Route::prefix('auth-backbone')->name('auth-backbone.')->controller(AuthBackboneController::class)->group(function () {
    Route::get('login', 'loginPage')->name('login');
    Route::post('submitLogin', 'submitLogin')->name('submitLogin');
    Route::post('logout', 'logout')->name('logout');
});

Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::get('facebook', [FacebookWebhookController::class, 'verify']);
    Route::post('facebook', [FacebookWebhookController::class, 'handle']);
});
