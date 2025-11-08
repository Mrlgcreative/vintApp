@extends('app')

@section('content')
<!-- Toast notification -->
<div class="fixed bottom-4 right-4 z-50">
    <div id="mainToast" class="hidden bg-purple-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform translate-x-full transition-transform duration-300">
        <div id="mainToastBody" class="flex-1">Notification</div>
        <button type="button" onclick="hideToast()" class="text-white hover:text-gray-200 ml-4">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-box mr-3 text-purple-600"></i>
                    Articles disponibles
                </h1>
                @auth
                    <a href="{{ route('items.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <i class="fas fa-plus mr-2"></i>
                        Vendre un article
                    </a>
                @endauth
            </div>
        </div>

        <!-- Barre de recherche et filtres -->
        <div class="mb-8">
            <div class="max-w-3xl mx-auto">
                <div class="flex gap-4 p-2 bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl border border-purple-100 hover:shadow-2xl transition-all duration-300">
                    <!-- Formulaire de recherche -->
                    <form method="GET" action="{{ route('items.index') }}" class="flex-1">
                        <div class="relative">
                            <input type="search" 
                                   name="search" 
                                   class="w-full pl-5 pr-32 py-4 bg-gray-50 border-2 border-transparent rounded-xl text-gray-900 placeholder-gray-500 font-medium focus:bg-white focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                                   placeholder="🔍 Rechercher un article..." 
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                            <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 px-6 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                                <i class="fas fa-search mr-2"></i>
                                <span class="hidden md:inline">Rechercher</span>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Bouton filtres -->
                    <button type="button" onclick="toggleFiltersModal()" class="flex items-center px-6 py-4 bg-gray-50 border-2 border-transparent text-purple-600 font-semibold rounded-xl hover:bg-white hover:border-purple-600 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <i class="fas fa-filter mr-2"></i>
                        <span class="hidden sm:inline">Filtres</span>
                    </button>
                </div>
            </div>

            <!-- Modal de filtrage -->
            <div id="filtersModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeFiltersModal()"></div>
                    
                    <div class="inline-block align-middle bg-white rounded-2xl shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                        <!-- En-tête du modal -->
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 rounded-t-2xl">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white flex items-center">
                                    <i class="fas fa-filter mr-3"></i>
                                    Filtres de recherche
                                </h3>
                                <button type="button" onclick="closeFiltersModal()" class="text-white hover:text-gray-200 transition-colors">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Contenu du modal -->
                        <div class="px-6 py-6 bg-gray-50">
                            <form method="GET" action="{{ route('items.index') }}" id="filterForm" class="space-y-6">
                                <!-- Recherche par mot-clé -->
                                <div>
                                    <label for="filterSearch" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-search mr-2 text-purple-600"></i>Mot-clé
                                    </label>
                                    <input type="text" 
                                           id="filterSearch" 
                                           name="search" 
                                           class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-900 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                                           placeholder="Ex: iPhone, Nike, Vêtements..." 
                                           value="{{ request('search') }}">
                                </div>

                                <!-- Catégorie -->
                                <div>
                                    <label for="filterCategory" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-layer-group mr-2 text-purple-600"></i>Catégorie
                                    </label>
                                    <select id="filterCategory" name="category" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-900 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-300">
                                        <option value="">Toutes les catégories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Marque -->
                                <div>
                                    <label for="filterBrand" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-tag mr-2 text-purple-600"></i>Marque
                                    </label>
                                    <select id="filterBrand" name="brand" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-900 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-300">
                                        <option value="">Toutes les marques</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Prix -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-dollar-sign mr-2 text-purple-600"></i>Prix (USD)
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="number" 
                                               name="min_price" 
                                               class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-900 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                                               placeholder="Min" 
                                               value="{{ request('min_price') }}" 
                                               min="0" 
                                               step="0.01">
                                        <input type="number" 
                                               name="max_price" 
                                               class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-900 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                                               placeholder="Max" 
                                               value="{{ request('max_price') }}" 
                                               min="0" 
                                               step="0.01">
                                    </div>
                                </div>

                                <!-- État -->
                                <div>
                                    <label for="filterCondition" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-star mr-2 text-purple-600"></i>État
                                    </label>
                                    <select id="filterCondition" name="condition" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-900 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-300">
                                        <option value="">Tous les états</option>
                                        <option value="new" {{ request('condition') == 'new' ? 'selected' : '' }}>🆕 Neuf</option>
                                        <option value="like_new" {{ request('condition') == 'like_new' ? 'selected' : '' }}>✨ Comme neuf</option>
                                        <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>👍 Bon état</option>
                                        <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>👌 État correct</option>
                                        <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>⚠️ Usé</option>
                                    </select>
                                </div>
                            </form>
                        </div>

                        <!-- Pied du modal -->
                        <div class="px-6 py-4 bg-white border-t border-gray-200 rounded-b-2xl flex justify-end space-x-3">
                            <button type="button" onclick="resetFilters()" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <i class="fas fa-undo mr-2"></i>Réinitialiser
                            </button>
                            <button type="button" onclick="applyFilters()" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300">
                                <i class="fas fa-check mr-2"></i>Appliquer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Résultats -->
        @if($items->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="items-grid">
                @foreach($items as $loop_index => $item)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 hover:scale-105 transition-all duration-300 border border-gray-100 overflow-hidden">
                        <!-- Image -->
                        <div class="relative">
                            <a href="{{ route('items.show', $item) }}" class="block">
                                @if($item->images && count($item->images) > 0)
                                    <img src="{{ Storage::url($item->images[0]) }}" 
                                         {{ $loop_index > 7 ? 'loading="lazy"' : '' }}
                                         class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300" 
                                         alt="{{ $item->name }}">
                                @else
                                    <div class="w-full h-48 bg-gray-100 flex items-center justify-content-center hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                    </div>
                                @endif
                            </a>
                            
                            <!-- Badge condition -->
                            <span class="absolute top-3 left-3 px-3 py-1 text-xs font-semibold bg-white/90 backdrop-blur-sm text-purple-600 rounded-lg shadow-md">
                                {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                            </span>

                            <!-- Bouton favori -->
                            @auth
                                <button class="absolute top-3 right-3 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md hover:bg-white hover:shadow-lg transition-all duration-200 favorite-btn"
                                        data-item-id="{{ $item->id }}">
                                    <i class="fas fa-heart text-gray-400 hover:text-red-500 transition-colors"></i>
                                </button>
                            @endauth
                        </div>

                        <!-- Contenu -->
                        <div class="p-5 flex flex-col h-full">
                            <a href="{{ route('items.show', $item) }}" class="block hover:text-purple-600 transition-colors">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 hover:text-purple-600">{{ Str::limit($item->name, 50) }}</h3>
                            </a>
                            
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-lg">{{ $item->category->name }}</span>
                                @if($item->brand)
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg">{{ $item->brand->name }}</span>
                                @endif
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-grow">
                                {{ Str::limit($item->description, 100) }}
                            </p>

                            <div class="mt-auto space-y-3">
                                <div class="flex justify-between items-center">
                                    <p class="text-xl font-bold text-purple-600">{{ $item->formatted_price }}</p>
                                    <span class="text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ $item->views }}
                                    </span>
                                </div>

                                <div class="text-xs text-gray-500 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center">
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $item->user->name }}
                                        </span>
                                        <span>{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('items.show', $item) }}" 
                                   class="block w-full text-center px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                    <i class="fas fa-eye mr-2"></i>
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <i class="fas fa-search text-6xl text-gray-300 mb-6"></i>
                <h4 class="text-2xl font-bold text-gray-600 mb-4">Aucun article trouvé</h4>
                <p class="text-gray-500 mb-8">Essayez de modifier vos critères de recherche.</p>
                @auth
                    <a href="{{ route('items.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <i class="fas fa-plus mr-2"></i>
                        Vendre votre premier article
                    </a>
                @endauth
            </div>
        @endif
    </div>
