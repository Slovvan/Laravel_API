<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleLikeController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ArticleController;

    Route::get('/articles', [ArticleController::class, 'index']);         
    Route::get('/articles/{id}', [ArticleController::class, 'show']);      
    Route::get('/articles/{article}/comments', [CommentController::class, 'index']);
    Route::post('/articles/{article}/like', [ArticleLikeController::class, 'toggle']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/articles/{article}/comments', [CommentController::class, 'store']);
        Route::put('/comments/{comment}', [CommentController::class, 'update']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    });