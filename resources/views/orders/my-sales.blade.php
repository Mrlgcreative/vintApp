@extends('app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-store me-2"></i>
                            Mes Ventes
                        </h3>
                        <div>
                            <span class="badge bg-primary fs-6">{{ $orders->total() }} commande(s)</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Commande</th>
                                        <th>Article</th>
                                        <th>Acheteur</th>
                                        <th>Prix</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $order->order_number }}</div>
                                                <small class="text-muted">Qty: {{ $order->quantity }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($order->item && $order->item->images && count($order->item->images) > 0)
                                                        <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                                             class="rounded me-2" 
                                                             style="width: 40px; height: 40px; object-fit: cover;"
                                                             alt="{{ $order->item->name }}">
                                                    @else
                                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $order->item->name ?? 'Article supprimé' }}</div>
                                                        <small class="text-muted">{{ $order->item->category->name ?? 'Catégorie inconnue' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $order->user->name ?? 'Utilisateur inconnu' }}</div>
                                                <small class="text-muted">{{ $order->shipping_city ?? 'Ville non spécifiée' }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary">{{ $order->formatted_total_price }}</div>
                                                <small class="text-muted">{{ $order->formatted_unit_price }} × {{ $order->quantity }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $order->status_badge_class }} fs-6">
                                                    {{ $order->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('orders.show', $order) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Voir les détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($order->status === 'pending')
                                                        <form method="POST" action="{{ route('orders.update', $order) }}" style="display: inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="confirmed">
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-success" 
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir confirmer cette commande ?')"
                                                                    title="Confirmer la commande">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($order->status === 'confirmed')
                                                        <form method="POST" action="{{ route('orders.update', $order) }}" style="display: inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="shipped">
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-info" 
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir marquer cette commande comme expédiée ?')"
                                                                    title="Marquer comme expédiée">
                                                                <i class="fas fa-shipping-fast"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($order->status === 'shipped')
                                                        <form method="POST" action="{{ route('orders.update', $order) }}" style="display: inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="delivered">
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-success" 
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir marquer cette commande comme livrée ?')"
                                                                    title="Marquer comme livrée">
                                                                <i class="fas fa-box-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucune vente pour le moment</h4>
                            <p class="text-muted">Vous n'avez pas encore reçu de commandes.</p>
                            <a href="{{ route('items.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Vendre un article
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
@if($orders->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total des ventes</h6>
                            <h4 class="mb-0">{{ $orders->sum('total_price') }} {{ $orders->first()->currency ?? 'USD' }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Commandes confirmées</h6>
                            <h4 class="mb-0">{{ $orders->where('status', 'confirmed')->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">En attente</h6>
                            <h4 class="mb-0">{{ $orders->where('status', 'pending')->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Expédiées</h6>
                            <h4 class="mb-0">{{ $orders->where('status', 'shipped')->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shipping-fast fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
function confirmOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir confirmer cette commande ?')) {
        updateOrderStatus(orderId, 'confirmed', 'Commande confirmée avec succès !');
    }
}

function shipOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir marquer cette commande comme expédiée ?')) {
        updateOrderStatus(orderId, 'shipped', 'Commande marquée comme expédiée !');
    }
}

function deliverOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir marquer cette commande comme livrée ?')) {
        updateOrderStatus(orderId, 'delivered', 'Commande marquée comme livrée !');
    }
}

function updateOrderStatus(orderId, status, successMessage) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/orders/${orderId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            status: status,
            _method: 'PUT'
        })
    })
    .then(response => {
        if (response.ok) {
            alert(successMessage);
            window.location.reload();
        } else {
            alert('Erreur lors de la mise à jour du statut');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
}
</script>
@endsection 