@props([
    'type' => 'button',
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2',
        'lg' => 'px-6 py-3 text-lg',
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white rounded-lg font-medium transition-all hover:-translate-y-0.5 hover:shadow-vinted-primary {$sizeClasses}"]) }}
>
    {{ $slot }}
</button>
