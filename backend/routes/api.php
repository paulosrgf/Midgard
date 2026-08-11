<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\HighlightController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/search', [\App\Http\Controllers\UserController::class, 'search']);
    
    // O whereNumber garante que essa rota SÓ dispara se o parâmetro for numérico (ex: /users/1)
    Route::get('/users/{id}/posts', [\App\Http\Controllers\UserController::class, 'posts'])->whereNumber('id');
    Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show'])->whereNumber('id');
    
    // Se não for número, o Laravel passa direto para cá e busca pelo Username (ex: /users/polokkz)
    Route::get('/users/{username}', [ProfileController::class, 'showUser']);
    
    Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->whereNumber('id');
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('/posts/{post}/like', [LikeController::class, 'store']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::get('/users/{username}', [ProfileController::class, 'showUser']);
    Route::post('/users/{username}/follow', [FollowController::class, 'toggle']);
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/stories', [StoryController::class, 'index']);
    Route::post('/stories', [StoryController::class, 'store']);
    Route::delete('/stories/{id}', [StoryController::class, 'destroy']);
    Route::post('/highlights', [HighlightController::class, 'store']);
    Route::get('/highlights/{id}', [HighlightController::class, 'show']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});