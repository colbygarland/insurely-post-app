<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Session::has('successMessage'))
                <div class="bg-green-200 text-green-900 inline-block rounded-lg py-2 px-4 mb-4">{{ Session::get('successMessage') }}</div>
            @endif
            @if(Session::has('errorMessage'))
                <div class="bg-red-200 text-red-900 inline-block rounded-lg py-2 px-4 mb-4">{{ Session::get('errorMessage') }}</div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg mb-16 p-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">Post to LinkedIn</h2>
                <p class="mb-2">By default, LinkedIn access tokens are valid for 60 days.</p>
                <div class="flex gap-4">
                    <a href="/get-token" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Authenticate with LinkedIn</a>
                    @if(auth()->user()->isLinkedinAuthenticated())
                        <a href="/posts" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Post to LinkedIn</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
