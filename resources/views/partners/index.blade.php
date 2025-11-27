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
                <div class="mb-10">
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
                                <th class="py-2 px-2">Code</th>
                                <th class="py-2 px-2">Email</th>
                                <th class="py-2 px-2">Company</th>
                                <th class="py-2 px-2"></th>
                            </tr>
                        </thead>
                        <tbody id="partnerCodeTableBody">
                            <tr class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <td class="py-2 px-2">
                                    EGF.LGF
                                </td>
                                <td class="py-2 px-2">
                                    colbyg@insurely.ca
                                </td>
                                <td class="py-2 px-2">
                                    Insurely Inc.
                                </td>
                                <td class="py-2 px-2">
                                    <button data-code="EFG" type="button" class="copyButton inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="copyButtonText">Copy</span>
                                    </button>
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
    const table = document.getElementById('partnerCodeTableBody')
    const code = document.getElementById('code')
    const copyButtons = document.getElementsByClassName('copyButton')

    // From https://www.joshwcomeau.com/snippets/javascript/debounce/
    const debounce = (callback, wait) => {
        let timeoutId = null;
        return (...args) => {
            window.clearTimeout(timeoutId);
            timeoutId = window.setTimeout(() => {
            callback.apply(null, args);
            }, wait);
        };
    }

    const emptyTable = () => {
        table.innerHTML = ''
    }

    const populateTable = (data) => {

    }

    const handleFormSubmit = debounce(async (event) => {
        const response = await fetch(`http://localhost:8000/api/partners/find?key=${key}&searchCriteria=${event.target.value}`)
        const json = await response.json()

        const count = json['count']
        const data = json['data']

        // TODO: Enter the data into the table
        console.log(data)

        emptyTable()
    }, 250)

    code.addEventListener('input', handleFormSubmit)

    // Copy button functionality 
    const copyHandler = async function(){
        const code = this.getAttribute('data-code')

        await navigator.clipboard.writeText(code)
        this.querySelector('.copyButtonText').textContent = 'Copied!'
    }

    Array.from(copyButtons).forEach(function(element) {
      element.addEventListener('click', copyHandler);
    });
</script>

</x-app-layout>
