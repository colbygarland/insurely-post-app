<x-app-layout>
  <x-slot name="title">
    Conversation Details
  </x-slot>
    <div class="py-12 mt-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Session::has('successMessage'))
                <x-alert type="success" :message="Session::get('successMessage')" />
            @endif
            @if(Session::has('errorMessage'))
                <x-alert type="error" :message="Session::get('errorMessage')" />
            @endif
            
            <!-- Main Conversation Card -->
            <div class="bg-white dark:bg-gray-800 dark:text-gray-200 overflow-hidden shadow-xl sm:rounded-lg">
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
                            <p class="text-blue-100 mt-1">Call summary</p>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="px-6 py-8">
                    <div class="grid gap-6">
                        <!-- Contact Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 dark:text-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Contact Information
                            </h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="flex items-center p-3 bg-white dark:bg-gray-700 rounded-md border dark:border-gray-600">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-200">Name</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $conversation->first_name }} {{ $conversation->last_name }}</p>
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
                                            @if($conversation->phone)
                                                {{ $conversation->phone }}
                                            @else
                                                <span class="text-gray-400">Not provided</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white dark:bg-gray-700 rounded-md border dark:border-gray-600">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-200">Email</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $conversation->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white dark:bg-gray-700 rounded-md border dark:border-gray-600">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-200">ElevenLabs Conversation</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                                            @if($conversation->conversation_id)
                                                                                               <a href="https://elevenlabs.io/app/conversational-ai/history/{{ $conversation->conversation_id }}" target="_blank" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors">
                                                    View in ElevenLabs
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                </a>
                                            @else
                                                <span class="text-gray-400">Not provided</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                ElevenLabs Summary
                            </h3>
                            <div class="bg-gray-50 dark:bg-gray-600 dark:text-gray-200 rounded-lg p-4 border-l-4 border-purple-400">
                                @if($summary)
                                    <div class="text-gray-700 dark:text-gray-200 leading-relaxed prose prose-sm max-w-none">{!! $summary !!}</div>
                                @else
                                    <p class="text-gray-400 italic">No summary available</p>
                                @endif
                            </div>
                        </div>

                        <!-- Message Content -->
                        <div class="bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                Call Content
                            </h3>
                            <div class="bg-gray-50 dark:bg-gray-600 dark:text-gray-200 rounded-lg p-4 border-l-4 border-blue-400">
                                @if($conversation->message)
                                    <div class="text-gray-700 dark:text-gray-200 leading-relaxed prose prose-sm max-w-none">{!! $conversation->message !!}</div>
                                @else
                                    <p class="text-gray-400 italic">No message content available</p>
                                @endif
                            </div>
                        </div>

                        <!-- AI Analysis Result -->
                        @if($conversation->analyze_result)
                            <div class="bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4 flex items-center" id="analysis">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423L16.5 15.75l.394 1.183a2.25 2.25 0 001.423 1.423L19.5 18.75l-1.183.394a2.25 2.25 0 00-1.423 1.423z"></path>
                                    </svg>
                                    AI Analysis
                                </h3>
                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900 dark:to-pink-900 rounded-lg p-4 border-l-4 border-purple-400">
                                    <div class="text-gray-700 dark:text-gray-200 leading-relaxed prose prose-sm max-w-none">@markdown($conversation->analyze_result)</div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4">
                                                                 <a href="{{ route('ai.conversation.destroy', $conversation->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                     <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                     </svg>
                                     Delete Conversation
                                 </a>
                            @if($conversation->message && !$conversation->analyze_result)
                                 <form method="POST" action="{{ route('ai.conversation.analyze', $conversation->id) }}" class="inline" id="ai-analyze-form">
                                     @csrf
                                     <button type="submit" id="ai-analyze-btn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                         <span id="btn-content" class="flex items-center">
                                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423L16.5 15.75l.394 1.183a2.25 2.25 0 001.423 1.423L19.5 18.75l-1.183.394a2.25 2.25 0 00-1.423 1.423z"></path>
                                             </svg>
                                             Push to AI for Review
                                         </span>
                                         <span id="btn-loading" class="hidden flex items-center">
                                             <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                             </svg>
                                             Analyzing...
                                         </span>
                                     </button>
                                 </form>

                                 <script>
                                     document.getElementById('ai-analyze-form').addEventListener('submit', function(e) {
                                         const btn = document.getElementById('ai-analyze-btn');
                                         const btnContent = document.getElementById('btn-content');
                                         const btnLoading = document.getElementById('btn-loading');
                                         
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