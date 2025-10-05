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
                <!-- Réductions disponibles -->
                @auth
                    @if(Auth::id() !== $item->user_id)
                        <div id="discountSection" class="mb-3" style="display: none;">
                            <div class="alert alert-success">
                                <h6 class="alert-heading">
                                    <i class="fas fa-tag me-2"></i>
                                    Réduction disponible !
                                </h6>
                                <div id="discountInfo"></div>
                                <button class="btn btn-sm btn-success mt-2" onclick="applyDiscount()">
                                    Appliquer la réduction
                                </button>
                            </div>
                        </div>
                    @endif
                @endauth

                <div class="d-grid gap-2">
                    @auth
                        @if(Auth::id() !== $item->user_id)
                            <!-- Bouton de contact avec demande de réduction -->
                            <form id="contactForm" method="POST" action="{{ route('contact.seller', $item) }}">
                                @csrf
                                <button type="button" class="btn btn-outline-primary btn-lg mb-2" data-bs-toggle="modal" data-bs-target="#contactModal">
                                    <i class="fas fa-percentage me-2"></i>
                                    Demander une réduction
                                </button>
                            </form>
                            
                            <!-- Bouton contact simple -->
                            <button class="btn btn-outline-secondary" onclick="contactSeller()">
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

<!-- Modal de demande de réduction -->
@auth
    @if(Auth::id() !== $item->user_id)
        <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="contactModalLabel">
                            <i class="fas fa-percentage me-2"></i>
                            Demander une réduction
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Aperçu du produit -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                @if($item->images && count($item->images) > 0)
                                    <img src="{{ Storage::url($item->images[0]) }}" 
                                         class="img-fluid rounded" 
                                         alt="{{ $item->name }}"
                                         style="height: 150px; object-fit: cover; width: 100%;">
                                @endif
                            </div>
                            <div class="col-md-8">
                                <h6 class="fw-bold">{{ $item->name }}</h6>
                                <p class="text-muted small">{{ Str::limit($item->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold fs-5">{{ $item->formatted_price }}</span>
                                    <span class="badge bg-info">{{ $item->category->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Message personnalisé -->
                        <div class="mb-3">
                            <label for="customMessage" class="form-label">
                                <i class="fas fa-edit me-2"></i>
                                Message personnalisé (optionnel)
                            </label>
                            <textarea name="custom_message" 
                                      id="customMessage" 
                                      class="form-control" 
                                      rows="4" 
                                      placeholder="Bonjour, je suis intéressé(e) par votre produit. Serait-il possible d'obtenir une réduction ?"></textarea>
                            <small class="text-muted">
                                Un message automatique sera envoyé si vous laissez ce champ vide.
                            </small>
                        </div>

                        <!-- Informations sur le processus -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Comment ça marche ?
                            </h6>
                            <ul class="mb-0 small">
                                <li>Votre demande sera envoyée automatiquement au vendeur</li>
                                <li>Le vendeur pourra vous proposer une réduction</li>
                                <li>Si acceptée, la réduction sera automatiquement appliquée</li>
                                <li>Vous recevrez une notification de la réponse</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </button>
                        <button type="button" class="btn btn-primary" id="submitDiscountBtn" onclick="submitDiscountRequest()">
                            <i class="fas fa-paper-plane me-2"></i>
                            Envoyer maintenant
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth


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

/* Animations pour les notifications */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
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
    window.location.href = `/messages/conversation/{{ $item->user_id }}?item={{ $item->id }}`;
}

// Envoyer une demande de réduction
function submitDiscountRequest() {
    const form = document.getElementById('contactForm');
    const customMessage = document.getElementById('customMessage').value;
    
    // Ajouter le message personnalisé au formulaire
    if (customMessage.trim()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'custom_message';
        input.value = customMessage;
        form.appendChild(input);
    }
    
    // Désactiver le bouton pour éviter les doublons
    const submitBtn = document.querySelector('#contactModal .btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    
    // Soumettre le formulaire
    form.submit();
}

// Vérifier les réductions disponibles au chargement
@auth
    @if(Auth::id() !== $item->user_id)
        document.addEventListener('DOMContentLoaded', function() {
            checkAvailableDiscounts();
        });
        
        function checkAvailableDiscounts() {
            fetch(`/discounts/item/{{ $item->id }}/available`)
                .then(response => response.json())
                .then(discounts => {
                    if (discounts.length > 0) {
                        const discount = discounts[0]; // Prendre la première réduction disponible
                        showDiscountSection(discount);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification des réductions:', error);
                });
        }
        
        function showDiscountSection(discount) {
            const section = document.getElementById('discountSection');
            const info = document.getElementById('discountInfo');
            
            const savings = discount.original_price - discount.final_price;
            const formattedSavings = new Intl.NumberFormat('fr-FR').format(savings);
            const formattedFinalPrice = new Intl.NumberFormat('fr-FR').format(discount.final_price);
            
            info.innerHTML = `
                <strong>Réduction de ${discount.discount_percentage}% disponible !</strong><br>
                <small class="text-muted">
                    Prix original: ${new Intl.NumberFormat('fr-FR').format(discount.original_price)} FCFA<br>
                    Nouveau prix: <span class="text-success fw-bold">${formattedFinalPrice} FCFA</span><br>
                    Économie: <span class="text-success">${formattedSavings} FCFA</span><br>
                    Valable jusqu'au ${new Date(discount.expires_at).toLocaleDateString('fr-FR')}
                </small>
            `;
            
            section.style.display = 'block';
            
            // Stocker l'ID de la réduction pour l'application
            section.dataset.discountId = discount.id;
        }
        
        function applyDiscount() {
            const section = document.getElementById('discountSection');
            const discountId = section.dataset.discountId;
            
            if (!discountId) return;
            
            fetch(`/discounts/${discountId}/apply`, {
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
                    // Mettre à jour l'affichage du prix
                    const priceElement = document.querySelector('.alibaba-price');
                    if (priceElement) {
                        priceElement.innerHTML = `
                            <span class="text-decoration-line-through text-muted me-2">{{ $item->formatted_price }}</span>
                            <span class="text-success">${new Intl.NumberFormat('fr-FR').format(data.final_price)} FCFA</span>
                        `;
                    }
                    
                    // Masquer la section de réduction
                    section.style.display = 'none';
                    
                    // Afficher un message de succès
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.error || 'Erreur lors de l\'application de la réduction', 'danger');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'danger');
            });
        }
    @endif
@endauth
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

// Fonction pour afficher les notifications
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 8px;
        animation: slideInRight 0.3s ease-out;
    `;
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Fonction pour soumettre la demande de réduction
function submitDiscountRequest() {
    const button = document.getElementById('submitDiscountBtn');
    const originalText = button.innerHTML;
    
    // Désactiver le bouton et afficher le chargement
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    
    const customMessage = document.getElementById('customMessage').value;
    
    // Créer un formulaire caché pour l'envoi
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("contact.seller", $item) }}';
    
    // Ajouter le token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Ajouter le message personnalisé
    const messageInput = document.createElement('input');
    messageInput.type = 'hidden';
    messageInput.name = 'custom_message';
    messageInput.value = customMessage;
    form.appendChild(messageInput);
    
    // Ajouter le formulaire au DOM et le soumettre
    document.body.appendChild(form);
    
    // Afficher une notification avant la redirection
    showNotification('Envoi de votre demande en cours...', 'info');
    
    // Petit délai pour que l'utilisateur voie le changement
    setTimeout(() => {
        form.submit();
    }, 500);
}

// Fonction pour contacter directement le vendeur
function contactSeller() {
    const sellerId = {{ $item->user_id }};
    window.location.href = `/messages/${sellerId}?item_id={{ $item->id }}`;
}
</script>
@endsection 