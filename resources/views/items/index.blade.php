@extends('app')
@section('title', 'Articles disponibles')
@section('content')
<div class="fixed bottom-4 right-4 z-50">
    <div id="mainToast" class="hidden bg-gray-900 text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform translate-x-full transition-transform duration-300">
        <div id="mainToastBody" class="flex-1">Notification</div>
        <button type="button" onclick="hideToast()" class="text-white hover:text-gray-300 ml-4">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 md:py-8" data-page-type="product-grid">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        {{-- En-tete --}}
        <div class="mb-6 md:mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-box mr-3 text-gray-700 dark:text-gray-300"></i>
                    Articles disponibles
                </h1>
                <div class="flex items-center gap-2">
                    @if($userCity)
                        <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-full">
                            <i class="fas fa-location-dot mr-1.5"></i>
                            {{ $userCity }}
                        </span>
                    @endif
                    @auth
                        <a href="{{ route('items.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-900 text-white font-semibold rounded-xl shadow hover:shadow-lg active:scale-[0.98] transition-all">
                            <i class="fas fa-plus mr-2"></i>
                            Vendre un article
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Barre de recherche et filtres --}}
        <div class="mb-6 md:mb-8">
            <div class="max-w-3xl mx-auto">
                <div class="flex gap-2 sm:gap-4 p-1.5 sm:p-2 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('items.index') }}" class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="search"
                                   name="search"
                                   class="w-full pl-11 pr-4 sm:pr-28 py-3 sm:py-3.5 bg-gray-50 dark:bg-gray-900 border-2 border-transparent rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all"
                                   placeholder="Rechercher un article..."
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                            <button type="submit" class="hidden sm:flex absolute right-1.5 top-1/2 -translate-y-1/2 items-center px-5 py-2 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-lg transition-colors">
                                <i class="fas fa-search mr-2"></i>
                                Rechercher
                            </button>
                        </div>
                    </form>
                    <button type="button" onclick="toggleFiltersModal()" class="flex items-center px-4 sm:px-5 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-sliders-h sm:mr-2"></i>
                        <span class="hidden sm:inline">Filtres</span>
                    </button>
                </div>
            </div>

            {{-- Modal de filtrage --}}
            <div id="filtersModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
                <div class="flex items-end sm:items-center justify-center min-h-screen">
                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeFiltersModal()"></div>
                    <div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-2xl shadow-2xl max-h-[85vh] overflow-y-auto">
                        <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 rounded-t-2xl z-10">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                                    <i class="fas fa-filter mr-2 text-gray-700 dark:text-gray-300"></i>
                                    Filtres
                                </h3>
                                <button type="button" onclick="closeFiltersModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-5">
                            <form method="GET" action="{{ route('items.index') }}" id="filterForm" class="space-y-5">
                                <div>
                                    <label for="filterSearch" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-search mr-1.5 text-gray-500"></i>Mot-cle
                                    </label>
                                    <input type="text"
                                           id="filterSearch"
                                           name="search"
                                           class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all"
                                           placeholder="Ex: iPhone, Nike, Vetements..."
                                           value="{{ request('search') }}">
                                </div>

                                <div>
                                    <label for="filterCategory" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-layer-group mr-1.5 text-gray-500"></i>Categorie
                                    </label>
                                    <select id="filterCategory" name="category" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all">
                                        <option value="">Toutes les categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="filterBrand" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-tag mr-1.5 text-gray-500"></i>Marque
                                    </label>
                                    <select id="filterBrand" name="brand" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all">
                                        <option value="">Toutes les marques</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-dollar-sign mr-1.5 text-gray-500"></i>Prix (USD)
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="number"
                                               name="min_price"
                                               class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all"
                                               placeholder="Min"
                                               value="{{ request('min_price') }}"
                                               min="0"
                                               step="0.01">
                                        <input type="number"
                                               name="max_price"
                                               class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all"
                                               placeholder="Max"
                                               value="{{ request('max_price') }}"
                                               min="0"
                                               step="0.01">
                                    </div>
                                </div>

                                <div>
                                    <label for="filterCondition" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-star mr-1.5 text-gray-500"></i>Etat
                                    </label>
                                    <select id="filterCondition" name="condition" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all">
                                        <option value="">Tous les etats</option>
                                        <option value="new" {{ request('condition') == 'new' ? 'selected' : '' }}>Neuf</option>
                                        <option value="like_new" {{ request('condition') == 'like_new' ? 'selected' : '' }}>Comme neuf</option>
                                        <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Bon etat</option>
                                        <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>Etat correct</option>
                                        <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>Use</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="filterCity" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                        <i class="fas fa-city mr-1.5 text-gray-500"></i>Ville
                                    </label>
                                    <select id="filterCity" name="city" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all">
                                        <option value="">Toutes les villes</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city }}" {{ request('city', $userCity) == $city ? 'selected' : '' }}>
                                                {{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @if($userCity)
                                            <i class="fas fa-location-dot mr-1 text-gray-500"></i>Ville detectee : <strong>{{ $userCity }}</strong>
                                        @else
                                            <i class="fas fa-info-circle mr-1"></i>Aucune ville detectee
                                        @endif
                                    </p>
                                </div>
                            </form>
                        </div>

                        <div class="sticky bottom-0 bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                            <button type="button" onclick="resetFilters()" class="flex-1 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-undo mr-2"></i>Reinitialiser
                            </button>
                            <button type="button" onclick="applyFilters()" class="flex-1 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl shadow transition-colors">
                                <i class="fas fa-check mr-2"></i>Appliquer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grille d'articles --}}
        @if($items->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5" id="items-grid">
                @foreach($items as $loop_index => $item)
                    @php
                        $firstImage = is_string($item->images) ? json_decode($item->images, true)[0] ?? null : ($item->images[0] ?? null);
                        $isNew = $item->created_at->gt(now()->subDays(7));
                        $activeBoost = $item->activeBoosts->first();
                        $isBoosted = $activeBoost !== null;
                        $boostType = $activeBoost?->boostType;
                    @endphp
                    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        {{-- Image --}}
                        <a href="{{ route('items.show', $item) }}" class="block relative aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                            <x-skeleton class="absolute inset-0 h-full w-full" />
                            @if($item->images && count($item->images) > 0)
                                <img data-src="{{ Storage::url($item->images[0]) }}"
                                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23f3f4f6' width='400' height='300'/%3E%3C/svg%3E"
                                     loading="lazy"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     alt="{{ $item->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-3xl text-gray-300 dark:text-gray-500"></i>
                                </div>
                            @endif

                            {{-- Badges top-left --}}
                            <div class="absolute top-2 left-2 flex flex-col gap-1 z-20">
                                @if($isBoosted)
                                    <span class="px-2 py-0.5 bg-gray-900 text-white text-[10px] md:text-xs font-bold rounded-full shadow flex items-center gap-1">
                                        <i class="fas fa-star text-[9px]"></i>
                                        <span class="hidden sm:inline">{{ $boostType?->name ?? 'BOOST' }}</span>
                                    </span>
                                @endif
                                @if($isNew)
                                    <span class="px-2 py-0.5 bg-gray-900 text-white text-[10px] md:text-xs font-bold rounded-full shadow">
                                        NOUVEAU
                                    </span>
                                @endif
                            </div>

                            {{-- Favori top-right --}}
                            @auth
                                <button class="absolute top-2 right-2 z-20 w-8 h-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md hover:bg-white dark:hover:bg-gray-700 transition-all favorite-btn"
                                        data-item-id="{{ $item->id }}">
                                    <i class="fas fa-heart text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors text-xs"></i>
                                </button>
                            @endauth

                            {{-- Prix overlay bottom --}}
                            <div class="absolute bottom-2 right-2 z-20">
                                <span class="px-2.5 py-1 bg-gray-900/80 backdrop-blur-sm text-white rounded-lg text-xs md:text-sm font-bold shadow-lg">
                                    {{ $item->formatted_price }}
                                </span>
                            </div>
                        </a>

                        {{-- Contenu --}}
                        <div class="p-3 md:p-4">
                            <a href="{{ route('items.show', $item) }}" class="block group/title">
                                <h3 class="text-xs md:text-sm font-bold text-gray-900 dark:text-white line-clamp-2 group-hover/title:text-gray-600 dark:group-hover/title:text-gray-300 transition-colors leading-tight">
                                    {{ $item->name }}
                                </h3>
                            </a>

                            <div class="flex flex-wrap gap-1 mt-2">
                                <span class="px-1.5 md:px-2 py-0.5 text-[10px] md:text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">{{ $item->category->name }}</span>
                                @if($item->brand)
                                    <span class="px-1.5 md:px-2 py-0.5 text-[10px] md:text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">{{ $item->brand->name }}</span>
                                @endif
                            </div>

                            <p class="hidden md:block text-gray-500 dark:text-gray-400 text-xs mt-2 line-clamp-2">
                                {{ Str::limit($item->description, 70) }}
                            </p>

                            <div class="flex items-center justify-between mt-2.5 md:mt-3 pt-2.5 md:pt-3 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <i class="fas fa-user-circle text-gray-300 dark:text-gray-600 text-sm flex-shrink-0"></i>
                                    <span class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 truncate">{{ $item->user->name }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0 text-[10px] md:text-xs text-gray-400 dark:text-gray-500">
                                    <span class="flex items-center gap-0.5">
                                        <i class="fas fa-eye"></i>
                                        {{ $item->views }}
                                    </span>
                                    <span class="hidden sm:inline">{{ $item->created_at->diffForHumans(null, true) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 flex justify-center">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-search text-2xl text-gray-300 dark:text-gray-600"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">Aucun article trouve</h4>
                <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Essayez de modifier vos criteres de recherche.</p>
                @auth
                    <a href="{{ route('items.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-900 text-white font-semibold rounded-xl shadow hover:shadow-lg active:scale-[0.98] transition-all">
                        <i class="fas fa-plus mr-2"></i>
                        Vendre votre premier article
                    </a>
                @endauth
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function showNotification(message, type) {
    var toast = document.getElementById('mainToast');
    var body = document.getElementById('mainToastBody');
    if (!toast || !body) return;
    body.textContent = message;
    var base = 'text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform transition-transform duration-300';
    toast.className = (type === 'danger' ? 'bg-red-600' : 'bg-green-600') + ' ' + base;
    toast.classList.remove('hidden', 'translate-x-full');
    toast.classList.add('translate-x-0');
    setTimeout(hideToast, 4000);
}

function hideToast() {
    var toast = document.getElementById('mainToast');
    if (!toast) return;
    toast.classList.add('translate-x-full');
    setTimeout(function() { toast.classList.add('hidden'); }, 300);
}

function toggleFiltersModal() {
    var modal = document.getElementById('filtersModal');
    modal.classList.toggle('hidden');
    document.body.style.overflow = modal.classList.contains('hidden') ? '' : 'hidden';
}

function closeFiltersModal() {
    var modal = document.getElementById('filtersModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

function resetFilters() {
    var form = document.getElementById('filterForm');
    form.reset();
    form.querySelectorAll('input, select').forEach(function(f) {
        if (f.type === 'text' || f.type === 'search' || f.type === 'number') f.value = '';
        else if (f.tagName === 'SELECT') f.selectedIndex = 0;
    });
}

function applyFilters() {
    document.getElementById('filterForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.favorite-btn').forEach(function(btn) {
        var pending = false;
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (pending) return;
            pending = true;
            var itemId = this.dataset.itemId;
            var icon = this.querySelector('i');
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
                    icon.classList.toggle('text-gray-400', !data.is_favorite);
                    icon.classList.toggle('text-red-500', data.is_favorite);
                    showNotification(data.message, 'success');
                }
            })
            .catch(function() { pending = false; showNotification('Une erreur est survenue', 'danger'); });
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeFiltersModal();
    });
});
</script>
@endpush
