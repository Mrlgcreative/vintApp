@props([
    'value' => '0',
    'label' => '',
    'icon' => 'fas fa-circle',
    'tone' => 'blue',
    'id' => null,
])

@php
    $chips = [
        'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
        'sky' => 'bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
        'primary' => 'bg-vinted-primary-100 text-vinted-primary-700 dark:bg-vinted-primary-500/20 dark:text-vinted-primary-300',
    ];
    $values = [
        'blue' => 'text-blue-600 dark:text-blue-400',
        'emerald' => 'text-emerald-600 dark:text-emerald-400',
        'sky' => 'text-sky-600 dark:text-sky-400',
        'amber' => 'text-amber-600 dark:text-amber-400',
        'primary' => 'text-vinted-primary-700 dark:text-vinted-primary-300',
    ];
    $chipClasses = $chips[$tone] ?? $chips['blue'];
    $valueClasses = $values[$tone] ?? $values['blue'];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/50 shadow-sm p-5 text-center">
    <div class="w-10 h-10 mx-auto rounded-xl flex items-center justify-center mb-3 {{ $chipClasses }}">
        <i class="{{ $icon }}"></i>
    </div>
    <h4 class="text-2xl md:text-3xl font-bold tabular-nums {{ $valueClasses }}" @if($id) id="{{ $id }}" @endif>{{ $value }}</h4>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $label }}</p>
</div>