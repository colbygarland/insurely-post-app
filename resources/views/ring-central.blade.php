<x-app-layout>
    <x-slot name="title">
        Ring Central
    </x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ring Central') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if(Session::has('successMessage'))
                <x-alert type="success" :message="Session::get('successMessage')" />
            @endif
            
            @if(Session::has('errorMessage'))
                <x-alert type="error" :message="Session::get('errorMessage')" />
            @endif

            @if(Gate::allows('is-admin'))
                <!-- Stats Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Calls -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Calls</div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_calls'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transcribed Calls -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 3a1 1 0 100 2h6a1 1 0 100-2H7zm0 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Transcribed</div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['transcribed_calls'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Duration -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Duration</div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_duration'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transcription Cost -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        Transcription Cost
                                        <div class="relative group">
                                            <svg class="w-4 h-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none w-64">
                                                <div class="font-semibold mb-1">How is this calculated?</div>
                                                <div class="text-gray-200 dark:text-gray-300">
                                                    Based on Gemini 2.5-Flash pricing: $0.92 per 1M tokens (blended rate of 80% input @ $1.00 and 20% output @ $0.60 per 1M tokens).
                                                </div>
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_transcription_cost'], 4) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Call Logs Section -->
            <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg p-6 mb-8">
                <div class="flex flex-col gap-4 mb-6">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Calls from Ring Central
                    </h2>
                    
                    <!-- Filters -->
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Date Range Filter -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600 dark:text-gray-200 flex items-center gap-2">
                                From:
                                <input type="date" 
                                       id="startDate" 
                                       value="{{ $startDate }}"
                                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </label>
                            <label class="text-sm text-gray-600 dark:text-gray-200 flex items-center gap-2">
                                To:
                                <input type="date" 
                                       id="endDate" 
                                       value="{{ $endDate }}"
                                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </label>
                        </div>
                        
                        @if(Gate::allows('is-admin'))
                            <!-- Caller Filter -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600 dark:text-gray-200 flex items-center gap-2">
                                    Filter by caller:
                                    <select id="fromNameFilter" 
                                            class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="all" {{ $fromNameFilter === 'all' ? 'selected' : '' }}>All Callers</option>
                                        @foreach($fromNames as $fromName)
                                            <option value="{{ $fromName }}" {{ $fromNameFilter === $fromName ? 'selected' : '' }}>
                                                {{ App\Models\CallLog::cleanFromName($fromName) ?: 'Unknown' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        @endif
                        
                        <!-- Clear Filters Button -->
                        <button type="button" 
                                id="clearFilters" 
                                class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded transition-colors">
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Call Logs Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:text-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">Date</th>
                                <th scope="col" class="px-6 py-3">Duration</th>
                                <th scope="col" class="px-6 py-3">From</th>
                                <th scope="col" class="px-6 py-3">To</th>
                                <th scope="col" class="px-6 py-3">Transcript Generated</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($callLogs as $callLog)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ Carbon\Carbon::parse($callLog->start_time)->setTimezone('America/Edmonton')->format('F j, Y g:ia') }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ gmdate('i:s', $callLog->duration) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ App\Models\CallLog::cleanFromName($callLog->from_name) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $callLog->to }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($callLog->transcription)
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('ringcentral.details', array_merge(['callLog' => $callLog->id], request()->query())) }}" 
                                           class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ $callLogs->firstItem() }} to {{ $callLogs->lastItem() }} of {{ $callLogs->total() }} results
                        
                        @if($fromNameFilter !== 'all' || request('start_date') || request('end_date'))
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if($fromNameFilter !== 'all')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        Caller: {{ $fromNameFilter }}
                                    </span>
                                @endif
                                @if(request('start_date'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        From: {{ Carbon\Carbon::parse($startDate)->format('M j, Y') }}
                                    </span>
                                @endif
                                @if(request('end_date'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        To: {{ Carbon\Carbon::parse($endDate)->format('M j, Y') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="flex justify-center sm:justify-end">
                        {{ $callLogs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            @if(Gate::allows('is-admin'))
                <!-- Prompt Settings -->
                <div class="bg-white dark:bg-gray-800 dark:text-gray-200 overflow-hidden shadow-xl sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Prompt Settings
                        </h2>
                        <div class="mt-4">
                            <form action="{{ route('calllog.update-summary-prompt') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <x-input-label for="summary_prompt" :value="__('Summary Prompt')" />
                                    <x-text-area id="summary_prompt" name="summary_prompt" type="text" class="mt-1 block w-full min-h-32" required value="{{ $summaryPrompt }}"></x-text-area>
                                    <x-input-error class="mt-2" :messages="$errors->get('summary_prompt')" />
                                </div>
                                <x-primary-button>Update Prompt</x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fromNameFilter = document.getElementById('fromNameFilter');
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            const clearFiltersBtn = document.getElementById('clearFilters');
            
            function updateFilters() {
                const currentUrl = new URL(window.location.href);
                
                // Handle caller filter
                if (fromNameFilter) {
                    const selectedValue = fromNameFilter.value;
                    if (selectedValue === 'all') {
                        currentUrl.searchParams.delete('from_name');
                    } else {
                        currentUrl.searchParams.set('from_name', selectedValue);
                    }
                }
                
                // Handle start date
                const startDate = startDateInput.value;
                if (startDate) {
                    currentUrl.searchParams.set('start_date', startDate);
                } else {
                    currentUrl.searchParams.delete('start_date');
                }
                
                // Handle end date
                const endDate = endDateInput.value;
                if (endDate) {
                    currentUrl.searchParams.set('end_date', endDate);
                } else {
                    currentUrl.searchParams.delete('end_date');
                }
                
                // Remove page parameter to start from page 1 when filtering
                currentUrl.searchParams.delete('page');
                
                window.location.href = currentUrl.toString();
            }
            
            // Event listeners
            if (fromNameFilter) {
                fromNameFilter.addEventListener('change', updateFilters);
            }
            
            startDateInput.addEventListener('change', updateFilters);
            endDateInput.addEventListener('change', updateFilters);
            
            // Clear filters functionality
            clearFiltersBtn.addEventListener('click', function() {
                const currentUrl = new URL(window.location.href);
                
                // Remove all filter parameters
                currentUrl.searchParams.delete('from_name');
                currentUrl.searchParams.delete('start_date');
                currentUrl.searchParams.delete('end_date');
                currentUrl.searchParams.delete('page');
                
                window.location.href = currentUrl.toString();
            });
            
            // Validate date range
            function validateDateRange() {
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;
                
                if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                    alert('Start date cannot be later than end date');
                    return false;
                }
                return true;
            }
            
            startDateInput.addEventListener('change', validateDateRange);
            endDateInput.addEventListener('change', validateDateRange);
        });
    </script>
</x-app-layout>
