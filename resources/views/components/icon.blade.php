@props([
    'icon' => '',
    'tone' => 'primary',
    'size' => 'sm',
])

@php
    $sizes = [
        'sm' => 'w-8 h-8 rounded-lg text-sm',
        'md' => 'w-10 h-10 rounded-xl text-base',
        'lg' => 'w-12 h-12 rounded-xl text-lg',
    ];
    $tones = [
        'primary' => 'bg-vinted-primary-100 text-vinted-primary-700 dark:bg-vinted-primary-500/20 dark:text-vinted-primary-300',
        'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300',
        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300',
        'yellow' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-300',
        'red' => 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300',
        'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300',
        'sky' => 'bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-300',
        'gray' => 'bg-gray-100 text-gray-600 dark:bg-gray-500/20 dark:text-gray-300',
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['sm'];
    $toneClasses = $tones[$tone] ?? $tones['primary'];
@endphp

<span {{ $attributes->merge(['class' => "$sizeClasses $toneClasses flex-shrink-0 flex items-center justify-center"]) }}>
    <i class="{{ $icon }}"></i>
</span>