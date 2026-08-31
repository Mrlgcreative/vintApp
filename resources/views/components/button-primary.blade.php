@props([
    'type' => 'button',
    'size' => 'md',
    'variant' => 'primary',
    'href' => null,
])

@php
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-sm',
    ];
    $variants = [
        'primary' => 'bg-vinted-primary-600 text-white hover:bg-vinted-primary-700 shadow-sm',
        'secondary' => 'bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600',
        'destructive' => 'bg-vinted-danger-500 text-white hover:bg-vinted-danger-600 shadow-sm',
        'success' => 'bg-vinted-success-600 text-white hover:bg-vinted-success-700 shadow-sm',
        'warning' => 'bg-vinted-warning-500 text-white hover:bg-vinted-warning-600 shadow-sm',
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    $variantClasses = $variants[$variant] ?? $variants['primary'];
    $baseClasses = "inline-flex items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1 disabled:pointer-events-none disabled:opacity-50 {$variantClasses} {$sizeClasses}";
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