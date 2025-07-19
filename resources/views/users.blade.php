<x-app-layout>
  <x-slot name="title">
    Users
  </x-slot>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Users') }}
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
          <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg mb-16 p-6">
              <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">All Users</h2>
              <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Role
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Verified
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($users as $user)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $user->name }}
                            </th>
                            <td class="px-6 py-4">
                              {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                              {{ $user->role }}
                            </td>
                            <td class="px-6 py-4">
                              @if($user->verified_at)
                                <p>Verified at {{ Carbon\Carbon::parse($user->verified_at)->setTimezone('America/Edmonton')->format('M j, g:ia') }}</p>
                              @else  
                                <a href="/users/verify-user/{{ $user->id }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Verify User</a>
                              @endif
                            </td>
                            <td class="px-6 py-4">
                              @if($user->role !== 'admin')
                                <a href="{{ route('users.promote-admin', $user->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                   onclick="return confirm('Are you sure you want to promote this user to admin?')">
                                   Promote to Admin
                                </a>
                              @else
                                <span class="text-green-600 dark:text-green-400 font-semibold text-xs uppercase">Admin</span>
                              @endif
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
