@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'bg-vinted-primary-600 text-white',
        'secondary' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
        'outline' => 'border border-gray-300 text-gray-700 dark:border-gray-600 dark:text-gray-300',
        'info' => 'bg-blue-600 text-white',
        'success' => 'bg-vinted-success-600 text-white',
        'warning' => 'bg-vinted-warning-500 text-white',
        'danger' => 'bg-vinted-danger-500 text-white',
        'soft-primary' => 'bg-vinted-primary-50 text-vinted-primary-800 border border-vinted-primary-200',
        'soft-secondary' => 'bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-700/40 dark:text-gray-300 dark:border-gray-600',
        'soft-success' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
        'soft-warning' => 'bg-yellow-50 text-yellow-800 border border-yellow-200',
        'soft-info' => 'bg-blue-50 text-blue-800 border border-blue-200',
        'soft-danger' => 'bg-red-50 text-red-800 border border-red-200',
    ];
    $variantClasses = $variants[$variant] ?? $variants['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {$variantClasses}"]) }}>
    {{ $slot }}
</span>