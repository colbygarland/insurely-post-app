<x-app-layout>
  <x-slot name="title">
    Partners
  </x-slot>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Partners') }}
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
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                    Search for a Partner Code
                </h2>
                <div class="mb-20">
                    <form method="post" class="">
                        @csrf
                        <div>
                            <x-input-label for="code" :value="__('Search')" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('code')" />
                        </div>
                    </form>
                </div>
                <div class="">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Code</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Company</th>
                                <th scope="col" class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <td class="">
                                    
                                </td>
                                <td class="">
                                    
                                </td>
                                <td class="">
                                    
                                </td>
                                <td class="">
                                Copy button here
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
      </div>
  </div>

<script>
    const key = '@php echo env('MICROSOFT_REQUEST_KEY'); @endphp'
    document.getElementById('code').addEventListener('input', async function(e){
        // TODO: debounce this
        const response = await fetch(`http://localhost:8000/api/partners/find?key=${key}&searchCriteria=${this.value}`)
        const json = await response.json()
        
        const count = json['count']
        const data = json['data']

        // TODO: Enter the data into the table
        console.log(data)
    })
</script>

</x-app-layout>
