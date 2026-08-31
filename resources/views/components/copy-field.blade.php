@props([
    'target' => '',
    'class' => '',
    'mono' => false,
])

@php
    $textClasses = $mono
        ? 'font-mono font-semibold text-vinted-primary-600 dark:text-vinted-primary-300'
        : 'text-gray-600 dark:text-gray-300';
@endphp

<div class="flex">
    <input
        {{ $attributes->merge(['class' => "flex-1 px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-l-lg text-sm $textClasses {$class}"]) }}
        readonly
    >
    <button
        type="button"
        onclick="window.copyToClipboard('{{ $target }}')"
        class="px-4 py-2.5 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition-colors"
        aria-label="Copier"
    >
        <i class="fas fa-copy"></i>
    </button>
</div>