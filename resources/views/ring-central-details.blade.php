<x-app-layout>
    <x-slot name="title">
        Ring Central Details
    </x-slot>
    
    <div class="py-12 mt-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <a onclick="window.history.back()" class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Call Logs
            </a>
        </div>
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Session::has('successMessage'))
                <x-alert type="success" :message="Session::get('successMessage')" />
            @endif
            
            @if(Session::has('errorMessage'))
                <x-alert type="error" :message="Session::get('errorMessage')" />
            @endif
            
            <!-- Main Conversation Card -->
            <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-xl sm:rounded-lg mb-8">
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
                            <h1 class="text-2xl font-bold text-white dark:text-gray-200">Conversation Details</h1>
                            <p class="text-blue-100 mt-1">Ring Central call summary</p>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="px-6 py-8">
                    <div class="grid gap-6">
                        <!-- Contact Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 dark:text-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4 flex items-center">
                                Call Information
                            </h3>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="flex items-center p-3 bg-white dark:bg-gray-700 rounded-md border dark:border-gray-600">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-200">From</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $callLog->from_name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white dark:bg-gray-700 rounded-md border dark:border-gray-600">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-200">Phone Number</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                                            @if($callLog->from_phone_number)
                                                {{ $callLog->to }}
                                            @else
                                                <span class="text-gray-400">Not provided</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white dark:bg-gray-700 rounded-md border dark:border-gray-600">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-200">Call Date</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ Carbon\Carbon::parse($callLog->start_time)->setTimezone('America/Edmonton')->format('F j, Y g:ia') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Call Audio Section -->
                        <div class="bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Call Audio
                            </h3>
                            <div class="bg-gray-50 dark:bg-gray-600 dark:text-gray-200 rounded-lg p-4 border-l-4 border-teal-500">
                                @if($callLog->url)
                                    <div class="text-gray-700 dark:text-gray-200 leading-relaxed prose prose-sm max-w-none">
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

                        <!-- Summary Section -->
                        @if($callLog->summary)
                            <div class="bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 flex items-center">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Call Summary
                                    </h3>
                                    <button type="button" 
                                            id="copySummaryBtn"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span id="copySummaryBtnText">Copy</span>
                                    </button>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-600 dark:text-gray-200 rounded-lg p-4 border-l-4 border-purple-400">
                                    <div id="summaryContent" class="text-gray-700 dark:text-gray-200 leading-relaxed prose prose-sm max-w-none">{!! nl2br(e($callLog->summary)) !!}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Transcript Section -->
                        @if($callLog->transcription)
                            <div class="bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 flex items-center">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Call Transcript
                                    </h3>
                                    <button type="button" 
                                            id="copyTranscriptBtn"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span id="copyBtnText">Copy</span>
                                    </button>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-600 dark:text-gray-200 rounded-lg p-4 border-l-4 border-green-400">
                                    <div id="transcriptContent" class="text-gray-700 dark:text-gray-200 leading-relaxed prose prose-sm max-w-none">{!! nl2br(e($callLog->transcription)) !!}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Analysis Section -->
                        @if($callLog->analysis)
                            <div class="bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 flex items-center">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Call Analysis
                                    </h3>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-600 dark:text-gray-200 rounded-lg p-4 border-l-4 border-teal-400">
                                    <div class="text-gray-700 dark:text-gray-200 leading-relaxed prose prose-sm max-w-none">{!! nl2br(e($callLog->analysis)) !!}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4">
                            @if($callLog->url && !$callLog->transcription)
                                <form method="POST" action="{{ route('calllog.transcript-only', $callLog->id) }}" class="inline" id="transcript-form">
                                    @csrf
                                    <button type="submit" id="transcript-btn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span id="transcript-btn-content" class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Generate Transcript
                                        </span>
                                        <span id="transcript-btn-loading" class="hidden flex items-center">
                                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Generating...
                                        </span>
                                    </button>
                                </form>
                            @endif

                            @if($callLog->transcription && (!$callLog->summary || !$callLog->analysis))
                                <form method="POST" action="{{ route('calllog.summary-analysis', $callLog->id) }}" class="inline" id="summary-analysis-form">
                                    @csrf
                                    <button type="submit" id="summary-analysis-btn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span id="summary-analysis-btn-content" class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                            Generate Summary + Analysis
                                        </span>
                                        <span id="summary-analysis-btn-loading" class="hidden flex items-center">
                                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Generating...
                                        </span>
                                    </button>
                                </form>
                            @endif

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Handle transcript form submission
                                    const transcriptForm = document.getElementById('transcript-form');
                                    if (transcriptForm) {
                                        transcriptForm.addEventListener('submit', function(e) {
                                            const btn = document.getElementById('transcript-btn');
                                            const btnContent = document.getElementById('transcript-btn-content');
                                            const btnLoading = document.getElementById('transcript-btn-loading');
                                            
                                            // Show loading state
                                            btnContent.classList.add('hidden');
                                            btnLoading.classList.remove('hidden');
                                            btn.disabled = true;
                                        });
                                    }

                                    // Handle summary+analysis form submission
                                    const summaryAnalysisForm = document.getElementById('summary-analysis-form');
                                    if (summaryAnalysisForm) {
                                        summaryAnalysisForm.addEventListener('submit', function(e) {
                                            const btn = document.getElementById('summary-analysis-btn');
                                            const btnContent = document.getElementById('summary-analysis-btn-content');
                                            const btnLoading = document.getElementById('summary-analysis-btn-loading');
                                            
                                            // Show loading state
                                            btnContent.classList.add('hidden');
                                            btnLoading.classList.remove('hidden');
                                            btn.disabled = true;
                                        });
                                    }
                                });
                            </script>
                        </div>

                            <!-- Copy to Clipboard Script -->
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Transcript
                                    const copyBtn = document.getElementById('copyTranscriptBtn');
                                    const copyBtnText = document.getElementById('copyBtnText');
                                    const transcriptContent = document.getElementById('transcriptContent');

                                    // Summary
                                    const copySummaryBtn = document.getElementById('copySummaryBtn');
                                    const copySummaryBtnText = document.getElementById('copySummaryBtnText');
                                    const summaryContent = document.getElementById('summaryContent');
                                    
                                    // Transcript
                                    if (copyBtn && transcriptContent) {
                                        copyBtn.addEventListener('click', async function() {
                                            try {
                                                // Get the raw transcript text (strip HTML and convert line breaks)
                                                const transcriptText = transcriptContent.innerText || transcriptContent.textContent;
                                                
                                                // Copy to clipboard
                                                await navigator.clipboard.writeText(transcriptText);
                                                
                                                // Update button to show success
                                                const originalText = copyBtnText.textContent;
                                                copyBtnText.textContent = 'Copied!';
                                                copyBtn.classList.add('text-green-600', 'dark:text-green-400');
                                                
                                                // Reset button after 2 seconds
                                                setTimeout(() => {
                                                    copyBtnText.textContent = originalText;
                                                    copyBtn.classList.remove('text-green-600', 'dark:text-green-400');
                                                }, 2000);
                                                
                                            } catch (err) {
                                                // Fallback for older browsers
                                                console.error('Failed to copy transcript: ', err);
                                                
                                                // Create a temporary textarea element
                                                const textarea = document.createElement('textarea');
                                                textarea.value = transcriptContent.innerText || transcriptContent.textContent;
                                                document.body.appendChild(textarea);
                                                textarea.select();
                                                
                                                try {
                                                    document.execCommand('copy');
                                                    copyBtnText.textContent = 'Copied!';
                                                    copyBtn.classList.add('text-green-600', 'dark:text-green-400');
                                                    
                                                    setTimeout(() => {
                                                        copyBtnText.textContent = 'Copy';
                                                        copyBtn.classList.remove('text-green-600', 'dark:text-green-400');
                                                    }, 2000);
                                                } catch (fallbackErr) {
                                                    copyBtnText.textContent = 'Failed';
                                                    setTimeout(() => {
                                                        copyBtnText.textContent = 'Copy';
                                                    }, 2000);
                                                }
                                                
                                                document.body.removeChild(textarea);
                                            }
                                        });
                                    }

                                    // Summary
                                    if (copySummaryBtn && summaryContent) {
                                        copySummaryBtn.addEventListener('click', async function() {
                                            try {
                                                // Get the raw summary text (strip HTML and convert line breaks)
                                                const summaryText = summaryContent.innerText || summaryContent.textContent;
                                                
                                                // Copy to clipboard
                                                await navigator.clipboard.writeText(summaryText);
                                                
                                                // Update button to show success
                                                const originalText = copySummaryBtnText.textContent;
                                                copySummaryBtnText.textContent = 'Copied!';
                                                copySummaryBtn.classList.add('text-green-600', 'dark:text-green-400');
                                                
                                                // Reset button after 2 seconds
                                                setTimeout(() => {
                                                    copySummaryBtnText.textContent = originalText;
                                                    copySummaryBtn.classList.remove('text-green-600', 'dark:text-green-400');
                                                }, 2000);
                                            } catch (err) {
                                                // Fallback for older browsers
                                                console.error('Failed to copy summary: ', err);
                                            }
                                        });
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>