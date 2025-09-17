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
                <x-alert type="success" :message="Session::get('successMessage')" />
            @endif
            @if(Session::has('errorMessage'))
                <x-alert type="error" :message="Session::get('errorMessage')" />
            @endif
          <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg mb-16 p-6">
              <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">All Users</h2>
              <div class="relative">
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
                                Verified at
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Last active
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
                              <span class="@if($user->role === 'admin') text-green-600 dark:text-green-400 @else text-blue-600 dark:text-blue-400 @endif font-semibold text-xs uppercase">{{ $user->role }}</span>
                            </td>
                            <td class="px-6 py-4">
                              @if($user->verified_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Carbon\Carbon::parse($user->verified_at)->setTimezone('America/Edmonton')->format('M j, g:ia') }}</p>
                              @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                  Unverified
                                </span>
                              @endif
                            </td>
                            <td class="px-6 py-4">
                              @if($user->last_login_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Carbon\Carbon::parse($user->last_login_at)->setTimezone('America/Edmonton')->format('M j, g:ia') }}</p>
                              @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                  Never
                                </span>
                              @endif
                            </td>
                            <td class="px-6 py-4">
                              <div class="relative inline-block text-left">
                                <button type="button" 
                                        class="actions-dropdown-btn inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-role="{{ $user->role }}"
                                        data-user-verified="{{ $user->verified_at ? 'true' : 'false' }}">
                                  Actions
                                  <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                  </svg>
                                </button>
                                
                                <div class="dropdown-menu hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                  <div class="py-1">
                                    @if(!$user->verified_at)
                                    <a href="/users/verify-user/{{ $user->id }}" 
                                       class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                      <svg class="mr-3 h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                      </svg>
                                      Verify User
                                    </a>
                                    @endif
                                    
                                    <button type="button" 
                                            class="reset-password-btn group flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 text-left"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}">
                                      <svg class="mr-3 h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                      </svg>
                                      Reset Password
                                    </button>

                                    <a href="{{ route('users.login-as-user', $user->id) }}"
                                            class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 text-left"
                                            >
                                      <svg class="mr-3 h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                      </svg>
                                      Login as User
                                    </a>
                                    
                                    @if($user->role !== 'admin')
                                    <a href="{{ route('users.promote-admin', $user->id) }}" 
                                       class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                       onclick="return confirm('Are you sure you want to promote this user to admin?')">
                                      <svg class="mr-3 h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                      </svg>
                                      Promote to Admin
                                    </a>
                                    
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this user?')" 
                                                class="group flex items-center w-full px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900 text-left">
                                          <svg class="mr-3 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16" />
                                          </svg>
                                          Delete User
                                        </button>
                                    </form>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
              </div>
          </div>
      </div>
  </div>

  <!-- Password Reset Modal -->
  <div id="passwordResetModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div>
          <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full dark:bg-yellow-900">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
          </div>
          <div class="mt-3 text-center sm:mt-5">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
              Password Reset Link Generated
            </h3>
            <div class="mt-2">
              <p class="text-sm text-gray-500 dark:text-gray-400" id="modalUserInfo">
                Password reset link for <span id="modalUserName"></span> (<span id="modalUserEmail"></span>):
              </p>
            </div>
            <div class="mt-4">
              <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md">
                <textarea id="resetLinkTextarea" 
                          class="w-full h-24 text-xs text-gray-800 dark:text-gray-200 bg-transparent border-none resize-none focus:outline-none"
                          readonly></textarea>
              </div>
              <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Copy this link and send it to the user via your preferred method.
              </p>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
          <button type="button" 
                  id="copyLinkBtn"
                  class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
            Copy Link
          </button>
          <button type="button" 
                  id="closeModalBtn"
                  class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('passwordResetModal');
      const closeBtn = document.getElementById('closeModalBtn');
      const copyBtn = document.getElementById('copyLinkBtn');
      const resetLinkTextarea = document.getElementById('resetLinkTextarea');
      const modalUserName = document.getElementById('modalUserName');
      const modalUserEmail = document.getElementById('modalUserEmail');

      // Dropdown functionality
      document.querySelectorAll('.actions-dropdown-btn').forEach(button => {
        button.addEventListener('click', function(e) {
          e.stopPropagation();
          
          // Close all other dropdowns
          document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== this.nextElementSibling) {
              menu.classList.add('hidden');
            }
          });
          
          // Toggle current dropdown
          const dropdown = this.nextElementSibling;
          dropdown.classList.toggle('hidden');
        });
      });

      // Close dropdowns when clicking outside
      document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
          menu.classList.add('hidden');
        });
      });

      // Prevent dropdown from closing when clicking inside
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      });

      // Add click event listeners to all reset password buttons
      document.querySelectorAll('.reset-password-btn').forEach(button => {
        button.addEventListener('click', function() {
          const userId = this.dataset.userId;
          const userName = this.dataset.userName;
          const userEmail = this.dataset.userEmail;
          
          // Close dropdown
          const dropdown = this.closest('.dropdown-menu');
          if (dropdown) {
            dropdown.classList.add('hidden');
          }
          
          // Show loading state
          this.disabled = true;
          const originalText = this.innerHTML;
          this.innerHTML = '<svg class="mr-3 h-5 w-5 text-yellow-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generating...';
          
          // Make AJAX request to generate password reset link
          fetch(`/users/generate-password-reset/${userId}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Populate modal with data
              modalUserName.textContent = userName;
              modalUserEmail.textContent = userEmail;
              resetLinkTextarea.value = data.reset_url;
              
              // Show modal
              modal.classList.remove('hidden');
            } else {
              alert('Error generating password reset link: ' + (data.error || 'Unknown error'));
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error generating password reset link. Please try again.');
          })
          .finally(() => {
            // Reset button state
            this.disabled = false;
            this.innerHTML = originalText;
          });
        });
      });

      // Close modal functionality
      closeBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
      });

      // Close modal when clicking outside
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          modal.classList.add('hidden');
        }
      });

      // Copy link functionality
      copyBtn.addEventListener('click', function() {
        resetLinkTextarea.select();
        resetLinkTextarea.setSelectionRange(0, 99999); // For mobile devices
        
        try {
          document.execCommand('copy');
          
          // Show feedback
          const originalText = copyBtn.textContent;
          copyBtn.textContent = 'Copied!';
          copyBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
          copyBtn.classList.add('bg-green-600', 'hover:bg-green-700');
          
          setTimeout(() => {
            copyBtn.textContent = originalText;
            copyBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            copyBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
          }, 2000);
        } catch (err) {
          alert('Failed to copy link. Please copy manually.');
        }
      });
    });
  </script>
</x-app-layout>
