<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Post To LinkedIn') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-8">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">WordPress Settings</h2>
          <p class="mb-2">Click this button if you want to sync this system with your WordPress posts.</p>
          <a href="/wordpress/fetch_from_wordpress" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Sync System with WordPress</a>
          <p class="mt-4 mb-2">Click this button if you want to mark all posts as published to LinkedIn.</p>
          <a href="/posts/manually-mark-published" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Set Posts Published</a>
        </div>
        @if(Session::has('successMessage'))
            <div class="bg-green-100 text-green-900 inline-block rounded-lg py-2 px-4 mb-4">{{ Session::get('successMessage') }}</div>
        @endif
        @if(Session::has('errorMessage'))
            <div class="bg-red-100 text-red-900 inline-block rounded-lg py-2 px-4 mb-4">{{ Session::get('errorMessage') }}</div>
        @endif
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">Posts Going to LinkedIn Next</h2>
            <p class="mb-2">There is a job set up that runs daily. This job first checks WordPress for any new posts, and then after syncing the posts, it will then send the new posts to LinkedIn. This is automatic and requires no input.</p>
            <p class="mb-2">However, if you wish, you can press the button below and manually run this process.</p>
            @if(count($postsToBeSent) == 0)
                <div class="bg-blue-100 text-blue-900 inline-block rounded-lg py-2 px-4 mb-4">There currently are no new posts from WordPress.</div>
            @else
                <a href="/posts/manually-post-to-linkedin" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Send Posts Manually to LinkedIn</a>
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
          <div class="bg-white shadow-sm sm:rounded-lg p-6">
              <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">Posts Already Sent To LinkedIn</h2>
              <div class="grid grid-cols-3 gap-8">
                  @foreach ($postsAlreadySent as $post)
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
      </div>
  </div>
</x-app-layout>
