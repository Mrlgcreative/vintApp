@props([
    'icon' => null,
    'iconTone' => 'blue',
])

@php
    $tones = [
        'blue' => 'text-blue-500',
        'emerald' => 'text-emerald-500',
        'purple' => 'text-purple-500',
        'orange' => 'text-orange-500',
        'yellow' => 'text-yellow-500',
        'red' => 'text-red-500',
        'indigo' => 'text-indigo-500',
        'primary' => 'text-vinted-primary-600',
    ];
    $toneClasses = $tones[$iconTone] ?? $tones['blue'];
@endphp

<label {{ $attributes->merge(['class' => "block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5"]) }}>
    @if($icon)
        <i class="{{ $icon }} {{ $toneClasses }} mr-1"></i>
    @endif
    {{ $slot }}
</label>