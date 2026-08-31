@extends('app')

@section('title', 'Mes favoris - VintApp')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-8 md:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4">
                <button type="button"
                        onclick="history.back()"
                        aria-label="Retour"
                        class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <x-icon icon="fas fa-heart" tone="red" size="lg" />
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Mes favoris</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $items->total() }} article(s) dans vos favoris</p>
                </div>
            </div>
            <x-button-primary href="{{ route('items.index') }}">
                <i class="fas fa-arrow-left mr-2"></i>
                Explorer les articles
            </x-button-primary>
        </div>

        @if($items->count() > 0)
            <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach($items as $item)
                    @php
                        $firstImage = is_string($item->images) ? json_decode($item->images, true)[0] ?? null : ($item->images[0] ?? null);
                    @endphp
                    <div class="group relative flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-gray-700/50 dark:bg-gray-800">
                        <a href="{{ route('items.show', $item) }}" class="relative block aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700/50">
                            @if($firstImage)
                                <img src="{{ Storage::url($firstImage) }}"
                                     alt="{{ $item->name }}"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                     loading="lazy"
                                     onerror="this.parentElement.innerHTML = '<div class=\'flex items-center justify-center h-full\'><i class=\'fas fa-image text-gray-400 text-3xl\'></i></div>'">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <i class="fas fa-image text-gray-400 text-3xl"></i>
                                </div>
                            @endif
                        </a>

                        <button type="button"
                                class="favorite-btn absolute right-2.5 top-2.5 z-20 flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white/90 shadow-sm backdrop-blur-sm transition-colors hover:bg-red-50 dark:border-gray-600 dark:bg-gray-800/90 dark:hover:bg-gray-700"
                                data-item-id="{{ $item->id }}">
                            <i class="fas fa-heart text-sm text-red-500"></i>
                        </button>

                        <div class="flex flex-1 flex-col p-3 md:p-4">
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $item->category?->name ?? 'Vintage' }}</p>
                            <a href="{{ route('items.show', $item) }}" class="mt-1 line-clamp-1 text-sm font-medium text-gray-900 transition-colors hover:text-vinted-primary-600 dark:text-white dark:hover:text-vinted-primary-300">
                                {{ $item->name }}
                            </a>
                            <div class="mt-auto flex items-center justify-between gap-2 pt-3">
                                <span class="text-base font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($item->price, 0, ',', ' ') }} {{ $item->currency }}</span>
                                <div class="flex min-w-0 items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-store flex-shrink-0"></i>
                                    <span class="truncate">{{ $item->user?->name ?? 'Vendeur' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($items->hasPages())
                <div class="mt-10 flex justify-center">
                    <x-card class="p-4">
                        {{ $items->links() }}
                    </x-card>
                </div>
            @endif
        @else
            <!-- État vide -->
            <x-card class="mt-8 px-6 py-16">
                <div class="mx-auto max-w-md text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/20">
                        <i class="fas fa-heart text-2xl text-red-500 dark:text-red-400"></i>
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white">Aucun favori</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Vous n'avez pas encore ajouté d'articles à vos favoris.</p>
                    <x-button-primary class="mt-6" href="{{ route('items.index') }}">
                        <i class="fas fa-search mr-2"></i>
                        Découvrir des articles
                    </x-button-primary>
                </div>
            </x-card>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.favorite-btn').forEach(function(btn) {
        var pending = false;
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (pending) return;
            pending = true;
            var itemId = this.dataset.itemId;
            fetch('/api/items/' + itemId + '/favorite', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                pending = false;
                if (data.success) {
                    var card = btn.closest('.group');
                    card.style.transition = 'all 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(function() { card.remove(); }, 300);
                }
            })
            .catch(function() { pending = false; });
        });
    });
});
</script>
@endpush