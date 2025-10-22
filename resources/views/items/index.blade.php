@push('styles')
<style>
/* ===== BARRE DE RECHERCHE & FILTRES - UI MODERNE ===== */

/* Container principal */
.search-wrapper-home {
    max-width: 720px;
    margin: 0 auto;
    padding: 0.5rem;
    display: flex;
    gap: 0.875rem;
    align-items: stretch;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 
                0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(106, 13, 173, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.search-wrapper-home:hover {
    box-shadow: 0 6px 28px rgba(106, 13, 173, 0.12), 
                0 2px 6px rgba(0, 0, 0, 0.06);
    border-color: rgba(106, 13, 173, 0.15);
}

/* Formulaire de recherche */
.search-form-home {
    flex: 1;
    position: relative;
}

/* Champ de recherche */
.search-input-home {
    width: 100%;
    padding: 0.875rem 130px 0.875rem 1.25rem;
    border-radius: 12px;
    border: 2px solid transparent;
    background: #f8f9fa;
    font-size: 0.9375rem;
    font-weight: 500;
    color: #1f2937;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.01em;
}

.search-input-home::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.search-input-home:hover {
    background: #ffffff;
    border-color: rgba(106, 13, 173, 0.1);
}

.search-input-home:focus {
    background: #ffffff;
    border-color: #6A0DAD;
    box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.08);
    outline: none;
}

/* Bouton de recherche */
.search-btn-home {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    padding: 0.625rem 1.375rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.02em;
    background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
    border: none;
    color: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(106, 13, 173, 0.25);
}

.search-btn-home:hover {
    background: linear-gradient(135deg, #5a0b92 0%, #7209a8 100%);
    transform: translateY(-50%) translateY(-1px);
    box-shadow: 0 4px 14px rgba(106, 13, 173, 0.35);
}

.search-btn-home:active {
    transform: translateY(-50%) translateY(0);
    box-shadow: 0 2px 6px rgba(106, 13, 173, 0.3);
}

/* Bouton de filtre */
.filter-btn-home {
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    white-space: nowrap;
    letter-spacing: 0.02em;
    background: #f8f9fa;
    border: 2px solid transparent;
    color: #6A0DAD;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
}

.filter-btn-home::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(106, 13, 173, 0.1), rgba(139, 13, 199, 0.1));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.filter-btn-home:hover {
    background: #ffffff;
    border-color: #6A0DAD;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(106, 13, 173, 0.15);
}

.filter-btn-home:hover::before {
    opacity: 1;
}

.filter-btn-home:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(106, 13, 173, 0.1);
}

.filter-btn-home i {
    font-size: 1rem;
    position: relative;
    z-index: 1;
}

.filter-btn-home span {
    position: relative;
    z-index: 1;
}

/* Classes utilitaires */
.btn-purple {
    background-color: #6A0DAD;
    border-color: #6A0DAD;
    color: white;
}

.btn-purple:hover {
    background-color: #5a0b92;
    border-color: #5a0b92;
    color: white;
}

.btn-outline-purple {
    background: transparent;
    border-color: #6A0DAD;
    color: #6A0DAD;
}

.btn-outline-purple:hover {
    background: #6A0DAD;
    border-color: #6A0DAD;
    color: white;
}

/* Modal de filtres */
.modal.fade .modal-dialog {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal.show .modal-dialog {
    transform: none;
}

#filtersModal .modal-content {
    border-radius: 20px;
    border: 1px solid rgba(106, 13, 173, 0.1);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 
                0 4px 16px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

#filtersModal .modal-header {
    background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
    color: white;
    padding: 1.5rem 1.75rem;
    border-bottom: none;
}

#filtersModal .modal-title {
    font-weight: 700;
    font-size: 1.25rem;
    letter-spacing: 0.02em;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

#filtersModal .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

#filtersModal .btn-close:hover {
    opacity: 1;
}

#filtersModal .modal-body {
    padding: 2rem 1.75rem;
    background: #fafbfc;
}

#filtersModal label {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #1f2937;
    margin-bottom: 0.625rem;
    letter-spacing: 0.01em;
    display: block;
}

#filtersModal .form-control,
#filtersModal .form-select {
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
}

#filtersModal .form-control:focus,
#filtersModal .form-select:focus {
    border-color: #6A0DAD;
    box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.08);
    outline: none;
}

#filtersModal .modal-footer {
    padding: 1.5rem 1.75rem;
    background: white;
    border-top: 1px solid #e5e7eb;
}

/* Responsive - Tablette */
@media (max-width: 991.98px) {
    .search-wrapper-home {
        max-width: 100%;
        margin: 0 1rem;
    }
}

