<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\LinkedInController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RingCentralController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\Verified;
use App\Utils\LinkedInApi;
use Illuminate\Support\Facades\Route;

Route::get('/unauthorized', [UsersController::class, 'unauthorized'])->name('unauthorized');

Route::middleware(['auth', Verified::class])->group(function () {
    Route::get('/', [PostsController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/revoke-linkedin-tokens', [LinkedInController::class, 'revokeTokens']);
    Route::get('/users', [UsersController::class, 'list'])->name('users.list');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/users/verify-user/{id}', [UsersController::class, 'verify']);

    // Linkedin related things
    Route::get('access-token', [LinkedInController::class, 'saveTokens']);
    Route::get('get-token', [LinkedInController::class, 'redirectToAuth']);
    Route::get('posts/make/{id}', [LinkedInController::class, 'makePostToLinkedIn']);
    Route::get('posts/confirm/{id}', [PostsController::class, 'confirmPost']);
    Route::get('posts/list', [PostsController::class, 'list']);
    Route::get('posts/manually-post-to-linkedin', [PostsController::class, 'manuallyPostToLinkedIn']);
    Route::get('posts/manually-post-to-single-to-linkedin/{id}', [PostsController::class, 'manuallyPostSingleToLinkedIn']);
    Route::get('posts/manually-mark-published', [PostsController::class, 'manuallyMarkAllPublished']);
    Route::get('company-lookup', [LinkedInController::class, 'companySearch']);
    Route::get('ping', function () {
        return response()->json(LinkedInApi::pingServer());
    });

    // WordPress related things
    Route::get('wordpress/fetch_from_wordpress', [PostsController::class, 'fetchFromWordpress']);

    // Outbound agent stuff
    Route::get('/ai/outbound-call', [AiController::class, 'index'])->name('ai.index');
    Route::post('/ai/outbound-call', [AiController::class, 'outboundCall'])->name('ai.send');
    Route::post('/ai/upload', [AiController::class, 'upload'])->name('ai.upload');

    // Conversation stuff
    Route::get('/ai/conversation/{conversation}', [ConversationController::class, 'show'])->name('ai.conversation.show');
    Route::get('/ai/conversation/{conversation}/destroy', [ConversationController::class, 'destroy'])->name('ai.conversation.destroy');
    Route::post('/ai/conversation/{conversation}/analyze', [AiController::class, 'pushToAiForReview'])->name('ai.conversation.analyze');

    // Ring Central stuff
    Route::get('/ringcentral', [RingCentralController::class, 'index'])->name('ringcentral.index');
});

require __DIR__.'/auth.php';
