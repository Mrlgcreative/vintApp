@extends('app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Mes Commandes
                </h1>
                <a href="{{ route('orders.my-sales') }}" class="btn btn-outline-primary">
                    <i class="fas fa-store me-2"></i>
                    Mes Ventes
                </a>
            </div>

            @if($orders->count() > 0)
                <div class="row g-4">
                    @foreach($orders as $order)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 order-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $order->order_number }}</h6>
                                    <span class="badge {{ $order->status_badge_class }}">
                                        {{ $order->status_text }}
                                    </span>
                                </div>

                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            @if($order->item->images && count($order->item->images) > 0)
                                                <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                                     class="img-thumbnail" 
                                                     alt="{{ $order->item->name }}"
                                                     style="height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="height: 80px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-8">
                                            <h6 class="card-title">{{ Str::limit($order->item->name, 40) }}</h6>
                                            <p class="text-muted mb-1">
                                                <small>Quantité: {{ $order->quantity }}</small>
                                            </p>
                                            <p class="text-primary fw-bold mb-0">
                                                {{ $order->formatted_total_price }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Vendeur</small>
                                            <div class="fw-bold">{{ $order->item->user->name }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Date</small>
                                            <div class="fw-bold">{{ $order->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>

                                    @if($order->shipping_city)
                                        <div class="mb-3">
                                            <small class="text-muted">Livraison</small>
                                            <div class="fw-bold">{{ $order->shipping_city }}</div>
                                        </div>
                                    @endif

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i>
                                            Voir détails
                                        </a>
                                        
                                        @if($order->status === 'pending')
                                            <button class="btn btn-danger btn-sm" 
                                                    data-order-id="{{ $order->id }}"
                                                    onclick="cancelOrder(this.dataset.orderId)">
                                                <i class="fas fa-times me-2"></i>
                                                Annuler
                                            </button>
                                        @endif
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
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune commande</h4>
                    <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
                    <a href="{{ route('items.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>
                        Découvrir des articles
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        fetch(`/orders/${orderId}`, {
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
                window.location.reload();
            } else {
                alert('Erreur lors de l\'annulation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}
</script>
@endsection 