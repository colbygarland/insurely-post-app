<section>
  <header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-200 ">
      {{ __('LinkedIn Tokens') }}
    </h2>
    </header>
    <div class="mt-4 mb-4">
      <x-input-label for="access_token" :value="__('Access Token')" />
      <x-text-input readonly id="access_token" name="access_token" type="text" value="{{ $user->linkedin_access_token }}" class="mt-1 block w-full" />  
    </div>
    <div class="mt-4 mb-4">
      <x-input-label for="refresh_token" :value="__('Refresh Token')" />
      <x-text-input readonly id="refresh_token" name="refresh_token" type="text" value="{{ $user->linkedin_refresh_token }}" class="mt-1 block w-full" />  
    </div>
    <div>
      <a href="/profile/revoke-linkedin-tokens" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Revoke tokens</a>
    </div>
  </section>