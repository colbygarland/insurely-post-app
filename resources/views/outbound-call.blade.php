<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('AI Outbound Caller') }}
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
              <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">Make an Outbound Call</h2>
              <form method="post" action="{{ route('ai.send') }}" class="mt-6 space-y-6">
                @csrf
                @method('post')
        
                <div>
                    <x-input-label for="firstName" :value="__('First Name')" />
                    <x-text-input id="firstName" name="firstName" type="text" class="mt-1 block w-full" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('firstName')" />
                </div>

                <div>
                  <x-input-label for="lastName" :value="__('Last Name')" />
                  <x-text-input id="lastName" name="lastName" type="text" class="mt-1 block w-full" required autofocus />
                  <x-input-error class="mt-2" :messages="$errors->get('lastName')" />
                </div>

                <div>
                  <x-input-label for="number" :value="__('Phone Number')" />
                  <x-text-input id="number" name="number" type="text" class="mt-1 block w-full" required autofocus />
                  <x-input-error class="mt-2" :messages="$errors->get('number')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                  <x-input-label for="timezone" :value="__('Timezone')" />
                  <x-text-input id="timezone" name="timezone" type="text" class="mt-1 block w-full" value="America/Edmonton" required autofocus />
                  <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                </div>

                <input type="hidden" name="isWebUI" value="1">
        
                <div class="flex items-center gap-4">
                    <x-primary-button>Initiate Call</x-primary-button>
                </div>
            </form>
          </div>
      </div>
  </div>
</x-app-layout>