</div>

<script>
// Système de toast notifications
function showNotification(message, type = 'success') {
    const toast = document.getElementById('mainToast');
    const toastBody = document.getElementById('mainToastBody');
    
    if (!toast || !toastBody) return;
    
    // Configurer le message et le type
    toastBody.textContent = message;
    
    // Changer les couleurs selon le type
    toast.className = 'bg-purple-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform transition-transform duration-300';
    if (type === 'success') {
        toast.className = 'bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform transition-transform duration-300';
    } else if (type === 'danger') {
        toast.className = 'bg-red-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform transition-transform duration-300';
    }
    
    // Afficher le toast
    toast.classList.remove('hidden', 'translate-x-full');
    toast.classList.add('translate-x-0');
    
    // Masquer automatiquement après 5 secondes
    setTimeout(() => {
        hideToast();
    }, 5000);
}

function hideToast() {
    const toast = document.getElementById('mainToast');
    if (toast) {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 300);
    }
}

// Gestion du modal de filtres
function toggleFiltersModal() {
    const modal = document.getElementById('filtersModal');
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        closeFiltersModal();
    }
}

function closeFiltersModal() {
    const modal = document.getElementById('filtersModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Réinitialiser tous les filtres
function resetFilters() {
    const form = document.getElementById('filterForm');
    form.reset();
    form.querySelectorAll('input, select').forEach(field => {
        if (field.type === 'text' || field.type === 'search' || field.type === 'number') {
            field.value = '';
        } else if (field.tagName === 'SELECT') {
            field.selectedIndex = 0;
        }
    });
}

// Appliquer les filtres
function applyFilters() {
    document.getElementById('filterForm').submit();
}

// Gestion des favoris et autres événements
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des favoris
    const favoriteBtns = document.querySelectorAll('.favorite-btn');
    favoriteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.itemId;
            
            fetch(`/items/${itemId}/favorite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = this.querySelector('i');
                    if (data.is_favorite) {
                        icon.classList.remove('text-gray-400');
                        icon.classList.add('text-red-500');
                    } else {
                        icon.classList.remove('text-red-500');
                        icon.classList.add('text-gray-400');
                    }
                    
                    showNotification(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Une erreur est survenue', 'danger');
            });
        });
    });

    // Fermer le modal avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFiltersModal();
        }
    });

    // Responsive mobile - 3 colonnes en mode compact
    function adjustMobileLayout() {
        const itemsGrid = document.getElementById('items-grid');
        if (window.innerWidth <= 768 && itemsGrid) {
            itemsGrid.classList.remove('grid-cols-1', 'sm:grid-cols-2', 'lg:grid-cols-3', 'xl:grid-cols-4');
            itemsGrid.classList.add('grid-cols-3');
            itemsGrid.style.gap = '0.5rem';
            
            // Ajuster les cartes pour le mobile
            const cards = itemsGrid.querySelectorAll('.bg-white');
            cards.forEach(card => {
                // Réduire la hauteur de l'image
                const img = card.querySelector('img, .w-full.h-48');
                if (img) {
                    img.style.height = '60px';
                }
                
                // Réduire le padding
                const cardBody = card.querySelector('.p-5');
                if (cardBody) {
                    cardBody.classList.remove('p-5');
                    cardBody.classList.add('p-2');
                }
                
                // Réduire la taille du titre
                const title = card.querySelector('h3');
                if (title) {
                    title.classList.remove('text-lg');
                    title.classList.add('text-xs');
                    title.style.lineHeight = '1.1';
                }
                
                // Masquer la description
                const description = card.querySelector('.text-gray-600');
                if (description) {
                    description.style.display = 'none';
                }
                
                // Réduire les badges
                const badges = card.querySelectorAll('.px-2.py-1');
                badges.forEach(badge => {
                    badge.classList.remove('px-2', 'py-1');
                    badge.classList.add('px-1', 'py-0.5', 'text-xs');
                    badge.style.fontSize = '0.5rem';
                });
                
                // Réduire le prix
                const price = card.querySelector('.text-xl');
                if (price) {
                    price.classList.remove('text-xl');
                    price.classList.add('text-xs');
                }
                
                // Améliorer le bouton "Voir détails" pour mobile
                const button = card.querySelector('.block.w-full');
                if (button) {
                    button.classList.remove('px-4', 'py-3');
                    button.classList.add('px-3', 'py-2', 'text-sm', 'font-semibold');
                    button.style.marginTop = '0.5rem';
                    // Garder l'icône mais la rendre plus petite
                    const buttonIcon = button.querySelector('i');
                    if (buttonIcon) {
                        buttonIcon.classList.add('text-xs');
                    }
                    // Changer le texte pour qu'il soit plus court
                    const buttonText = button.textContent.trim();
                    if (buttonText.includes('Voir détails')) {
                        button.innerHTML = '<i class="fas fa-eye mr-1 text-xs"></i>Détails';
                    }
                }
                
                // Réduire les badges de condition
                const conditionBadge = card.querySelector('.absolute.top-3');
                if (conditionBadge) {
                    conditionBadge.classList.remove('top-3', 'left-3', 'px-3', 'py-1');
                    conditionBadge.classList.add('top-1', 'left-1', 'px-1', 'py-0.5', 'text-xs');
                    conditionBadge.style.fontSize = '0.5rem';
                }
                
                // Réduire le bouton favori
                const favoriteBtn = card.querySelector('.w-10.h-10');
                if (favoriteBtn) {
                    favoriteBtn.classList.remove('w-10', 'h-10', 'top-3', 'right-3');
                    favoriteBtn.classList.add('w-6', 'h-6', 'top-1', 'right-1');
                }
                
                // Masquer les détails supplémentaires
                const details = card.querySelector('.text-xs.text-gray-500');
                if (details) {
                    details.style.fontSize = '0.5rem';
                }
            });
        }
    }

    // Appliquer le layout mobile au chargement
    adjustMobileLayout();
    
    // Réappliquer lors du redimensionnement
    window.addEventListener('resize', adjustMobileLayout);
});
</script>
@endsection