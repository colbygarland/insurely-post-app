<x-app-layout>
    <x-slot name="title">
        Profile
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(Session::has('successMessage'))
                <div class="bg-green-100 text-green-900 inline-block rounded-lg py-2 px-4 mb-4">
                    <h2 class="text-xl font-bold mb-2">Success!</h2>
                    {{ Session::get('successMessage') }}
                </div>
            @endif
            @if(Session::has('errorMessage'))
                <div class="bg-red-100 text-red-900 inline-block rounded-lg py-2 px-4 mb-4">
                    <h2 class="text-xl font-bold mb-2">Uh oh, something went wrong.</h2>
                    {{ Session::get('errorMessage') }}
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 dark:text-gray-200 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 dark:text-gray-200 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 dark:text-gray-200 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.tokens')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 dark:text-gray-200 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
