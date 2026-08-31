@props(['class' => ''])

<div {{ $attributes->merge(['class' => "bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/50 shadow-sm text-gray-900 dark:text-gray-100 {$class}"]) }}>
    {{ $slot }}
</div>