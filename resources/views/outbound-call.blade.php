<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AI Outbound Caller') }}
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

            <div class="p-6 pl-0 flex gap-4">
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
                <div class="bg-white shadow-sm sm:rounded-lg mb-16 lg:mb-0 p-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
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
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-16">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
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

                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
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
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg mt-16 mb-16 lg:mb-0 p-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                    Conversations History
                </h2>

                <div class="mt-4 relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Message</th>
                                <th scope="col" class="px-6 py-3">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conversations as $conversation) 
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium underline text-blue-600 whitespace-nowrap dark:text-white"><a href="{{ route('ai.conversation.show', $conversation->id) }}">{{ $conversation->id }}</a></th>
                                    <td class="px-6 py-4">{!! Str::words($conversation->message, 10, '...')  !!}</td>
                                    <td class="px-6 py-4">{{ Carbon\Carbon::parse($conversation->created_at)->setTimezone('America/Edmonton')->format('F j, Y g:ia') }}</td>
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
        </div>
    </div>
</x-app-layout>
