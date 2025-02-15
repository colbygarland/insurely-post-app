<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Confirm and Post To LinkedIn') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-8">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">Confirm Post and Wording</h2>
          <div class="max-w-2xl">
            <img src="{{ $post->thumbnail_url }}" alt="" class="mb-4" />
            <div>
              <textarea class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full min-h-60">{{$post->summary}}</textarea>
            </div>
            <a href="">Send to LinkedIn</a>
          </div>
          
        </div>
      </div>
  </div>
</x-app-layout>
