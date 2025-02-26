<?php

use App\Models\Post;
use App\Utils\LinkedInApi;
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune --hours=48')->daily();

// This needs to run at 8:30am MST
Schedule::call(function () {
    $run = env('SCHEDULE_POSTS');

    if ($run) {
        // Get the new posts from Wordpress
        Post::fetchFromWordpress();
        // Post to LinkedIn
        Post::postToLinkedIn();
    }
})->daily();

// Ping the server to keep it warm before we post
Schedule::call(function () {
    LinkedInApi::pingServer();
})->everyFiveMinutes();
