@props([
    'count' => 8,
    'class' => '',
    'columns' => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4',
])

{{--
    Grille de cartes produit squelettes (style shadcn).
    Uso : <x-skeleton.product-grid :count="12" />
--}}
<div {{ $attributes->merge(['class' => 'grid gap-4 md:gap-6 ' . $columns . ' ' . $class]) }}>
    @for ($i = 0; $i < $count; $i++)
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-gray-700/60 dark:bg-gray-800">
            <div class="animate-pulse bg-gray-200 dark:bg-gray-700 aspect-[3/4] w-full"></div>
            <div class="space-y-3 p-5">
                <div class="animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 h-4 w-full"></div>
                <div class="animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 h-4 w-2/3"></div>
                <div class="flex items-center justify-between pt-2">
                    <div class="animate-pulse rounded-full bg-gray-200 dark:bg-gray-700 h-6 w-20"></div>
                    <div class="animate-pulse rounded-full bg-gray-200 dark:bg-gray-700 h-9 w-9"></div>
                </div>
            </div>
        </div>
    @endfor
</div>