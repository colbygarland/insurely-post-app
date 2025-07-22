<x-app-layout>
  <x-slot name="title">
    Settings
  </x-slot>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Settings') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Session::has('successMessage'))
                <x-alert type="success" :message="Session::get('successMessage')" />
            @endif
            @if(Session::has('errorMessage'))
                <x-alert type="error" :message="Session::get('errorMessage')" />
            @endif
          <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-16 p-6">
              <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">All Users</h2>
         
          </div>
      </div>
  </div>
</x-app-layout>
