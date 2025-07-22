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
                <x-alert type="success" :message="Session::get('successMessage')" />
            @endif
            @if(Session::has('errorMessage'))
                <x-alert type="error" :message="Session::get('errorMessage')" />
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

            @if(Gate::allows('is-admin'))
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 dark:text-gray-200 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.tokens')
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 dark:text-gray-200 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
