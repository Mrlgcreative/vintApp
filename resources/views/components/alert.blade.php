@props(['variant' => 'danger'])

@php
    $variants = [
        'danger' => 'bg-vinted-danger-50 text-vinted-danger-500 border-l-4 border-vinted-danger-500',
        'success' => 'bg-vinted-success-50 text-vinted-success-600 border-l-4 border-vinted-success-600',
        'warning' => 'bg-vinted-warning-50 text-vinted-warning-600 border-l-4 border-vinted-warning-600',
        'info' => 'bg-blue-50 text-blue-600 border-l-4 border-blue-600',
    ];
    $variantClasses = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => "border-0 rounded-xl font-medium p-4 {$variantClasses}"]) }}>
    {{ $slot }}
</div>
