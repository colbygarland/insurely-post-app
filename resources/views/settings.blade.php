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
              <div class="bg-green-200 text-green-900 inline-block rounded-lg py-2 px-4 mb-4">{{ Session::get('successMessage') }}</div>
          @endif
          @if(Session::has('errorMessage'))
              <div class="bg-red-200 text-red-900 inline-block rounded-lg py-2 px-4 mb-4">{{ Session::get('errorMessage') }}</div>
          @endif
          <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-16 p-6">
              <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">All Users</h2>
         
          </div>
      </div>
  </div>
</x-app-layout>
