@props([
    'lines' => 3,
    'lastClass' => 'w-3/4',
    'class' => '',
])

{{--
    Bloc de lignes de texte squelettes (style shadcn SkeletonText).
    Uso : <x-skeleton.text :lines="3" />
--}}
<div {{ $attributes->merge(['class' => 'grid gap-2 ' . $class]) }}>
    @for ($i = 0; $i < $lines; $i++)
        <div class="animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 h-4 {{ $i === $lines - 1 ? $lastClass : 'w-full' }}"></div>
    @endfor
</div>