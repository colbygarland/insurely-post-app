@props(['type', 'message'])
<?php
$classes = match ($type) {
    'success' => 'bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800',
    'error' => 'bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800',
    default => 'bg-gray-50 border border-gray-200 dark:bg-gray-900/20 dark:border-gray-800',
};
?>

<div class="relative {{ $classes }} rounded-lg p-4 mb-6" role="alert">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            @if($type === 'success')
                <svg class="w-5 h-5 text-green-400 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            @else
                <svg class="w-5 h-5 text-red-400 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            @endif
        </div>
        <div class="ml-3 flex-1">
            <h3 class="font-medium {{ $type === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
                {{ $type === 'success' ? 'Success' : 'Error' }}
            </h3>
            <p class="mt-1 {{ $type === 'success' ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                {{ $message }}
            </p>
        </div>
    </div>
</div>