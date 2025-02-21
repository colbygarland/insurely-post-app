<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LinkedInController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\Verified;
use Illuminate\Support\Facades\Route;

Route::get('/unauthorized', [UsersController::class, 'unauthorized'])->name('unauthorized');

Route::middleware(['auth', Verified::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users', [UsersController::class, 'list'])->name('users.list');
    Route::get('/users/verify-user/{id}', [UsersController::class, 'verify']);

    // Linkedin related things 
    Route::get('access-token', [LinkedInController::class, 'saveTokens']);
    Route::get('get-token', [LinkedInController::class, 'redirectToAuth']);
    Route::get('posts', [PostsController::class, 'index'])->name('posts');
    Route::get('posts/make/{id}', [LinkedInController::class, 'makePostToLinkedIn']);
    Route::get('posts/confirm/{id}', [PostsController::class, 'confirmPost']);
    Route::get('posts/list', [PostsController::class, 'list']);
    Route::get('posts/manually-post-to-linkedin', [PostsController::class, 'manuallyPostToLinkedIn']);
    Route::get('posts/manually-post-to-single-to-linkedin/{id}', [PostsController::class, 'manuallyPostSingleToLinkedIn']);
    Route::get('posts/manually-mark-published', [PostsController::class, 'manuallyMarkAllPublished']);
    Route::get('company-lookup', [LinkedInController::class, 'companySearch']);

    // WordPress related things 
    Route::get('wordpress/fetch_from_wordpress', [PostsController::class, 'fetchFromWordpress']);
});

require __DIR__.'/auth.php';
