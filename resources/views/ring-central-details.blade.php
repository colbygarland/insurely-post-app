<x-app-layout>

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
            <!-- Main Conversation Card -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Header Section -->
                <div class="bg-gradient-to-r bg-primary px-6 py-8">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold text-white">Conversation Details</h1>
                            <p class="text-blue-100 mt-1">Ring Central call summary</p>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="px-6 py-8">
                    <div class="grid gap-6">
                        <!-- Contact Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                Call Information
                            </h3>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="flex items-center p-3 bg-white rounded-md border">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">From</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $callLog->from_name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white rounded-md border">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Phone Number</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            @if($callLog->from_phone_number)
                                                {{ $callLog->to }}
                                            @else
                                                <span class="text-gray-400">Not provided</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white rounded-md border">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500">Result</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $callLog->result }}</p>
                                    </div>
                                </div>
                                                               
                             </div>
                         </div>

                         <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Call Audio
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-teal-500">
                                @if($callLog->url)  
                                    <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                                        <audio controls>
                                            <source src="{{ $callLog->url }}?access_token={{ $accessToken }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                @else
                                    <p class="text-gray-400 italic">No audio available</p>
                                @endif
                            </div>
                        </div>

                        <!-- Transcript Section -->
                        @if($callLog->transcription)
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Call Transcript
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-green-400">
                                    <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">{!! nl2br(e($callLog->transcription)) !!}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4">
                            @if($callLog->url && !$callLog->transcription)
                                <form method="POST" action="{{ route('calllog.transcript', $callLog->id) }}" class="inline" id="transcript-form">
                                    @csrf
                                    <button type="submit" id="transcript-btn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span id="transcript-btn-content" class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Get Transcript
                                        </span>
                                        <span id="transcript-btn-loading" class="hidden flex items-center">
                                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Generating Transcript...
                                        </span>
                                    </button>
                                </form>

                                <script>
                                    document.getElementById('transcript-form').addEventListener('submit', function(e) {
                                        const btn = document.getElementById('transcript-btn');
                                        const btnContent = document.getElementById('transcript-btn-content');
                                        const btnLoading = document.getElementById('transcript-btn-loading');
                                        
                                        // Show loading state
                                        btnContent.classList.add('hidden');
                                        btnLoading.classList.remove('hidden');
                                        btn.disabled = true;
                                    });
                                </script>
                            @endif
                        </div>

                        

                     
                     </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>