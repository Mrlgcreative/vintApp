@props([
    'title' => '',
    'icon' => null,
    'tone' => 'primary',
    'class' => '',
])

<div {{ $attributes->merge(['class' => "px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between gap-3 flex-wrap {$class}"]) }}>
    <div class="flex items-center gap-3">
        @if($icon)
            <x-icon :icon="$icon" :tone="$tone" />
        @endif
        <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
    </div>
    @isset($actions)
        <div class="flex items-center gap-2.5">{{ $actions }}</div>
    @endisset
</div>