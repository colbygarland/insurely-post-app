<?php

use App\Models\Post;
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune --hours=48')->daily();

Schedule::call(function () {
    $run = env('SCHEDULE_POSTS');

    if ($run) {
        // Get the new posts from Wordpress
        Post::fetchFromWordpress();
        // Post to LinkedIn
        Post::postToLinkedIn();
    }
})->daily();
