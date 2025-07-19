<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Conversation;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect non-admin users to Ring Central page
        if (! Gate::allows('is-admin')) {
            return redirect()->route('ringcentral.index');
        }

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

        // Get count of call logs per person (from_name, cleaned) for current month
        $callLogsPerPerson = CallLog::selectRaw('LOWER(TRIM(from_name)) as cleaned_name, COUNT(*) as count')
            ->whereNotNull('from_name')
            ->where('from_name', '!=', '')
            ->whereMonth('start_time', now()->month)
            ->whereYear('start_time', now()->year)
            ->groupBy('cleaned_name')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(function ($row) {
                // Clean the name using the model's cleanFromName method
                $cleaned = CallLog::cleanFromName($row->cleaned_name);

                return [$cleaned => $row->count];
            });

        // Get some stats
        $stats = [
            'total_published_posts' => Post::where('published_at', '!=', null)->count(),
            'pending_posts' => $pendingPosts->count(),
            'total_conversations' => Conversation::count(),
            'total_call_logs' => CallLog::count(),
            'transcribed_calls' => CallLog::whereNotNull('transcription')->count(),
            'call_logs_per_person' => $callLogsPerPerson,
            'current_month' => now()->format('F Y'), // e.g., "January 2025"
            'current_month_call_logs' => CallLog::whereMonth('start_time', now()->month)
                ->whereYear('start_time', now()->year)
                ->count(),
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
