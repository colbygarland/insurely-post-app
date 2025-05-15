<?php

use App\Models\Post;
use App\Utils\LinkedInApi;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune --hours=48')->daily();

// Ping the server to keep it warm before we post
Schedule::call(function () {
    LinkedInApi::pingServer();
})->everyFiveMinutes();

Schedule::call(function () {
    $run = env('SCHEDULE_POSTS');

    if ($run) {
        // Get the new posts from Wordpress
        Post::fetchFromWordpress();
        // Post to LinkedIn
        Post::postToLinkedIn();
    }

    // This needs to run at 8:30am MST
})->dailyAt('15:30');

Schedule::call(function () {
    $run = env('RUN_QUEUE_SCHEDULER');

    if ($run) {
        Log::info('Running queue scheduler');
        Artisan::call('queue:work', ['--once' => true]);
    }
})->everyMinute();
