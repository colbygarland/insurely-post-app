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
            @if(Gate::allows('is-admin'))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
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
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total RC Calls</div>
                        </div>
                    </div>

                    <!-- Transcribed Calls -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['transcribed_calls'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Transcribed RC Calls</div>
                        </div>
                    </div>

                     <!-- Published Posts -->
                     <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['total_published_posts'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Published LinkedIn Posts</div>
                        </div>
                    </div>

                    <!-- Pending Posts -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['pending_posts'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Pending LinkedIn Posts</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Call Logs Distribution Chart -->
            @if(Gate::allows('is-admin') && $stats['call_logs_per_person']->count() > 0)
                <div class="mb-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-8">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Ring Central Call Distribution by Person This Month</h3>
                                <div class="mt-2 flex items-center justify-center gap-2">
                                    <button type="button" id="dateRangeButton" class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 mb-2 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span id="selectedDateRange">{{ $stats['current_month'] }}</span>
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Tooltip Icon -->
                                    <div class="relative inline-block mb-2">
                                        <div class="tooltip-trigger cursor-help text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        
                                        <!-- Tooltip Content -->
                                        <div class="tooltip-content absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-sm text-white bg-gray-900 dark:bg-gray-700 rounded-lg shadow-lg opacity-0 invisible transition-all duration-200 whitespace-nowrap z-10">
                                            <div class="text-center">
                                                <div class="font-medium">Important Note</div>
                                                <div class="text-xs text-gray-300 dark:text-gray-400 mt-1">Data before July 2025 is not available yet.</div>
                                            </div>
                                            <!-- Tooltip Arrow -->
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col lg:flex-row items-center justify-center gap-8">
                                <!-- Chart Container -->
                                <div class="flex-shrink-0">
                                    <div style="position: relative; height: 400px; width: 400px;">
                                        <canvas id="callLogsChart"></canvas>
                                    </div>
                                </div>
                                
                                <!-- Stats Summary -->
                                <div class="flex-1 max-w-md">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" data-stat="total_calls">{{ $stats['call_logs_per_person']->sum() }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Calls</div>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-400" data-stat="active_members">{{ $stats['call_logs_per_person']->count() }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Active Members</div>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" data-stat="avg_calls">{{ $stats['call_logs_per_person']->count() > 0 ? number_format($stats['call_logs_per_person']->avg(), 1) : '0' }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Avg Calls</div>
                                        </div>
                                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" data-stat="highest_calls">{{ $stats['call_logs_per_person']->count() > 0 ? $stats['call_logs_per_person']->max() : '0' }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Highest Calls Per Person</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Top Performers -->
                                    <div class="mt-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Top Performers - {{ $stats['current_month'] }}</h4>
                                        @if($stats['call_logs_per_person']->count() > 0)
                                            <div class="space-y-2 top-performers-list">
                                                @foreach($stats['call_logs_per_person']->sortDesc()->take(3) as $name => $count)
                                                    <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $name }}</span>
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $count }} calls</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center p-4 text-gray-500 dark:text-gray-400">
                                                <svg class="w-8 h-8 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                                <p class="text-sm">No calls recorded this month</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Date Range Modal -->
            <div id="dateRangeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Select Date Range</h3>
                            <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Quick Presets -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Select</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" class="preset-btn px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-preset="current-month">This Month</button>
                                    <button type="button" class="preset-btn px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-preset="last-month">Last Month</button>
                                    <button type="button" class="preset-btn px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-preset="last-3-months">Last 3 Months</button>
                                    <button type="button" class="preset-btn px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-preset="last-6-months">Last 6 Months</button>
                                </div>
                            </div>
                            
                            <!-- Custom Date Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Custom Range</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">From</label>
                                        <input type="date" id="startDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">To</label>
                                        <input type="date" id="endDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-300">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6 space-x-3">
                            <button type="button" id="cancelBtn" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Cancel</button>
                            <button type="button" id="applyBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md">Apply</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                @if(Gate::allows('is-admin'))
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
                @endif
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
            @if(Gate::allows('is-admin'))
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
            @endif
            
            @if(Gate::allows('is-admin'))
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
            @endif
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Data from PHP -->
    <script>
        window.callLogsData = <?php echo json_encode($stats['call_logs_per_person']); ?>;
    </script>
    
    <script>
        let chart; // Global chart variable
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize chart with current data
            initializeChart();
            
            // Initialize tooltip functionality
            initializeTooltip();
            
            // Watch for dark mode changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        // Dark mode changed, update chart colors
                        updateChartColors();
                        if (chart) {
                            chart.update();
                        }
                    }
                });
            });
            
            // Start observing the document element for class changes
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
            
            // Modal functionality
            const modal = document.getElementById('dateRangeModal');
            const dateRangeButton = document.getElementById('dateRangeButton');
            const closeModal = document.getElementById('closeModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const applyBtn = document.getElementById('applyBtn');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            
            // Open modal
            dateRangeButton.addEventListener('click', function() {
                modal.classList.remove('hidden');
                // Set current dates
                const today = new Date();
                endDate.value = today.toISOString().split('T')[0];
                startDate.value = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            });
            
            // Close modal events
            [closeModal, cancelBtn].forEach(btn => {
                btn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            });
            
            // Close modal on backdrop click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
            
            // Preset buttons
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const preset = this.dataset.preset;
                    const dates = getPresetDates(preset);
                    startDate.value = dates.start;
                    endDate.value = dates.end;
                });
            });
            
            // Apply button
            applyBtn.addEventListener('click', function() {
                const start = startDate.value;
                const end = endDate.value;
                
                if (!start || !end) {
                    alert('Please select both start and end dates');
                    return;
                }
                
                if (new Date(start) > new Date(end)) {
                    alert('Start date cannot be later than end date');
                    return;
                }
                
                updateChartData(start, end);
                modal.classList.add('hidden');
            });
        });
        
        function initializeChart() {
            const ctx = document.getElementById('callLogsChart').getContext('2d');
            const callLogsData = window.callLogsData || {};
            
            chart = createChart(ctx, callLogsData);
        }
        
        function createChart(ctx, callLogsData) {
            const labels = Object.keys(callLogsData);
            const data = Object.values(callLogsData);
            
            // Comprehensive unique color palette
            const colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4',
                '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE',
                '#85C1E9', '#F8C471', '#82E0AA', '#F1948A', '#AED6F1',
                '#A9DFBF', '#F9E79F', '#D7BDE2', '#A3E4D7', '#FADBD8',
                '#D5DBDB', '#FCF3CF', '#EBDEF0', '#D1F2EB', '#FDF2E9',
                '#E8F8F5', '#FEF9E7', '#F4F6F7', '#E74C3C', '#3498DB',
                '#2ECC71', '#F39C12', '#9B59B6', '#1ABC9C', '#E67E22',
                '#34495E', '#16A085', '#27AE60', '#2980B9', '#8E44AD',
                '#F1C40F', '#E74C3C', '#95A5A6', '#D35400', '#C0392B'
            ];
            
            // Generate additional colors if needed
            function generateColor(index) {
                const hue = (index * 137.508) % 360; // Golden angle approximation
                return `hsl(${hue}, 70%, 60%)`;
            }
            
            // Ensure we have enough unique colors
            const neededColors = labels.length;
            const availableColors = [...colors];
            
            // Add generated colors if we need more
            for (let i = colors.length; i < neededColors; i++) {
                availableColors.push(generateColor(i));
            }
            
            const backgroundColors = availableColors.slice(0, labels.length);
            const borderColors = backgroundColors.map(color => {
                if (color.startsWith('#')) {
                    return color + '80';
                } else {
                    // For HSL colors, convert to HSLA with opacity
                    return color.replace('hsl(', 'hsla(').replace(')', ', 0.8)');
                }
            });
            
            // Get current dark mode colors
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#F3F4F6' : '#374151';
            const tooltipBg = isDarkMode ? '#374151' : '#FFFFFF';
            const tooltipBorder = isDarkMode ? '#4B5563' : '#E5E7EB';
            
            return new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                },
                                color: textColor
                            }
                        },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: tooltipBorder,
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} calls (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function getPresetDates(preset) {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth();
            
            switch(preset) {
                case 'current-month':
                    return {
                        start: new Date(year, month, 1).toISOString().split('T')[0],
                        end: today.toISOString().split('T')[0]
                    };
                case 'last-month':
                    const lastMonth = new Date(year, month - 1, 1);
                    const lastMonthEnd = new Date(year, month, 0);
                    return {
                        start: lastMonth.toISOString().split('T')[0],
                        end: lastMonthEnd.toISOString().split('T')[0]
                    };
                case 'last-3-months':
                    return {
                        start: new Date(year, month - 3, 1).toISOString().split('T')[0],
                        end: today.toISOString().split('T')[0]
                    };
                case 'last-6-months':
                    return {
                        start: new Date(year, month - 6, 1).toISOString().split('T')[0],
                        end: today.toISOString().split('T')[0]
                    };
                default:
                    return {
                        start: new Date(year, month, 1).toISOString().split('T')[0],
                        end: today.toISOString().split('T')[0]
                    };
            }
        }
        
        function updateChartData(startDate, endDate) {
            // Show loading state
            document.getElementById('selectedDateRange').textContent = 'Loading...';
            
            // Make AJAX request
            fetch('/dashboard/chart-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDate
                })
            })
            .then(response => response.json())
            .then(data => {
                // Update chart
                const labels = Object.keys(data.call_logs_per_person);
                const chartData = Object.values(data.call_logs_per_person);
                
                chart.data.labels = labels;
                chart.data.datasets[0].data = chartData;
                
                // Update colors for new data
                updateChartColors();
                
                chart.update();
                
                // Update stats
                updateStats(data);
                
                // Update date range display
                document.getElementById('selectedDateRange').textContent = data.date_range_label;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update chart data');
                document.getElementById('selectedDateRange').textContent = 'Error';
            });
        }
        
        function updateChartColors() {
            if (!chart) return;
            
            // Get current dark mode state
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#F3F4F6' : '#374151';
            const tooltipBg = isDarkMode ? '#374151' : '#FFFFFF';
            const tooltipBorder = isDarkMode ? '#4B5563' : '#E5E7EB';
            
            // Update legend colors
            chart.options.plugins.legend.labels.color = textColor;
            
            // Update tooltip colors
            chart.options.plugins.tooltip.backgroundColor = tooltipBg;
            chart.options.plugins.tooltip.titleColor = textColor;
            chart.options.plugins.tooltip.bodyColor = textColor;
            chart.options.plugins.tooltip.borderColor = tooltipBorder;
            
            // Update dataset colors if needed
            const labels = chart.data.labels;
            if (labels && labels.length > 0) {
                const colors = [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                    '#FF9F40', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4',
                    '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE',
                    '#85C1E9', '#F8C471', '#82E0AA', '#F1948A', '#AED6F1',
                    '#A9DFBF', '#F9E79F', '#D7BDE2', '#A3E4D7', '#FADBD8',
                    '#D5DBDB', '#FCF3CF', '#EBDEF0', '#D1F2EB', '#FDF2E9',
                    '#E8F8F5', '#FEF9E7', '#F4F6F7', '#E74C3C', '#3498DB',
                    '#2ECC71', '#F39C12', '#9B59B6', '#1ABC9C', '#E67E22',
                    '#34495E', '#16A085', '#27AE60', '#2980B9', '#8E44AD',
                    '#F1C40F', '#E74C3C', '#95A5A6', '#D35400', '#C0392B'
                ];
                
                function generateColor(index) {
                    const hue = (index * 137.508) % 360;
                    return `hsl(${hue}, 70%, 60%)`;
                }
                
                const neededColors = labels.length;
                const availableColors = [...colors];
                
                for (let i = colors.length; i < neededColors; i++) {
                    availableColors.push(generateColor(i));
                }
                
                const backgroundColors = availableColors.slice(0, labels.length);
                const borderColors = backgroundColors.map(color => {
                    if (color.startsWith('#')) {
                        return color + '80';
                    } else {
                        return color.replace('hsl(', 'hsla(').replace(')', ', 0.8)');
                    }
                });
                
                chart.data.datasets[0].backgroundColor = backgroundColors;
                chart.data.datasets[0].borderColor = borderColors;
            }
        }
        
        function updateStats(data) {
            // Update the stats cards
            const statsCards = document.querySelectorAll('[data-stat]');
            statsCards.forEach(card => {
                const statType = card.dataset.stat;
                if (data.stats[statType] !== undefined) {
                    card.textContent = data.stats[statType];
                }
            });
            
            // Update top performers
            const topPerformersContainer = document.querySelector('.top-performers-list');
            if (topPerformersContainer && data.top_performers) {
                topPerformersContainer.innerHTML = '';
                data.top_performers.forEach(performer => {
                    const div = document.createElement('div');
                    div.className = 'flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded';
                    div.innerHTML = `
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">${performer.name}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">${performer.count} calls</span>
                    `;
                    topPerformersContainer.appendChild(div);
                });
                         }
         }
         
         function initializeTooltip() {
             const tooltipTrigger = document.querySelector('.tooltip-trigger');
             const tooltipContent = document.querySelector('.tooltip-content');
             
             if (tooltipTrigger && tooltipContent) {
                 tooltipTrigger.addEventListener('mouseenter', function() {
                     tooltipContent.classList.remove('opacity-0', 'invisible');
                     tooltipContent.classList.add('opacity-100', 'visible');
                 });
                 
                 tooltipTrigger.addEventListener('mouseleave', function() {
                     tooltipContent.classList.remove('opacity-100', 'visible');
                     tooltipContent.classList.add('opacity-0', 'invisible');
                 });
             }
         }
    </script>
    
    <style>
        .tooltip-content {
            min-width: 200px;
        }
        
        .tooltip-content.visible {
            visibility: visible;
        }
        
        .tooltip-content.invisible {
            visibility: hidden;
        }
    </style>
</x-app-layout>
