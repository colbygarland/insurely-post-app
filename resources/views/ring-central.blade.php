<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ring Central') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Session::has('successMessage'))
                <div class="bg-green-200 text-green-900 inline-block rounded-lg py-2 px-4 mb-4">
                    {{ Session::get('successMessage') }}
                </div>
            @endif

            @if(Session::has('errorMessage'))
                <div class="bg-red-200 text-red-900 inline-block rounded-lg py-2 px-4 mb-4">
                    {{ Session::get('errorMessage') }}
                </div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg mb-16 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Calls from Ring Central
                    </h2>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600 flex items-center gap-2">
                            Filter by caller:
                            <select id="fromNameFilter" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="all" {{ $fromNameFilter === 'all' ? 'selected' : '' }}>All Callers</option>
                                @foreach($fromNames as $fromName)
                                    <option value="{{ $fromName }}" {{ $fromNameFilter === $fromName ? 'selected' : '' }}>
                                        {{ $fromName ?: 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Date
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
                        @if($fromNameFilter !== 'all')
                            <span class="ml-2 text-indigo-600 font-medium">(filtered by: {{ $fromNameFilter }})</span>
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
            
            fromNameFilter.addEventListener('change', function() {
                const selectedValue = this.value;
                const currentUrl = new URL(window.location.href);
                
                if (selectedValue === 'all') {
                    currentUrl.searchParams.delete('from_name');
                } else {
                    currentUrl.searchParams.set('from_name', selectedValue);
                }
                
                // Remove page parameter to start from page 1 when filtering
                currentUrl.searchParams.delete('page');
                
                window.location.href = currentUrl.toString();
            });
        });
    </script>
</x-app-layout>
