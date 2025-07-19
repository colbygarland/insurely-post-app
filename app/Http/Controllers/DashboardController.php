<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Conversation;
use App\Models\Post;

class DashboardController extends Controller
{
    public function index()
    {
        // Get recent LinkedIn posts (published and pending)
        $recentPublishedPosts = Post::where('published_at', '!=', null)
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        $pendingPosts = Post::postsToBeSent();

        // Get recent AI conversations
        $recentConversations = Conversation::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent Ring Central call logs
        $recentCallLogs = CallLog::getRecentCallLogs();

        // Get some stats
        $stats = [
            'total_published_posts' => Post::where('published_at', '!=', null)->count(),
            'pending_posts' => $pendingPosts->count(),
            'total_conversations' => Conversation::count(),
            'total_call_logs' => CallLog::count(),
            'transcribed_calls' => CallLog::whereNotNull('transcription')->count(),
        ];

        return view('index', compact(
            'recentPublishedPosts',
            'pendingPosts',
            'recentConversations',
            'recentCallLogs',
            'stats'
        ));
    }
}
