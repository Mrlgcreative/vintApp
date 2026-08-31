@props(['class' => ''])

<select
    {{ $attributes->merge(['class' => "w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors {$class}"]) }}
>{{ $slot }}</select>