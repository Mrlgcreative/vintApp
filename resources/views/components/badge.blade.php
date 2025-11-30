@props(['variant' => 'primary'])

@php
    $variants = [
        'primary' => 'bg-vinted-primary-600 text-white',
        'danger' => 'bg-vinted-danger-500 text-white',
        'success' => 'bg-vinted-success-600 text-white',
        'warning' => 'bg-vinted-warning-500 text-white',
    ];
    $variantClasses = $variants[$variant] ?? $variants['primary'];
@endphp

<span {{ $attributes->merge(['class' => "rounded-full font-medium px-3 py-1.5 text-sm inline-block {$variantClasses}"]) }}>
    {{ $slot }}
</span>
