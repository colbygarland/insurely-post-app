<x-app-layout>
  <x-slot name="title">
    Dashboard
  </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Insight Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                @php
                    $hour = now()->setTimezone('America/Edmonton')->format('H');
                    if ($hour < 12) {
                        $greeting = 'Good morning';
                    } elseif ($hour < 17) {
                        $greeting = 'Good afternoon';
                    } else {
                        $greeting = 'Good evening';
                    }
                @endphp
                {{ $greeting }}, {{ auth()->user()->name }}
            </h2>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <!-- Published Posts -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['total_published_posts'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Published Posts</div>
                    </div>
                </div>

                <!-- Pending Posts -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['pending_posts'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pending Posts</div>
                    </div>
                </div>

                <!-- AI Conversations -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_conversations'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">AI Conversations</div>
                    </div>
                </div>

                <!-- Total Calls -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_call_logs'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Calls</div>
                    </div>
                </div>

                <!-- Transcribed Calls -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['transcribed_calls'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Transcribed Calls</div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                <!-- AI Conversations Widget -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Recent AI Conversations</h3>
                            <a href="{{ route('ai.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View All</a>
                        </div>

                        @if($recentConversations->count() > 0)
                            @foreach($recentConversations as $conversation)
                                <div class="border-l-4 border-blue-400 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20 p-3 mb-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $conversation->first_name }} {{ $conversation->last_name }}</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $conversation->phone }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $conversation->created_at->format('M j, Y g:ia') }}</div>
                                        </div>
                                        @if($conversation->conversation_id)
                                            <a href="{{ route('ai.conversation.show', $conversation) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View Details</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">No AI conversations yet</div>
                        @endif
                    </div>
                </div>

                <!-- Ring Central Call Logs Widget -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Recent Ring Central Calls</h3>
                            <a href="{{ route('ringcentral.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View All</a>
                        </div>

                        @if($recentCallLogs->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-2 py-2 text-left">Date</th>
                                            <th class="px-2 py-2 text-left">From</th>
                                            <th class="px-2 py-2 text-left">To</th>
                                            <th class="px-2 py-2 text-left">Duration</th>
                                            <th class="px-2 py-2 text-left">Transcript</th>
                                            <th class="px-2 py-2 text-left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentCallLogs->take(5) as $callLog)
                                            <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-gray-100">{{ Carbon\Carbon::parse($callLog->start_time)->setTimezone('America/Edmonton')->format('M j, g:ia') }}</td>
                                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-gray-100">{{ App\Models\CallLog::cleanFromName($callLog->from_name) ?: 'Unknown' }}</td>
                                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-gray-100">{{ $callLog->to }}</td>
                                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-gray-100">{{ gmdate('i:s', $callLog->duration) }}</td>
                                                <td class="px-2 py-2">
                                                    @if($callLog->transcription)
                                                        <svg class="w-4 h-4 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                </td>
                                                <td class="px-2 py-2">
                                                    <a href="{{ route('ringcentral.details', $callLog->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">No call logs available</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- LinkedIn Posts Widget -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">LinkedIn Posts</h3>
                        <a href="{{ route('posts') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View All</a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            @if($pendingPosts->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-orange-600 dark:text-orange-400 mb-2">Pending Posts ({{ $pendingPosts->count() }})</h4>
                                    @foreach($pendingPosts as $post)
                                        <div class="border-l-4 border-orange-400 dark:border-orange-500 bg-orange-50 dark:bg-orange-900/20 p-3 mb-2">
                                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $post->title }}</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Created: {{ $post->created_at->format('M j, Y g:ia') }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 dark:text-gray-400 py-4">No pending posts</div>
                            @endif
                        </div>

                        <div>
                            @if($recentPublishedPosts->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-green-600 dark:text-green-400 mb-2">Recent Published</h4>
                                    @foreach($recentPublishedPosts as $post)
                                        <div class="border-l-4 border-green-400 dark:border-green-500 bg-green-50 dark:bg-green-900/20 p-3 mb-2">
                                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $post->title }}</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Published: {{ Carbon\Carbon::parse($post->published_at)->format('M j, Y g:ia') }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 dark:text-gray-400 py-4">No published posts yet</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('posts') }}" class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                            Manage LinkedIn Posts
                        </a>
                        <a href="{{ route('ai.index') }}" class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                            Make AI Call
                        </a>
                        <a href="{{ route('ringcentral.index') }}" class="bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                            View Call Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
