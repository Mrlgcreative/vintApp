@props([
    'id' => '',
    'title' => null,
    'icon' => null,
    'tone' => 'blue',
    'maxWidth' => 'lg',
])

@php
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
    ];
    $widthClasses = $widths[$maxWidth] ?? 'max-w-lg';
@endphp

<div
    id="{{ $id }}"
    {{ $attributes->merge(['class' => 'fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 hidden']) }}
>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700/50 {{ $widthClasses }}">
        @if($title || isset($header))
            <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 flex items-center justify-between px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-3">
                    @if($icon)
                        <x-icon :icon="$icon" :tone="$tone" />
                    @endif
                    {{ $title ?? $header }}
                </h5>
                <button
                    type="button"
                    onclick="window.closeModal('{{ $id }}')"
                    aria-label="Fermer"
                    class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-colors"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <div class="p-5 md:p-6">{{ $slot }}</div>

        @isset($footer)
            <div class="px-5 md:px-6 py-4 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between gap-3 rounded-b-xl">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>