<x-app-layout>
  <x-slot name="title">
    Documentation
  </x-slot>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Documentation') }}
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
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">Upload New Document</h2>
                
                <form action="{{ route('docs.create') }}" method="POST" enctype="multipart/form-data" class="mt-6">
                    @csrf
                    
                    <!-- Document Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Document Name
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter document name"
                            value="{{ old('name') }}"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Document Type -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Document Type
                        </label>
                        <select 
                            id="type" 
                            name="type" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Select a type</option>
                            @foreach(\App\Models\Document::$TYPE as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label for="document" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Document File
                        </label>
                        <input 
                            type="file" 
                            id="document" 
                            name="document" 
                            required
                            accept=".pdf,.doc,.docx,.txt"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-200"
                        >
                        @error('document')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            Upload Document
                        </button>
                    </div>
                </form>
            </div>
          
          <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-16 p-6">
              <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">Wording Booklets</h2>
              
              <!-- Documents Table -->
              <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:text-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Last Updated</th>
                                <th scope="col" class="px-6 py-3">Updated By</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                            <tbody>
                                @foreach($documents as $document)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $document['name'] }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($document['updated_at'])->setTimezone('America/Edmonton')->format('F j, Y g:ia') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $document->getUpdatedBy()->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ asset('storage/documents/'.$document->file_name) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
          </div>
      </div>
  </div>
</x-app-layout>
