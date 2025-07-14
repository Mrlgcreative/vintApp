@extends('app')

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête de recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">
                        <i class="fas fa-search me-2"></i>
                        Résultats de recherche
                        @if($query)
                            pour "{{ $query }}"
                        @endif
                    </h4>
                    
                    <!-- Formulaire de recherche -->
                    <form method="GET" action="{{ route('items.search') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="q" 
                                       placeholder="Rechercher un article..."
                                       value="{{ $query }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select" name="category">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select" name="condition">
                                <option value="">Toutes les conditions</option>
                                <option value="new" {{ request('condition') == 'new' ? 'selected' : '' }}>Neuf</option>
                                <option value="like_new" {{ request('condition') == 'like_new' ? 'selected' : '' }}>Comme neuf</option>
                                <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Bon état</option>
                                <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>État correct</option>
                                <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>Usé</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="number" 
                                   class="form-control" 
                                   name="min_price" 
                                   placeholder="Prix min"
                                   value="{{ request('min_price') }}">
                        </div>

                        <div class="col-md-2">
                            <input type="number" 
                                   class="form-control" 
                                   name="max_price" 
                                   placeholder="Prix max"
                                   value="{{ request('max_price') }}">
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    Rechercher
                                </button>
                                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats -->
    <div class="row">
        <div class="col-12">
            @if($items->count() > 0)
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5>
                        <i class="fas fa-list me-2"></i>
                        {{ $items->total() }} article(s) trouvé(s)
                    </h5>
                    
                    <!-- Tri -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Trier par :</label>
                        <select class="form-select form-select-sm" id="sortSelect" style="width: auto;">
                            <option value="created_at-desc" {{ request('sort') == 'created_at' && request('order') == 'desc' ? 'selected' : '' }}>
                                Plus récents
                            </option>
                            <option value="created_at-asc" {{ request('sort') == 'created_at' && request('order') == 'asc' ? 'selected' : '' }}>
                                Plus anciens
                            </option>
                            <option value="price-asc" {{ request('sort') == 'price' && request('order') == 'asc' ? 'selected' : '' }}>
                                Prix croissant
                            </option>
                            <option value="price-desc" {{ request('sort') == 'price' && request('order') == 'desc' ? 'selected' : '' }}>
                                Prix décroissant
                            </option>
                            <option value="views-desc" {{ request('sort') == 'views' && request('order') == 'desc' ? 'selected' : '' }}>
                                Plus populaires
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($items as $item)
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card h-100 item-card">
                                <!-- Image -->
                                <div class="position-relative">
                                    @if($item->images && count($item->images) > 0)
                                        <img src="{{ Storage::url($item->images[0]) }}" 
                                             class="card-img-top" 
                                             alt="{{ $item->name }}"
                                             style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Badge condition -->
                                    <span class="position-absolute top-0 start-0 m-2 badge condition-badge condition-{{ $item->condition }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                                    </span>

                                    <!-- Bouton favori -->
                                    @auth
                                        <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 favorite-btn"
                                                data-item-id="{{ $item->id }}">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    @endauth
                                </div>

                                <!-- Contenu -->
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ Str::limit($item->name, 50) }}</h6>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-primary">{{ $item->category->name }}</span>
                                        @if($item->brand)
                                            <span class="badge bg-secondary">{{ $item->brand->name }}</span>
                                        @endif
                                    </div>

                                    <p class="card-text text-muted small">
                                        {{ Str::limit($item->description, 80) }}
                                    </p>

                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="h6 mb-0 text-primary fw-bold">{{ $item->formatted_price }}</span>
                                            <small class="text-muted">
                                                <i class="fas fa-eye me-1"></i>
                                                {{ $item->views }}
                                            </small>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $item->user->name }}
                                            </small>
                                            <small class="text-muted">
                                                {{ $item->created_at->diffForHumans() }}
                                            </small>
                                        </div>

                                        <a href="{{ route('items.show', $item) }}" 
                                           class="btn btn-primary btn-sm w-100 mt-2">
                                            <i class="fas fa-eye me-1"></i>
                                            Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $items->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun article trouvé</h4>
                    <p class="text-muted">
                        @if($query)
                            Aucun résultat pour "{{ $query }}" avec les critères sélectionnés.
                        @else
                            Essayez de modifier vos critères de recherche.
                        @endif
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('items.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>
                            Voir tous les articles
                        </a>
                        @auth
                            <a href="{{ route('items.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>
                                Vendre un article
                            </a>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du tri
    const sortSelect = document.getElementById('sortSelect');
    sortSelect.addEventListener('change', function() {
        const [sort, order] = this.value.split('-');
        const url = new URL(window.location);
        url.searchParams.set('sort', sort);
        url.searchParams.set('order', order);
        window.location.href = url.toString();
    });

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
                        icon.classList.add('text-danger');
                    } else {
                        icon.classList.remove('text-danger');
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
});

// Fonction pour afficher les notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>

<style>
.card {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.item-card {
    transition: all 0.3s ease;
}

.item-card:hover {
    transform: translateY(-4px);
}

/* Animation d'entrée */
.card {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Styles pour les badges */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Styles pour les boutons */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Styles pour les favoris */
.favorite-btn {
    transition: all 0.2s ease;
}

.favorite-btn:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.favorite-btn .text-danger {
    color: #dc3545 !important;
}

/* Styles responsives */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
    }
}
</style>
@endsection 