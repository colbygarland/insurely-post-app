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
                                Verified at
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
                                <p>{{ Carbon\Carbon::parse($user->verified_at)->setTimezone('America/Edmonton')->format('M j, g:ia') }}</p>
                              @else  
                                <a href="/users/verify-user/{{ $user->id }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Verify User</a>
                              @endif
                            </td>
                            <td class="px-6 py-4">
                              <div class="flex items-center gap-2">
                              @if($user->role !== 'admin')
                                <a href="{{ route('users.promote-admin', $user->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                   onclick="return confirm('Are you sure you want to promote this user to admin?')">
                                   Promote to Admin
                                </a>
                              @else
                                <span class="text-gray-600 dark:text-gray-400 font-semibold text-xs uppercase">n/a</span>
                              @endif
                              
                              <button type="button" 
                                      class="reset-password-btn inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                      data-user-id="{{ $user->id }}"
                                      data-user-name="{{ $user->name }}"
                                      data-user-email="{{ $user->email }}">
                                Reset Password
                              </button>
                              @if($user->role !== 'admin')
                              <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" title="Delete User" onclick="return confirm('Are you sure you want to delete this user?')" class="inline-flex items-center px-2 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-2">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16" />
                                      </svg>
                                  </button>
                              </form>
                              @endif
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

      // Add click event listeners to all reset password buttons
      document.querySelectorAll('.reset-password-btn').forEach(button => {
        button.addEventListener('click', function() {
          const userId = this.dataset.userId;
          const userName = this.dataset.userName;
          const userEmail = this.dataset.userEmail;
          
          // Show loading state
          this.disabled = true;
          this.textContent = 'Generating...';
          
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
            this.textContent = 'Reset Password';
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
