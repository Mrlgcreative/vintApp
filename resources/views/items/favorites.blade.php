@extends('app')

@section('title', 'Mes favoris - VintApp')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 md:py-8">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 md:mb-8">
            <div class="flex items-center">
                <button onclick="history.back()" class="mr-4 p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                    <i class="fas fa-arrow-left text-lg"></i>
                </button>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-heart mr-3 text-red-500"></i>
                        Mes favoris
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $items->total() }} article(s) dans vos favoris</p>
                </div>
            </div>
            <a href="{{ route('items.index') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Explorer les articles
            </a>
        </div>

        @if($items->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5">
                @foreach($items as $item)
                    @php
                        $firstImage = is_string($item->images) ? json_decode($item->images, true)[0] ?? null : ($item->images[0] ?? null);
                    @endphp
                    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <a href="{{ route('items.show', $item) }}" class="block relative aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                            @if($item->images && count($item->images) > 0)
                                <img src="{{ Storage::url($item->images[0]) }}"
                                     alt="{{ $item->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy"
                                     onerror="this.parentElement.innerHTML = '<div class=\'flex items-center justify-center h-full\'><i class=\'fas fa-image text-gray-400 text-3xl\'></i></div>'">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <i class="fas fa-image text-gray-400 text-3xl"></i>
                                </div>
                            @endif
                            <button type="button"
                                    class="favorite-btn absolute top-2 right-2 z-20 w-8 h-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md hover:bg-white dark:hover:bg-gray-700 transition-all"
                                    data-item-id="{{ $item->id }}">
                                <i class="fas fa-heart text-red-500 text-sm"></i>
                            </button>
                        </a>
                        <div class="p-3 md:p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 truncate">{{ $item->category?->name ?? 'Vintage' }}</p>
                            <h3 class="font-medium text-gray-900 dark:text-white text-sm md:text-base line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors">
                                <a href="{{ route('items.show', $item) }}">{{ $item->name }}</a>
                            </h3>
                            <div class="flex items-center justify-between">
                                <span class="text-base md:text-lg font-bold text-primary-600 dark:text-primary-400">{{ number_format($item->price, 0, ',', ' ') }} {{ $item->currency }}</span>
                                <div class="flex items-center text-xs text-gray-400">
                                    <i class="fas fa-store mr-1"></i>
                                    <span class="truncate max-w-[80px]">{{ $item->user?->name ?? 'Vendeur' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-red-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aucun favori</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Vous n'avez pas encore ajouté d'articles à vos favoris.</p>
                <a href="{{ route('items.index') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg transition-all">
                    <i class="fas fa-search mr-2"></i>
                    Découvrir des articles
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.favorite-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
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
                if (data.success) {
                    var card = btn.closest('.group');
                    card.style.transition = 'all 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(function() { card.remove(); }, 300);
                }
            })
            .catch(function() {});
        });
    });
});
</script>
@endpush
