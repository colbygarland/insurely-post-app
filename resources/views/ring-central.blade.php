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
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                    Transcripts from Ring Central
                </h2>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Employee
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Customer Phone Number
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                               {{ Carbon\Carbon::parse("2025-07-16 10:00:00")->setTimezone('America/Edmonton')->format('F j, Y g:ia')}}
                            </th>
                            <td class="px-6 py-4">
                                John Doe
                            </td>
                            <td class="px-6 py-4">
                                (123) 456-7890
                            </td>
                        </tr>
                    </tbody>
                </table>
</div>
          
         
        </div>
    </div>
</x-app-layout>
