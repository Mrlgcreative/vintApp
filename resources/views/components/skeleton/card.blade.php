@props([
    'withMedia' => true,
    'headerLines' => 2,
    'class' => '',
])

{{--
    Carte squelette (style shadcn SkeletonCard).
    Uso : <x-skeleton.card />, le slot peut ajouter des lignes supplémentaires.
--}}
<div {{ $attributes->merge(['class' => 'w-full overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-gray-700/60 dark:bg-gray-800 ' . $class]) }}>
    @if($withMedia)
        <div class="animate-pulse bg-gray-200 dark:bg-gray-700 aspect-video w-full"></div>
    @endif
    <div class="grid gap-2.5 p-4">
        @for ($i = 0; $i < $headerLines; $i++)
            <div class="animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 h-4 {{ $i === 0 ? 'w-2/3' : 'w-1/2' }}"></div>
        @endfor
        @isset($slot)
            <div class="pt-1">{{ $slot }}</div>
        @endisset
    </div>
</div>