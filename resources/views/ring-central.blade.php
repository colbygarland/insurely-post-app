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
            @if(Session::has('successMessage'))
                <div class="bg-green-200 text-green-900 dark:text-green-900 inline-block rounded-lg py-2 px-4 mb-4">
                    {{ Session::get('successMessage') }}
                </div>
            @endif

            @if(Session::has('errorMessage'))
                <div class="bg-red-200 text-red-900 dark:text-red-900 inline-block rounded-lg py-2 px-4 mb-4">
                    {{ Session::get('errorMessage') }}
                </div>
            @endif
            <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg mb-16 p-6">
                <div class="flex flex-col gap-4 mb-4">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Calls from Ring Central
                    </h2>
                    
                    <!-- Filters -->
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Date Range Filter -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600 dark:text-gray-200 flex items-center gap-2">
                                From:
                                <input type="date" id="startDate" 
                                       value="{{ request('start_date') }}"
                                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </label>
                            <label class="text-sm text-gray-600 dark:text-gray-200 flex items-center gap-2">
                                To:
                                <input type="date" id="endDate" 
                                       value="{{ request('end_date') }}"
                                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </label>
                        </div>
                        
                        @if(Gate::allows('is-admin'))
                            <!-- Caller Filter -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600 dark:text-gray-200 flex items-center gap-2">
                                    Filter by caller:
                                    <select id="fromNameFilter" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
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
                        <button type="button" id="clearFilters" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded transition-colors">
                            Clear Filters
                        </button>
                    </div>
                </div>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:text-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Duration
                            </th>
                            <th scope="col" class="px-6 py-3">
                                From
                            </th>
                            <th scope="col" class="px-6 py-3">
                                To
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Transcript Generated
                            </th>
                            <th scope="col" class="px-6 py-3">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($callLogs as $callLog)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                               {{ Carbon\Carbon::parse($callLog->start_time)->setTimezone('America/Edmonton')->format('F j, Y g:ia')}}
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
                                <a href="{{ route('ringcentral.details', $callLog->id) }}" class="text-blue-500 font-bold">View Details</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination Info -->
                <div class="mt-4 flex justify-between items-center gap-4">
                    <div class="text-sm text-gray-500">
                        Showing {{ $callLogs->firstItem() }} to {{ $callLogs->lastItem() }} of {{ $callLogs->total() }} results
                        @if($fromNameFilter !== 'all' || request('start_date') || request('end_date'))
                            <div class="mt-1 flex flex-wrap gap-2">
                                @if($fromNameFilter !== 'all')
                                    <span class="text-indigo-600 font-medium">Caller: {{ $fromNameFilter }}</span>
                                @endif
                                @if(request('start_date'))
                                    <span class="text-indigo-600 font-medium">From: {{ Carbon\Carbon::parse(request('start_date'))->format('M j, Y') }}</span>
                                @endif
                                @if(request('end_date'))
                                    <span class="text-indigo-600 font-medium">To: {{ Carbon\Carbon::parse(request('end_date'))->format('M j, Y') }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $callLogs->appends(request()->query())->links() }}
                    </div>
                </div>
</div>
          
         
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