/* Responsive - Mobile */
@media (max-width: 767.98px) {
    .search-wrapper-home {
        padding: 0.4rem;
        gap: 0.5rem;
        margin: 0 0.75rem;
        border-radius: 14px;
    }
    
    .search-input-home {
        padding: 0.75rem 70px 0.75rem 1rem;
        font-size: 0.875rem;
        border-radius: 10px;
    }
    
    .search-btn-home {
        padding: 0.5rem 0.875rem;
        right: 5px;
        font-size: 0.8125rem;
        border-radius: 8px;
    }
    
    .filter-btn-home {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        border-radius: 10px;
        min-width: 48px;
        justify-content: center;
    }
    
    .filter-btn-home i {
        font-size: 0.9375rem;
    }
    
    #filtersModal .modal-dialog {
        margin: 0.5rem;
    }
    
    #filtersModal .modal-content {
        border-radius: 16px;
    }
    
    #filtersModal .modal-header,
    #filtersModal .modal-body,
    #filtersModal .modal-footer {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
    }
    
    #filtersModal .modal-title {
        font-size: 1.125rem;
    }
    
    #filtersModal .modal-footer .btn {
        min-width: 100px;
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
    }
}

/* Responsive - Très petit mobile */
@media (max-width: 374.98px) {
    .search-wrapper-home {
        gap: 0.375rem;
        padding: 0.35rem;
    }
    
    .search-input-home {
        padding: 0.625rem 65px 0.625rem 0.875rem;
        font-size: 0.8125rem;
    }
    
    .search-btn-home {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
    }
    
    .filter-btn-home {
        padding: 0.625rem 0.875rem;
        min-width: 44px;
    }
}

/* ========================================
   CARTES D'ARTICLES
   ======================================== */

