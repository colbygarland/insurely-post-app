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

            <!-- 3/4 - 1/4 Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Column - Document Tables (3/4) -->
                <div class="lg:col-span-3 space-y-8">
                    @foreach(\App\Models\Document::$TYPE as $type)
                        @php
                            $typeDocuments = $documents->where('type', $type);
                        @endphp
                        
                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                                {{ ucwords(str_replace('_', ' ', $type)) }}
                            </h2>
                            
                            @if($typeDocuments->count() > 0)
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
                                            @foreach($typeDocuments as $document)
                                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    <a class="text-blue-500 hover:text-blue-700" href="{{ route('docs.view', $document->id) }}">{{ $document->name }}</a>
                                                </th>
                                                <td class="px-6 py-4">
                                                    {{ \Carbon\Carbon::parse($document->updated_at)->setTimezone('America/Edmonton')->format('F j, Y g:ia') }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $document->getUpdatedBy()->name }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="relative inline-block text-left">
                                                        <button type="button" id="button-{{ $document->id }}" onclick="toggleDropdown({{ $document->id }})" class="inline-flex justify-center items-center gap-x-1.5 rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                            Actions
                                                            <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div id="dropdown-{{ $document->id }}" class="hidden fixed z-50 w-32 rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                            <div class="py-1">
                                                                <a href="{{ route('docs.view', $document->id) }}" target="_blank" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">View</a>
                                                                <a href="{{ route('docs.download', $document->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Download</a>
                                                                <form action="{{ route('docs.delete', $document->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 text-sm italic">No documents in this category yet.</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Right Column - Upload Form (1/4) -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 sticky top-24">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">Upload New Document</h2>
                        
                        <form action="{{ route('docs.create') }}" method="POST" enctype="multipart/form-data">
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
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
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
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
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
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-200"
                                >
                                @error('document')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button 
                                    type="submit"
                                    class="w-full px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                >
                                    Upload Document
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
      </div>
  </div>

  <script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById('dropdown-' + id);
        const button = document.getElementById('button-' + id);
        
        // Close all other dropdowns first
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
            if (d.id !== 'dropdown-' + id) {
                d.classList.add('hidden');
            }
        });
        
        if (dropdown.classList.contains('hidden')) {
            // Position the dropdown relative to the button
            const rect = button.getBoundingClientRect();
            dropdown.style.top = (rect.bottom + 2) + 'px';
            dropdown.style.left = (rect.right - 128) + 'px'; // 128px = w-32
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
        dropdowns.forEach(dropdown => {
            const id = dropdown.id.replace('dropdown-', '');
            const button = document.getElementById('button-' + id);
            if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });
  </script>
</x-app-layout>
