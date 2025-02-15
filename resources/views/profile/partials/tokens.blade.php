<section>
  <header>
    <h2 class="text-lg font-medium text-gray-900">
      {{ __('LinkedIn Tokens') }}
    </h2>
    </header>
    <div class="mt-4 mb-4">
      <x-input-label for="access_token" :value="__('Access Token')" />
      <x-text-input readonly id="access_token" name="access_token" type="text" value="{{ $user->linkedin_access_token }}" class="mt-1 block w-full" />  
    </div>
    <div>
      <x-input-label for="refresh_token" :value="__('Refresh Token')" />
      <x-text-input readonly id="refresh_token" name="refresh_token" type="text" value="{{ $user->linkedin_refresh_token }}" class="mt-1 block w-full" />  
    </div>
  </section>