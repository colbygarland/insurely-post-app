<x-app-layout>
  <x-slot name="title">
    AI Outbound Caller
  </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('AI Outbound Caller') }}
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
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Conversations History
                    </h2>
                    <!-- <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600 dark:text-gray-200">Show voicemail conversations:
                            <input type="checkbox" id="showVoicemail" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </label>
                    </div> -->
                </div>

                <div class="mt-4 relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:text-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    To
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Message
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conversations as $conversation) 
                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ Carbon\Carbon::parse($conversation->created_at)->setTimezone('America/Edmonton')->format('F j, Y g:ia') }}
                                    </th>
                                    <td class="px-6 py-4">{{ $conversation->phone }}</td>
                                    <td class="px-6 py-4">{!! Str::words($conversation->message, 10, '...')  !!}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('ai.conversation.show', $conversation->id) }}" class="text-blue-500 font-bold">View Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Info -->
                <div class="mt-4 flex justify-between items-center gap-4">
                    <div class="text-sm text-gray-500">
                        Showing {{ $conversations->firstItem() }} to {{ $conversations->lastItem() }} of {{ $conversations->total() }} results
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $conversations->links() }}
                    </div>
                </div>
            </div>

            <div class="p-6 pl-0 flex gap-4 dark:text-gray-200">
                <div class="">
                    <x-primary-button-link href="https://fly-metrics.net/d/fly-logs/fly-logs?orgId=1059613&var-app=insurely-outbound-caller-ai">
                        Historical Outbound Caller Logs
                    </x-primary-button-link>
                    <x-primary-button-link href="https://fly.io/apps/insurely-outbound-caller-ai/monitoring">
                        Live Outbound Caller Logs
                    </x-primary-button-link>
                    
                    <p class="mt-4"><strong>Useful queries for the logs:</strong></p>
                    <p>For call summaries, search for: "engagement successfully created for"</p>
                    <p>For transfers, search for: "Outbound call transfer from"</p>
                    <p>For meetings successfully booked, search for: "meeting successfully booked for"</p>
                    <p>For voicemails, search for: "No answer received for"</p>
                </div>
            </div>

            <div class="lg:grid lg:grid-cols-2 lg:gap-6">
                <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg mb-16 lg:mb-0 p-6">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        Make an Outbound Call
                    </h2>
                    
                    <form method="post" action="{{ route('ai.send') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('post')

                        <div>
                            <x-input-label for="firstName" :value="__('First Name')" />
                            <x-text-input id="firstName" name="firstName" type="text" class="mt-1 block w-full" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('firstName')" />
                        </div>

                        <div>
                            <x-input-label for="lastName" :value="__('Last Name')" />
                            <x-text-input id="lastName" name="lastName" type="text" class="mt-1 block w-full" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('lastName')" />
                        </div>

                        <div>
                            <x-input-label for="number" :value="__('Phone Number')" />
                            <x-text-input id="number" name="number" type="text" class="mt-1 block w-full" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('number')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="timezone" :value="__('Timezone')" />
                            <x-text-input id="timezone" name="timezone" type="text" class="mt-1 block w-full" value="America/Edmonton" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                        </div>

                        <div>
                            <x-input-label for="callType" :value="__('Call Type (outbound or rental)')" />
                            <x-text-input id="callType" name="callType" type="text" class="mt-1 block w-full" value="rental" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('callType')" />
                        </div>

                        <input type="hidden" name="isWebUI" value="1">

                        <div class="flex items-center gap-4">
                            <x-primary-button>Initiate Call</x-primary-button>
                        </div>
                    </form>
                </div>

                <div class="">
                    <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg p-6 mb-16">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                            Upload a CSV file
                        </h2>
                        
                        <p>
                            The CSV file should have the following columns: 
                            <strong>ID</strong>, <strong>First Name</strong>, <strong>Last Name</strong>, 
                            <strong>Email</strong>, <strong>Phone Number</strong>. 
                            The header should always be the first row.
                        </p>
                        
                        <form method="post" action="{{ route('ai.upload') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('post')

                            <div>
                                <x-input-label for="file" :value="__('CSV File')" />
                                <x-text-input id="file" name="file" type="file" class="mt-1 block w-full" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('file')" />
                            </div>

                            <input type="hidden" name="isWebUI" value="1">

                            <div class="flex items-center gap-4">
                                <x-primary-button>Upload CSV</x-primary-button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg p-6">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                            Process CSV file
                        </h2>
                        
                        <p>
                            Using the uploaded CSV file, the AI will make outbound calls to the phone numbers in the CSV file. 
                            The job will be queued and will be processed in the background.
                        </p>
                        
                        <form method="post" action="{{ route('ai.process') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('post')

                            <div>
                                <x-input-label for="minutes" :value="__('Minutes to process')" />
                                <x-text-input id="minutes" name="minutes" type="number" value="60" class="mt-1 block w-full" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('minutes')" />
                            </div>

                            <input type="hidden" name="isWebUI" value="1">

                            <div class="flex items-center gap-4">
                                <x-primary-button>Process CSV</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg mb-16 lg:mb-0 p-6">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        Analyze Transcripts Settings
                    </h2>
                    
                    <form method="post" action="{{ route('ai.analyze-transcripts-settings') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('post')

                        <div>
                            <x-input-label for="agent_performance" :value="__('Agent Performance')" />
                            <x-text-area id="agent_performance" name="agent_performance" type="text" class="mt-1 block w-full" required autofocus value="{{ $analyzeTranscriptSettings->agent_performance }}"></x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('agent_performance')" />
                        </div>

                        <div>
                            <x-input-label for="general" :value="__('General')" />
                            <x-text-area id="general" name="general" type="text" class="mt-1 block w-full" required autofocus value="{{ $analyzeTranscriptSettings->general }}"></x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('general')" />
                        </div>

                        <div>
                            <x-input-label for="sentiment" :value="__('Sentiment')" />
                            <x-text-area id="sentiment" name="sentiment" type="text" class="mt-1 block w-full" required autofocus value="{{ $analyzeTranscriptSettings->sentiment }}"></x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('sentiment')" />
                        </div>

                        <div>
                            <x-input-label for="summary" :value="__('Summary')" />
                            <x-text-area id="summary" name="summary" type="text" class="mt-1 block w-full" required autofocus value="{{ $analyzeTranscriptSettings->summary }}"></x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('summary')" />
                        </div>

                        <div>
                            <x-input-label for="keywords" :value="__('Keywords')" />
                            <x-text-area id="keywords" name="keywords" type="text" class="mt-1 block w-full" required autofocus value="{{ $analyzeTranscriptSettings->keywords }}"></x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('keywords')" />
                        </div>

                        <div>
                            <x-input-label for="action_items" :value="__('Action Items')" />
                            <x-text-area id="action_items" name="action_items" type="text" class="mt-1 block w-full" required autofocus value="{{ $analyzeTranscriptSettings->action_items }}"></x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('action_items')" />
                        </div>

                        <div>
                            <x-input-label for="agent_insights" :value="__('Agent Insights')" />
                            <x-text-area id="agent_insights" name="agent_insights" type="text" class="mt-1 block w-full" required autofocus value="{{ $analyzeTranscriptSettings->agent_insights }}"></x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('agent_insights')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Save Settings</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showVoicemailCheckbox = document.getElementById('showVoicemail');
            const conversationRows = document.querySelectorAll('tbody tr');
            
            function filterConversations() {
                const showVoicemail = showVoicemailCheckbox.checked;
                let visibleCount = 0;
                
                conversationRows.forEach(row => {
                    const messageCell = row.querySelector('td:nth-child(2)');
                    if (messageCell) {
                        const messageText = messageCell.textContent.toLowerCase();
                        const containsVoicemail = messageText.includes("didn't answer the call");
                        
                        if (containsVoicemail && !showVoicemail) {
                            row.style.display = 'none';
                        } else {
                            row.style.display = '';
                            visibleCount++;
                        }
                    }
                });
                
                // Update pagination info text
                const paginationInfo = document.querySelector('.text-sm.text-gray-500');
                if (paginationInfo && !showVoicemail) {
                    const originalText = paginationInfo.textContent;
                    if (!originalText.includes('(filtered)')) {
                        paginationInfo.textContent = `${originalText} (filtered - ${visibleCount} visible)`;
                    }
                } else if (paginationInfo && showVoicemail) {
                    // Reset to original text
                    const originalText = paginationInfo.textContent.replace(/ \(filtered[^)]*\)/, '');
                    paginationInfo.textContent = originalText;
                }
            }
            
            // Hide voicemail conversations by default
            filterConversations();
            
            // Add event listener for checkbox changes
            showVoicemailCheckbox.addEventListener('change', filterConversations);
        });
    </script>
</x-app-layout>
