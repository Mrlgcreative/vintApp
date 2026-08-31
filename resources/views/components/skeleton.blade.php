@props(['class' => ''])

{{--
    Composant Skeleton réutilisable (style shadcn/ui).
    Uso : <x-skeleton class="h-4 w-full" />
--}}
<div {{ $attributes->merge(['class' => 'animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 ' . $class]) }}></div>