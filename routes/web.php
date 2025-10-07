<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/posts', [PostsController::class, 'index'])->name('posts')->middleware('can:is-admin');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/revoke-linkedin-tokens', [LinkedInController::class, 'revokeTokens']);
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings')->middleware('can:is-admin');

    // Users related things
    Route::get('/users', [UsersController::class, 'list'])->name('users.list')->middleware('can:is-admin');
    Route::get('/users/verify-user/{id}', [UsersController::class, 'verify'])->middleware('can:is-admin');
    Route::get('/users/promote-to-admin/{id}', [UsersController::class, 'promoteToAdmin'])->name('users.promote-admin')->middleware('can:is-admin');
    Route::post('/users/generate-password-reset/{id}', [UsersController::class, 'generatePasswordResetLink'])->name('users.generate-password-reset')->middleware('can:is-admin');
    Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy')->middleware('can:is-admin');
    Route::get('/users/login-as-user/{id}', [UsersController::class, 'loginAsUser'])->name('users.login-as-user')->middleware('can:is-admin');
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
    Route::get('/ai/outbound-call', [AiController::class, 'index'])->name('ai.index')->middleware('can:is-admin');
    Route::post('/ai/outbound-call', [AiController::class, 'outboundCall'])->name('ai.send')->middleware('can:is-admin');
    Route::post('/ai/upload', [AiController::class, 'upload'])->name('ai.upload')->middleware('can:is-admin');
    Route::post('/ai/analyze-transcripts-settings', [AiController::class, 'analyzeTranscriptsSettings'])->name('ai.analyze-transcripts-settings')->middleware('can:is-admin');
    // Conversation stuff
    Route::get('/ai/conversation/{conversation}', [ConversationController::class, 'show'])->name('ai.conversation.show')->middleware('can:is-admin');
    Route::get('/ai/conversation/{conversation}/destroy', [ConversationController::class, 'destroy'])->name('ai.conversation.destroy')->middleware('can:is-admin');
    Route::post('/ai/conversation/{conversation}/analyze', [AiController::class, 'pushToAiForReview'])->name('ai.conversation.analyze')->middleware('can:is-admin');

    // Ring Central stuff
    Route::get('/ringcentral', [RingCentralController::class, 'index'])->name('ringcentral.index');
    Route::get('/ringcentral/details/{callLog}', [RingCentralController::class, 'show'])->name('ringcentral.details');
    Route::post('/calllog/{callLog}/transcript', [CallLogController::class, 'generateTranscript'])->name('calllog.transcript');
    Route::post('/calllog/{callLog}/transcript-only', [CallLogController::class, 'generateTranscriptOnly'])->name('calllog.transcript-only');
    Route::post('/calllog/{callLog}/summary-analysis', [CallLogController::class, 'generateSummaryAndAnalysis'])->name('calllog.summary-analysis');
    Route::post('/calllog/update-summary-prompt', [CallLogController::class, 'updateSummaryPrompt'])->name('calllog.update-summary-prompt')->middleware('can:is-admin');
    Route::post('/calllog/update-analyze-prompt', [CallLogController::class, 'updateAnalyzePrompt'])->name('calllog.update-analyze-prompt')->middleware('can:is-admin');
    Route::post('/calllog/{callLog}/update-call-type', [CallLogController::class, 'updateCallType'])->name('calllog.update-call-type');
});

require __DIR__.'/auth.php';
