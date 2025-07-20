@props(['disabled' => false, 'value' => ''])

<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-200']) }}>{{ old($attributes->get('name'), $value) }}</textarea>