@media (max-width: 768px) {
    .row.g-4 > [class^="col-"],
    .row.g-4 > [class*=" col-"] {
        flex: 0 0 33.3333%;
        max-width: 33.3333%;
        padding-left: 3px;
        padding-right: 3px;
        margin-bottom: 8px;
    }
    .item-card {
        margin-bottom: 1rem;
        min-width: 0;
        max-width: 100%;
        height: 120px;
        padding: 0.25rem 0.25rem 0.1rem 0.25rem;
        border-radius: 8px;
    }
    .card-img-top, .item-card img {
        height: 50px !important;
        border-radius: 6px 6px 0 0;
    }
    .card-title {
        font-size: 9px;
        line-height: 1.1;
        margin-bottom: 0.1rem;
    }
    .card-text {
        font-size: 8px;
        line-height: 1.1;
        display: none;
    }
    .card-body {
        padding: 0.3rem;
    }
    .badge {
        font-size: 6px;
        padding: 0.08em 0.3em;
    }
    .condition-badge {
        font-size: 6px;
        padding: 0.15em 0.4em;
        border-radius: 6px;
    }
    .favorite-btn {
        width: 20px;
        height: 20px;
        padding: 0;
        font-size: 9px;
        margin: 2px;
    }
    .btn-primary.w-100 {
        font-size: 8px;
        padding: 0.2rem 0.25rem;
        margin-top: 0.2rem !important;
    }
    .btn-primary.w-100 i {
        display: none;
    }
    .text-primary.fw-bold {
        font-size: 9px;
    }
    .text-muted small, small.text-muted {
        font-size: 7px;
    }
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1px;
    }
    .d-flex.justify-content-between.align-items-center.mb-2 {
        margin-bottom: 0.15rem !important;
    }
}
</style>
@endpush
<!-- Toast notification Bootstrap -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11000">
    <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastBody">
                Notification
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>
function showNotification(message, type = 'primary') {
        var toastEl = document.getElementById('mainToast');
        var toastBody = document.getElementById('mainToastBody');
        if (!toastEl || !toastBody) return;
        toastBody.textContent = message;
        toastEl.className = 'toast align-items-center text-bg-' + type + ' border-0';
        var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        toast.show();
}
</script>
@extends('app')

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-box me-2"></i>
                    Articles disponibles
                </h1>
                @auth
                    <a href="{{ route('items.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Vendre un article
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <section class="px-0">
                <div class="search-wrapper-home">
                    <form method="GET" action="{{ route('items.index') }}" class="search-form-home">
                        <div class="position-relative">
                            <input type="search" 
                                   name="search" 
                                   class="form-control search-input-home" 
                                   placeholder="🔍 Rechercher un article..." 
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                            <button type="submit" class="btn btn-purple search-btn-home">
                                <i class="fas fa-search"></i>
                                <span class="d-none d-md-inline ms-2">Rechercher</span>
                            </button>
                        </div>
                    </form>
                    <button type="button" class="btn btn-outline-purple filter-btn-home" onclick="toggleFiltersModal()">
                        <i class="fas fa-filter"></i>
                        <span class="d-none d-sm-inline ms-2">Filtres</span>
                    </button>
                </div>
            </section>

            <!-- Modal de filtrage -->
            <div id="filtersModal" class="modal fade" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filtersModalLabel">
                                <i class="fas fa-filter me-2 text-purple-600"></i>Filtres de recherche
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="GET" action="{{ route('items.index') }}" id="filterForm">
                                <!-- Recherche par mot-clé -->
                                <div class="mb-3">
                                    <label for="filterSearch" class="form-label fw-semibold">
                                        <i class="fas fa-search me-1"></i>Mot-clé
                                    </label>
                                    <input type="text" class="form-control" id="filterSearch" name="search" 
                                           placeholder="Ex: iPhone, Nike, Vêtements..." 
                                           value="{{ request('search') }}">
                                </div>

                                <!-- Catégorie -->
                                <div class="mb-3">
                                    <label for="filterCategory" class="form-label fw-semibold">
                                        <i class="fas fa-layer-group me-1"></i>Catégorie
                                    </label>
                                    <select class="form-select" id="filterCategory" name="category">
                                        <option value="">Toutes les catégories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Marque -->
                                <div class="mb-3">
                                    <label for="filterBrand" class="form-label fw-semibold">
                                        <i class="fas fa-tag me-1"></i>Marque
                                    </label>
                                    <select class="form-select" id="filterBrand" name="brand">
                                        <option value="">Toutes les marques</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Prix -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-dollar-sign me-1"></i>Prix (USD)
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="min_price" 
                                                   placeholder="Min" value="{{ request('min_price') }}" min="0" step="0.01">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="max_price" 
                                                   placeholder="Max" value="{{ request('max_price') }}" min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>

                                <!-- État -->
                                <div class="mb-3">
                                    <label for="filterCondition" class="form-label fw-semibold">
                                        <i class="fas fa-star me-1"></i>État
                                    </label>
                                    <select class="form-select" id="filterCondition" name="condition">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="fas fa-undo me-1"></i>Réinitialiser
                            </button>
                            <button type="button" class="btn btn-purple" onclick="applyFilters()">
                                <i class="fas fa-check me-1"></i>Appliquer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats -->
    <div class="row">
        <div class="col-12">
            @if($items->count() > 0)
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
                                            <i class="fas fa-image fa-3x text-muted"></i>
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
                                    <h5 class="card-title">{{ Str::limit($item->name, 50) }}</h5>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-primary">{{ $item->category->name }}</span>
                                        @if($item->brand)
                                            <span class="badge bg-secondary">{{ $item->brand->name }}</span>
                                        @endif
                                    </div>

                                    <p class="card-text text-muted">
                                        {{ Str::limit($item->description, 100) }}
                                    </p>

                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <p class="text-primary fw-bold">{{ $item->formatted_price }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-eye me-1"></i>
                                                {{ $item->views }} vues
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
                                           class="btn btn-primary w-100 mt-2">
                                            <i class="fas fa-eye me-2"></i>
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
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                    @auth
                        <a href="{{ route('items.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Vendre votre premier article
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>

<script>
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

// Ouvrir le modal de filtres
function toggleFiltersModal() {
    const modal = new bootstrap.Modal(document.getElementById('filtersModal'));
    modal.show();
}

// Réinitialiser tous les filtres
function resetFilters() {
    const form = document.getElementById('filterForm');
    form.reset();
    // Supprimer les valeurs des champs pour un reset complet
    form.querySelectorAll('input, select').forEach(field => {
        if (field.type === 'text' || field.type === 'search' || field.type === 'number') {
            field.value = '';
        } else if (field.tagName === 'SELECT') {
            field.selectedIndex = 0;
        }
    });
}

// Appliquer les filtres (soumettre le formulaire)
function applyFilters() {
    document.getElementById('filterForm').submit();
}
</script>
@endsection 

@push('styles')
<style>
.item-card {
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(79,0,206,0.08), 0 1.5px 4px rgba(0,0,0,0.04);
    transition: transform 0.15s, box-shadow 0.15s;
    border: none;
}
.item-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 8px 32px rgba(79,0,206,0.16), 0 2px 8px rgba(0,0,0,0.08);
    border: 1.5px solid #4f00ce22;
}
.card-img-top, .item-card img {
    border-radius: 18px 18px 0 0;
}
.condition-badge {
    border-radius: 12px;
    font-size: 0.85rem;
    padding: 0.4em 0.9em;
    background: #f5f5f5;
    color: #4f00ce;
    font-weight: 600;
    box-shadow: 0 1px 4px rgba(79,0,206,0.08);
}
.favorite-btn {
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(79,0,206,0.08);
    transition: background 0.2s, color 0.2s;
}
.favorite-btn i {
    color: #bbb;
    transition: color 0.2s;
}
.favorite-btn:hover i, .favorite-btn.active i, .favorite-btn i.text-danger {
    color: #e74c3c !important;
}
.card-title {
    font-weight: 700;
    color: #2d176a;
}
.card-text {
    font-size: 0.97rem;
}
.btn-primary, .btn-primary:focus {
    background: linear-gradient(90deg, #4f00ce 60%, #8f5cff 100%);
    border: none;
    font-weight: 600;
    letter-spacing: 0.02em;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #8f5cff 0%, #4f00ce 100%);
}
.badge.bg-primary {
    background: #4f00ce !important;
}
.badge.bg-secondary {
    background: #8f5cff !important;
}
@media (max-width: 768px) {
    .item-card {
        margin-bottom: 0.8rem;
    }
    .card-img-top, .item-card img {
        height: 50px !important;
    }
    .card-body {
        padding: 0.3rem;
    }
}
</style>
@endpush 