<x-app-layout>
  <x-slot name="title">
    Post To LinkedIn
  </x-slot>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Post To LinkedIn') }}
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
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-8">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">Settings</h2>
          <div class="grid grid-cols-2 gap-8 dark:text-gray-200">
            <div>
                <p class="mb-2">Click this button if you want to manually sync this system with your WordPress posts. This process is also done daily automatically.</p>
                <a href="/wordpress/fetch_from_wordpress" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Sync System with WordPress</a>
            </div>
            <div>
                <p class="mb-2">Click this button if you want to mark all posts as published to LinkedIn. Useful if you don't want any posts going to LinkedIn.</p>
                <a href="/posts/manually-mark-published" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Set Posts Published</a>      
            </div>
            <div>
                <p class="mb-2">In order for the system to work, you must authenticate with LinkedIn first.</p>
                @if(auth()->user()->isLinkedinAuthenticated())
                    <div class="flex items-center gap-2 ml-2">
                        <div class="relative flex h-3 w-3 group inline-block">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 group-[.status-down]:bg-red-600 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-400 group-[.status-down]:bg-red-600"></span>
                        </div>
                        <p class="inline-block">You are successfully connected with LinkedIn.</p>
                    </div>
                @else 
                    <a href="/get-token" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Authenticate with LinkedIn</a>
                @endif
                </div>
          </div>
         </div>
        <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg p-6 mb-8">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">Posts Going to LinkedIn Next</h2>
            <p class="mb-2">There is a job set up that runs daily. This job first checks WordPress for any new posts, and then after syncing the posts, it will then send the new posts to LinkedIn. This is automatic and requires no input.</p>
            <p class="mb-2">However, if you wish, you can press the button below and manually run this process.</p>
            @if(count($postsToBeSent) == 0)
                <div class="bg-blue-100 text-blue-900 inline-block rounded-lg py-2 px-4 mb-4">There currently are no new posts from WordPress.</div>
            @else
                @if(auth()->user()->isLinkedinAuthenticated())
                    <a href="/posts/manually-post-to-linkedin" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Send Posts Manually to LinkedIn</a>
                @else 
                    <p>You must authenticate with LinkedIn first.</p>
                @endif
            @endif
            <div class="grid grid-cols-3 gap-8 mt-4">
                @foreach ($postsToBeSent as $post)
                    <div class="mb-4 relative shadow p-3 rounded-lg">
                        <img src="{{ $post->thumbnail_url }}" alt="" class="object-cover h-52 w-full" />
                        @if($post->published_at)
                            <div class="absolute top-3 left-3 bg-gray-800 text-white px-2 text-sm">Posted at: {{ \Carbon\Carbon::create($post->published_at)->toFormattedDateString() }}</div>
                        @endif
                        <p class="font-bold text-sm mt-1">Published to WP on {{ \Carbon\Carbon::create($post->date)->toFormattedDateString() }}</p>
                        <p class="mt-2 mb-4">
                            {{ $post->title }}
                        </p>
                        <p>
                          <a href="{{ $post->link }}" target="_blank" class="text-gray-900 inline-flex items-center px-4 py-2 bg-transparent border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700 hover:text-white focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">View Article</a>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 dark:text-gray-200 shadow-sm sm:rounded-lg p-6">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">Posts Already Sent To LinkedIn</h2>
            <div class="grid grid-cols-3 gap-8">
                @foreach ($postsAlreadySent as $post)
                    <div class="mb-4 relative shadow p-3 rounded-lg dark:bg-gray-600 dark:text-gray-200">
                        <img src="{{ $post->thumbnail_url }}" alt="" class="object-cover h-52 w-full" />
                        @if($post->published_at)
                            <div class="absolute top-3 left-3 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 px-2 text-sm">Posted at: {{ \Carbon\Carbon::create($post->published_at)->toFormattedDateString() }}</div>
                        @endif
                        <p class="font-bold text-sm mt-1">Published to WP on {{ \Carbon\Carbon::create($post->date)->toFormattedDateString() }}</p>
                        <p class="mt-2 mb-4">
                            {{ $post->title }}
                        </p>
                        <p>
                        <a href="{{ $post->link }}" target="_blank" class="text-gray-900 dark:text-gray-200 inline-flex items-center px-4 py-2 bg-transparent border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700 hover:text-white focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">View Article</a>
                        @if(auth()->user()->isLinkedinAuthenticated())
                            <a href="/posts/manually-post-to-single-to-linkedin/{{ $post->id }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Re-post</a>
                        @else 
                            <a href="/get-token" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Authenticate with LinkedIn</a>
                        @endif
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
      </div>
  </div>
</x-app-layout>
