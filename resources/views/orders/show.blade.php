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
                                <i class="fas fa-truck me-2"></i>
                                Adresse de livraison
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-1"><strong>Ville:</strong> {{ $order->shipping_city }}</p>
                                    <p class="mb-1"><strong>Téléphone:</strong> {{ $order->shipping_phone }}</p>
                                    <p class="mb-0"><strong>Adresse:</strong></p>
                                    <p class="text-muted">{{ $order->shipping_address }}</p>
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
                            
                            @if($order->item->user_id === Auth::id())
                                <a href="{{ route('orders.edit', $order) }}" 
                                   class="btn btn-warning me-2"
                                   aria-label="Modifier le statut de la commande {{ $order->order_number }}">
                                    <i class="fas fa-edit me-2"></i>
                                    Modifier le statut
                                </a>
                            @endif
                            
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
                            Contacter le vendeur
                        </a>
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
// Script simplifié - Formulaire HTML utilisé pour la confirmation
console.log('Page de commande chargée');
</script>
@endsection 