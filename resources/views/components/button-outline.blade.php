@props([
    'type' => 'button',
    'size' => 'md',
    'tone' => 'default',
    'href' => null,
])

@php
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-sm',
    ];
    $tones = [
        'default' => 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white',
        'primary' => 'border-vinted-primary-200 bg-vinted-primary-50 text-vinted-primary-700 hover:bg-vinted-primary-100 dark:border-vinted-primary-500/30 dark:bg-vinted-primary-500/10 dark:text-vinted-primary-300 dark:hover:bg-vinted-primary-500/20',
        'info' => 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300 dark:hover:bg-blue-500/20',
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300 dark:hover:bg-amber-500/20',
        'danger' => 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20',
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    $toneClasses = $tones[$tone] ?? $tones['default'];
    $baseClasses = "inline-flex items-center justify-center rounded-md border font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1 disabled:pointer-events-none disabled:opacity-50 {$toneClasses} {$sizeClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $baseClasses]) }}
    >
        {{ $slot }}
    </button>
@endif