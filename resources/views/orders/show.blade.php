@extends('app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Commande {{ $order->order_number }}
                        </h3>
                        <span class="badge {{ $order->status_badge_class }} fs-6">
                            {{ $order->status_text }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations de l'article -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            @if($order->item->images && count($order->item->images) > 0)
                                <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $order->item->name }}">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $order->item->name }}</h4>
                            <p class="text-muted">{{ $order->item->description }}</p>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Prix unitaire</small>
                                    <div class="h5 text-primary fw-bold">{{ $order->formatted_unit_price }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Quantité</small>
                                    <div class="h5 fw-bold">{{ $order->quantity }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="badge bg-primary me-2">{{ $order->item->category->name }}</span>
                                @if($order->item->brand)
                                    <span class="badge bg-secondary">{{ $order->item->brand->name }}</span>
                                @endif
                                <span class="badge condition-badge condition-{{ $order->item->condition }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->item->condition)) }}
                                </span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Vendeur</small>
                                    <div class="fw-bold">{{ $order->item->user->name }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Acheteur</small>
                                    <div class="fw-bold">{{ $order->buyer->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Informations de livraison -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Adresse de livraison
                            </h5>
                            <div class="card border-primary">
                                <div class="card-body">
                                    @if($order->deliveryAddress)
                                        {{-- Afficher depuis delivery_addresses --}}
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user text-purple me-2"></i>
                                                <strong>Destinataire:</strong>
                                            </div>
                                            <p class="ms-4 mb-0">{{ $order->deliveryAddress->full_name }}</p>
                                        </div>

                                        @if($order->deliveryAddress->email)
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-envelope text-info me-2"></i>
                                                    <strong>Email:</strong>
                                                </div>
                                                <p class="ms-4 mb-0">
                                                    <a href="mailto:{{ $order->deliveryAddress->email }}" class="text-decoration-none">
                                                        {{ $order->deliveryAddress->email }}
                                                    </a>
                                                </p>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-phone text-success me-2"></i>
                                                <strong>Téléphone:</strong>
                                            </div>
                                            <p class="ms-4 mb-0">
                                                <a href="tel:{{ $order->deliveryAddress->phone }}" class="text-decoration-none">
                                                    {{ $order->deliveryAddress->phone }}
                                                </a>
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-city text-primary me-2"></i>
                                                <strong>Ville / Commune:</strong>
                                            </div>
                                            <p class="ms-4 mb-0">{{ $order->deliveryAddress->city }}, {{ $order->deliveryAddress->commune }}</p>
                                        </div>

                                        <div class="mb-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-home text-info me-2"></i>
                                                <strong>Adresse complète:</strong>
                                            </div>
                                            <p class="ms-4 mb-0 text-muted">{{ $order->deliveryAddress->address }}</p>
                                        </div>

                                        @if($order->deliveryAddress->notes)
                                            <div class="mt-3 p-2 bg-light rounded">
                                                <small class="text-muted">
                                                    <i class="fas fa-sticky-note me-1"></i>
                                                    <strong>Note:</strong> {{ $order->deliveryAddress->notes }}
                                                </small>
                                            </div>
                                        @endif
                                    @else
                                        {{-- Fallback sur shipping_address si pas de deliveryAddress --}}
                                        @if($order->shipping_city && $order->shipping_city !== 'À définir')
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-city text-primary me-2"></i>
                                                    <strong>Ville:</strong>
                                                </div>
                                                <p class="ms-4 mb-0">{{ $order->shipping_city }}</p>
                                            </div>
                                        @endif

                                        @if($order->shipping_phone)
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-phone text-success me-2"></i>
                                                    <strong>Téléphone:</strong>
                                                </div>
                                                <p class="ms-4 mb-0">
                                                    <a href="tel:{{ $order->shipping_phone }}" class="text-decoration-none">
                                                        {{ $order->shipping_phone }}
                                                    </a>
                                                </p>
                                            </div>
                                        @endif

                                        @if($order->shipping_address && $order->shipping_address !== 'À définir')
                                            <div class="mb-0">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-home text-info me-2"></i>
                                                    <strong>Adresse complète:</strong>
                                                </div>
                                                <p class="ms-4 mb-0 text-muted">{{ $order->shipping_address }}</p>
                                            </div>
                                        @endif

                                        @if((!$order->shipping_city || $order->shipping_city === 'À définir') && 
                                            (!$order->shipping_address || $order->shipping_address === 'À définir') &&
                                            !$order->deliveryAddress)
                                            <div class="alert alert-warning mb-0" role="alert">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Adresse non définie</strong>
                                                <br>
                                                <small>L'adresse de livraison n'a pas encore été définie pour cette commande.</small>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>
                                <i class="fas fa-money-bill me-2"></i>
                                Détails du paiement
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-6">Prix unitaire:</div>
                                        <div class="col-6 text-end">{{ $order->formatted_unit_price }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">Quantité:</div>
                                        <div class="col-6 text-end">{{ $order->quantity }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6"><strong>Total:</strong></div>
                                        <div class="col-6 text-end"><strong class="text-primary">{{ $order->formatted_total_price }}</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="mb-4">
                            <h5>
                                <i class="fas fa-sticky-note me-2"></i>
                                Notes
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">{{ $order->notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Historique des statuts -->
                    <div class="mb-4">
                        <h5>
                            <i class="fas fa-history me-2"></i>
                            Historique
                        </h5>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Commande créée</h6>
                                    <p class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($order->paid_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6>Paiement confirmé</h6>
                                        <p class="text-muted">{{ $order->paid_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($order->shipped_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6>Expédiée</h6>
                                        <p class="text-muted">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($order->delivered_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6>Livrée</h6>
                                        <p class="text-muted">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour aux commandes
                        </a>
                        
                        <div>
                            {{-- Acheteur : Bouton payer si commande en attente (pending) --}}
                            @if($order->buyer_id === Auth::id() && $order->status === 'pending')
                                <form method="POST" action="{{ route('orders.confirm-payment', $order) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-success me-2" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir confirmer le paiement de cette commande ?')"
                                            aria-label="Confirmer le paiement de la commande {{ $order->order_number }}">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Confirmer le paiement
                                    </button>
                                </form>
                            @endif
                            
                            {{-- Vendeur : Bouton expédier si commande confirmée (payée) --}}
                            @if($order->item->user_id === Auth::id() && $order->status === 'confirmed')
                                <form method="POST" action="{{ route('orders.mark-shipped', $order) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-primary me-2" 
                                            onclick="return confirm('Marquer cette commande comme expédiée ?')"
                                            aria-label="Marquer la commande {{ $order->order_number }} comme expédiée">
                                        <i class="fas fa-shipping-fast me-2"></i>
                                        📦 Expédier la commande
                                    </button>
                                </form>
                            @endif
                            
                            {{-- Vendeur : Bouton marquer comme livrée si commande expédiée --}}
                            @if($order->item->user_id === Auth::id() && $order->status === 'shipped')
                                <form method="POST" action="{{ route('orders.mark-delivered', $order) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-success me-2" 
                                            onclick="return confirm('Marquer cette commande comme livrée ?')"
                                            aria-label="Marquer la commande {{ $order->order_number }} comme livrée">
                                        <i class="fas fa-check-circle me-2"></i>
                                        ✅ Marquer comme livrée
                                    </button>
                                </form>
                            @endif
                            
                            {{-- Acheteur : Annuler si pas encore payé --}}
                            @if($order->buyer_id === Auth::id() && $order->status === 'pending')
                                <form method="POST" action="{{ route('orders.destroy', $order) }}" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger"
                                            aria-label="Annuler la commande {{ $order->order_number }}">
                                        <i class="fas fa-times me-2"></i>
                                        Annuler
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('items.show', $order->item) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>
                            Voir l'article
                        </a>
                        
                        @if($order->item->user_id === Auth::id())
                            <a href="{{ route('items.edit', $order->item) }}" class="btn btn-outline-warning">
                                <i class="fas fa-edit me-2"></i>
                                Modifier l'article
                            </a>
                        @endif
                        
                        <a href="{{ route('items.show', $order->item) }}#contact" class="btn btn-outline-info">
                            <i class="fas fa-envelope me-2"></i>
                            Contacter {{ $order->buyer_id === Auth::id() ? 'le vendeur' : 'l\'acheteur' }}
                        </a>

                        {{-- Messages d'état selon le statut de la commande --}}
                        @if($order->status === 'pending')
                            <div class="alert alert-warning mb-0" role="alert">
                                <i class="fas fa-clock me-2"></i>
                                <strong>En attente de paiement</strong>
                                @if($order->buyer_id === Auth::id())
                                    <br><small>Veuillez confirmer le paiement pour continuer</small>
                                @else
                                    <br><small>L'acheteur n'a pas encore payé</small>
                                @endif
                            </div>
                        @elseif($order->status === 'confirmed')
                            <div class="alert alert-info mb-0" role="alert">
                                <i class="fas fa-box me-2"></i>
                                <strong>Paiement confirmé</strong>
                                @if($order->item->user_id === Auth::id())
                                    <br><small>Vous pouvez maintenant expédier la commande</small>
                                @else
                                    <br><small>En attente d'expédition par le vendeur</small>
                                @endif
                            </div>
                        @elseif($order->status === 'shipped' && !$order->confirmed_by_buyer_at)
                            @if($order->buyer_id === Auth::id())
                                <button class="btn btn-success btn-lg" 
                                        onclick="confirmDelivery()">
                                    <i class="fas fa-check-circle me-2"></i>
                                    ✅ Commande Reçue
                                </button>
                                <div class="alert alert-primary mb-0 mt-2" role="alert">
                                    <i class="fas fa-truck me-2"></i>
                                    <small>Cliquez sur "Commande Reçue" une fois la livraison effectuée</small>
                                </div>
                            @else
                                <div class="alert alert-primary mb-0" role="alert">
                                    <i class="fas fa-shipping-fast me-2"></i>
                                    <strong>Commande expédiée</strong>
                                    <br><small>En attente de confirmation de réception par l'acheteur</small>
                                </div>
                            @endif
                        @elseif($order->status === 'delivered' && !$order->confirmed_by_buyer_at)
                            @if($order->buyer_id === Auth::id())
                                <button class="btn btn-success btn-lg" 
                                        onclick="confirmDelivery()">
                                    <i class="fas fa-check-circle me-2"></i>
                                    ✅ Commande Reçue
                                </button>
                                <div class="alert alert-primary mb-0 mt-2" role="alert">
                                    <i class="fas fa-home me-2"></i>
                                    <small>Confirmez la réception pour finaliser la transaction</small>
                                </div>
                            @else
                                <div class="alert alert-success mb-0" role="alert">
                                    <i class="fas fa-check me-2"></i>
                                    <strong>Commande livrée</strong>
                                    <br><small>En attente de confirmation par l'acheteur</small>
                                </div>
                            @endif
                        @endif

                        {{-- Confirmation de réception effectuée --}}
                        @if($order->confirmed_by_buyer_at)
                            <div class="alert alert-success mb-0" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>✅ Réception confirmée</strong>
                                <br>
                                <small>Le {{ $order->confirmed_by_buyer_at->format('d/m/Y à H:i') }}</small>
                                @if($order->buyer_confirmation_note)
                                    <br><small class="text-muted fst-italic">"{{ $order->buyer_confirmation_note }}"</small>
                                @endif
                                <hr class="my-2">
                                <small class="text-muted">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    La distribution des fonds a été effectuée
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6"><small class="text-muted">Numéro:</small></div>
                        <div class="col-6 text-end"><small>{{ $order->order_number }}</small></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><small class="text-muted">Créée le:</small></div>
                        <div class="col-6 text-end"><small>{{ $order->created_at->format('d/m/Y') }}</small></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><small class="text-muted">Devise:</small></div>
                        <div class="col-6 text-end"><small>{{ $order->currency }}</small></div>
                    </div>
                    @if($order->updated_at !== $order->created_at)
                        <div class="row">
                            <div class="col-6"><small class="text-muted">Modifiée le:</small></div>
                            <div class="col-6 text-end"><small>{{ $order->updated_at->format('d/m/Y') }}</small></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 20px;
    border-left: 2px solid #e9ecef;
    padding-bottom: 10px;
}

.timeline-content h6 {
    margin-bottom: 5px;
}
</style>

<script>
// Script pour confirmer la réception de la commande
function confirmDelivery() {
    const note = prompt('Confirmez-vous avoir reçu votre commande ?\n\nVous pouvez ajouter un commentaire (optionnel) :');
    
    if (note !== null) { // L'utilisateur n'a pas cliqué sur Annuler
        fetch('{{ route('orders.confirm-delivery', $order) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                note: note || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.error || 'Erreur lors de la confirmation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la confirmation');
        });
    }
}

console.log('Page de commande chargée');
</script>
@endsection 