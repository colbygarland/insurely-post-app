<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Conversation;
use App\Models\Post;
use Illuminate\Http\Request;
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
        $recentConversations = Conversation::excludeVoicemail()
            ->orderBy('created_at', 'desc')
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

    public function getChartData(Request $request)
    {
        // Ensure admin access
        if (! Gate::allows('is-admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validate dates
        if (! $startDate || ! $endDate) {
            return response()->json(['error' => 'Start and end dates are required'], 400);
        }

        try {
            $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Get call logs data for the specified date range
        $callLogsPerPerson = CallLog::selectRaw('LOWER(TRIM(from_name)) as cleaned_name, COUNT(*) as count')
            ->whereNotNull('from_name')
            ->where('from_name', '!=', '')
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->groupBy('cleaned_name')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(function ($row) {
                $cleaned = CallLog::cleanFromName($row->cleaned_name);

                return [$cleaned => $row->count];
            });

        // Calculate stats
        $totalCalls = $callLogsPerPerson->sum();
        $activeMembers = $callLogsPerPerson->count();
        $avgCalls = $activeMembers > 0 ? number_format($callLogsPerPerson->avg(), 1) : '0';
        $highestCalls = $activeMembers > 0 ? $callLogsPerPerson->max() : '0';

        // Top performers (top 3)
        $topPerformers = $callLogsPerPerson->sortDesc()->take(3)->map(function ($count, $name) {
            return ['name' => $name, 'count' => $count];
        })->values();

        // Create date range label
        $startCarbon = \Carbon\Carbon::parse($startDate);
        $endCarbon = \Carbon\Carbon::parse($endDate);

        if ($startCarbon->format('Y-m') === $endCarbon->format('Y-m')) {
            $dateRangeLabel = $startCarbon->format('F Y');
        } else {
            $dateRangeLabel = $startCarbon->format('M Y').' - '.$endCarbon->format('M Y');
        }

        return response()->json([
            'call_logs_per_person' => $callLogsPerPerson,
            'stats' => [
                'total_calls' => $totalCalls,
                'active_members' => $activeMembers,
                'avg_calls' => $avgCalls,
                'highest_calls' => $highestCalls,
            ],
            'top_performers' => $topPerformers,
            'date_range_label' => $dateRangeLabel,
        ]);
    }
}
