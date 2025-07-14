@extends('app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Galerie d'images verticale -->
        <div class="col-lg-1 d-none d-lg-block">
            @if($item->images && count($item->images) > 0)
                <div class="d-flex flex-column align-items-center gap-2">
                    @foreach($item->images as $index => $image)
                        <img src="{{ Storage::url($image) }}" class="img-thumbnail alibaba-thumb {{ $index === 0 ? 'active' : '' }}" alt="Miniature {{ $index + 1 }}" data-index="{{ $index }}" style="width: 60px; height: 60px; object-fit: cover; cursor:pointer;">
                    @endforeach
                </div>
            @endif
        </div>
        <!-- Image principale -->
        <div class="col-lg-5">
            <div class="card p-2 alibaba-main-img-card">
                @if($item->images && count($item->images) > 0)
                    <img id="mainProductImg" src="{{ Storage::url($item->images[0]) }}" class="w-100 alibaba-main-img" alt="{{ $item->name }}" style="height: 420px; object-fit: contain; border-radius: 18px;">
                @else
                    <div class="d-flex align-items-center justify-content-center" style="height: 420px; background: #f5f5f5; border-radius: 18px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                @endif
            </div>
        </div>
        <!-- Card produit -->
        <div class="col-lg-6">
            <div class="card p-4 shadow-lg alibaba-product-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h1 class="h3 mb-1 fw-bold">{{ $item->name }}</h1>
                        <div class="mb-2">
                            <span class="badge bg-primary me-2">{{ $item->category->name }}</span>
                            @if($item->brand)
                                <span class="badge bg-secondary me-2">{{ $item->brand->name }}</span>
                            @endif
                            <span class="badge condition-badge condition-{{ $item->condition }} ms-2">
                                {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                            </span>
                        </div>
                    </div>
                    @auth
                        <button class="btn btn-outline-danger favorite-btn" data-item-id="{{ $item->id }}">
                            <i class="fas fa-heart"></i>
                        </button>
                    @endauth
                </div>
                <div class="mb-3">
                    <span class="alibaba-price">{{ $item->formatted_price }}</span>
                    <span class="text-muted ms-2">@if($item->quantity > 0) En stock @else <span class="text-danger">Rupture</span> @endif</span>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <small class="text-muted">Vues</small>
                            <div class="fw-bold">{{ $item->views }}</div>
                        </div>
                        <div>
                            <small class="text-muted">Quantité</small>
                            <div class="fw-bold">{{ $item->quantity }}</div>
                        </div>
                        <div>
                            <small class="text-muted">Publié</small>
                            <div class="fw-bold">{{ $item->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <form method="POST" action="{{ route('cart.add', $item->id) }}" class="d-inline-block me-2">
                        @csrf
                        <div class="input-group" style="max-width: 140px;">
                            <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 60px;">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-cart-plus me-1"></i> Ajouter
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('cart.buy', $item->id) }}" class="btn btn-primary btn-lg ms-2">
                        <i class="fas fa-bolt me-1"></i> Acheter maintenant
                    </a>
                </div>
                <div class="mb-4">
                    <h5>Description</h5>
                    <p class="text-muted">{{ $item->description }}</p>
                </div>
                @if($item->specifications && is_array($item->specifications) && count($item->specifications) > 0)
                    <div class="mb-4">
                        <h5>Spécifications</h5>
                        <ul class="list-group list-group-flush">
                            @foreach($item->specifications as $key => $value)
                                <li class="list-group-item bg-transparent px-0 py-1 border-0">
                                    <strong>{{ is_string($key) ? ucfirst($key) : '' }}:</strong> 
                                    {{ is_string($value) ? $value : (is_array($value) ? json_encode($value) : '') }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="mb-4">
                    <h5>Vendeur</h5>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3">
                            <i class="fas fa-user fa-2x text-muted"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $item->user->name }}</div>
                            <small class="text-muted">Membre depuis {{ $item->user->created_at->format('M Y') }}</small>
                        </div>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    @auth
                        @if(Auth::id() !== $item->user_id)
                            <button class="btn btn-outline-primary btn-lg" onclick="contactSeller()">
                                <i class="fas fa-envelope me-2"></i>
                                Contacter le vendeur
                            </button>
                        @else
                            <div class="btn-group w-100">
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>
                                    Modifier
                                </a>
                                <button class="btn btn-danger" onclick="deleteItem()">
                                    <i class="fas fa-trash me-2"></i>
                                    Supprimer
                                </button>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se connecter pour acheter
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    <!-- Articles similaires -->
    @if($similarItems->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="fas fa-thumbs-up me-2"></i>
                    Articles similaires
                </h3>
                <div class="row g-4">
                    @foreach($similarItems as $similarItem)
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 item-card similar-item-card">
                                @if($similarItem->images && count($similarItem->images) > 0)
                                    <img src="{{ Storage::url($similarItem->images[0]) }}" 
                                         class="card-img-top" 
                                         alt="{{ $similarItem->name }}"
                                         style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 150px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">{{ Str::limit($similarItem->name, 40) }}</h6>
                                    <p class="text-primary fw-bold">{{ $similarItem->formatted_price }}</p>
                                    <a href="{{ route('items.show', $similarItem) }}" class="btn btn-sm btn-outline-primary">
                                        Voir détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>


@push('styles')
<style>
/* GLOBAL */
:root {
    --primary-color: #4f00ce;
    --secondary-color: #6c757d;
    --soft-bg: #f9f9fb;
    --radius: 18px;
    --transition: all 0.3s ease;
}

body {
    background-color: var(--soft-bg);
}

/* Image principale */
.alibaba-main-img-card {
    background: #fff;
    border-radius: var(--radius);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
    transition: var(--transition);
}

.alibaba-main-img {
    border-radius: var(--radius);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
    background: #fff;
    object-fit: contain;
}

/* Miniatures */
.alibaba-thumb {
    border: 2px solid #eee;
    border-radius: 12px;
    margin-bottom: 10px;
    transition: var(--transition);
    opacity: 0.8;
}
.alibaba-thumb:hover,
.alibaba-thumb.active {
    border-color: var(--primary-color);
    opacity: 1;
    transform: scale(1.05);
}

/* Carte produit */
.alibaba-product-card {
    border-radius: var(--radius);
    border: none;
    background: #fff;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    transition: var(--transition);
}

.alibaba-product-card:hover {
    transform: translateY(-3px);
}

/* Prix */
.alibaba-price {
    font-size: 2.4rem;
    font-weight: bold;
    color: var(--primary-color);
}

/* Badge condition */
.condition-badge {
    background-color: #e9ecef;
    color: #333;
    font-size: 0.8rem;
    padding: 4px 8px;
    border-radius: 8px;
}

/* Boutons */
.favorite-btn {
    border-radius: 12px;
    transition: var(--transition);
}
.favorite-btn:hover {
    background-color: #fde8ef;
    color: #dc3545;
}
.btn-outline-primary:hover {
    background-color: var(--primary-color);
    color: #fff;
}
.btn-success {
    border-radius: 12px;
}
.btn-primary.btn-lg {
    border-radius: 12px;
}
.btn-warning,
.btn-danger {
    border-radius: 12px;
}

/* Liste des spécifications */
.list-group-item {
    font-size: 0.95rem;
    color: #333;
}

/* Vendeur */
.avatar {
    width: 48px;
    height: 48px;
    background: #eee;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Articles similaires */
.similar-item-card {
    border: none;
    border-radius: var(--radius);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
    transition: var(--transition);
}
.similar-item-card:hover {
    transform: translateY(-5px);
}
.similar-item-card .card-body {
    padding: 1rem;
}

@media (max-width: 992px) {
    .alibaba-main-img {
        height: 260px !important;
    }
}

@media (max-width: 768px) {
    .alibaba-main-img {
        height: 180px !important;
    }
    .alibaba-product-card,
    .alibaba-main-img-card {
        margin-bottom: 1.5rem;
    }
}
</style>
@endpush

<script>
// Galerie d'images Alibaba : clic sur miniature
const thumbs = document.querySelectorAll('.alibaba-thumb');
if (thumbs.length > 0) {
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            thumbs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('mainProductImg').src = this.src;
        });
    });
}
function contactSeller() {
    window.location.href = `/messages?user={{ $item->user_id }}&item={{ $item->id }}`;
}
function deleteItem() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        fetch(`/items/{{ $item->id }}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/dashboard';
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}
// Gestion des favoris
const favoriteBtn = document.querySelector('.favorite-btn');
if (favoriteBtn) {
    favoriteBtn.addEventListener('click', function(e) {
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
}
</script>
@endsection 