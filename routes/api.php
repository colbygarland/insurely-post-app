<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;
use App\Http\Middleware\Verified;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Wordpress related things
Route::middleware([Verified::class])->group(function(){
    Route::get('posts/fetch_from_wordpress', [PostsController::class, 'fetchFromWordpress']);
    Route::get('posts/list', [PostsController::class, 'list']);
    Route::get('posts/get/{post}', [PostsController::class, 'get']);
    Route::post('posts/{post}', [PostsController::class, 'update']);
});

