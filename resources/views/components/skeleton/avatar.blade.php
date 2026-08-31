@props([
    'size' => 'w-10 h-10',
    'lines' => true,
    'class' => '',
])

{{--
    Avatar + lignes de texte squelettes (style shadcn SkeletonAvatar).
    Uso : <x-skeleton.avatar :size="'w-12 h-12'" />
--}}
<div {{ $attributes->merge(['class' => 'flex items-center gap-4 ' . $class]) }}>
    <div class="animate-pulse rounded-full bg-gray-200 dark:bg-gray-700 shrink-0 {{ $size }}"></div>
    @if($lines)
        <div class="grid gap-2">
            <div class="animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 h-4 w-36"></div>
            <div class="animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 h-4 w-24"></div>
        </div>
    @endif
</div>