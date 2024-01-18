<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Backend\ArticleController;
use App\Http\Controllers\Backend\AuthorController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\SourceController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Publicly accessible routes

//Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article}', [ArticleController::class, 'show']);

// Resource routes
Route::resource('categories', CategoryController::class);
Route::resource('sources', SourceController::class);
Route::resource('authors', AuthorController::class);

// Routes that require authentication
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/validate-token', [AuthController::class, 'validateToken']);
    Route::get('/user/get-preferences', [UserController::class, 'getPreferences']);
    Route::post('/user/update-preferences', [UserController::class, 'updatePreferences']);
    Route::post('/user/settings', [UserController::class, 'updateSettings']);
    Route::match(['get', 'post'], '/feed', [ArticleController::class, 'personalizedFeed']);
    
});
