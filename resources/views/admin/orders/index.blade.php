@extends('layouts.admin')

@section('title', 'Gestion des commandes')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Gestion des commandes</h1>
</div>

<!-- Filtres -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
    <div class="p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Expédié</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Livré</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            
            <div>
                <label for="date_to" class="form-label">Date fin</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-3">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="ID commande, utilisateur..." value="{{ request('search') }}">
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrer
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Liste des commandes -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            Liste des commandes 
            @if(isset($orders))
                <span class="badge bg-secondary">{{ $orders->total() ?? 0 }} total</span>
            @endif
        </h5>
    </div>
    <div class="card-body p-0">
        @if(isset($orders) && $orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Acheteur</th>
                            <th>Vendeur</th>
                            <th>Article</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong>#{{ $order->id }}</strong>
                            </td>
                            <td>
                                @if($order->buyer)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            @if($order->buyer->avatar)
                                                <img src="{{ $order->buyer->avatar_url }}" class="rounded-circle" width="32" height="32">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    {{ $order->buyer->initial }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $order->buyer->name }}</div>
                                            <small class="text-muted">{{ $order->buyer->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Utilisateur supprimé</span>
                                @endif
                            </td>
                            <td>
                                @if($order->seller)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            @if($order->seller->avatar)
                                                <img src="{{ $order->seller->avatar_url }}" class="rounded-circle" width="32" height="32">
                                            @else
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    {{ $order->seller->initial }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $order->seller->name }}</div>
                                            <small class="text-muted">{{ $order->seller->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Utilisateur supprimé</span>
                                @endif
                            </td>
                            <td>
                                @if($order->item)
                                    <div class="d-flex align-items-center">
                                        @if($order->item->images && count($order->item->images) > 0)
                                            <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                                 class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ Str::limit($order->item->title, 30) }}</div>
                                            <small class="text-muted">{{ $order->item->brand->name ?? 'Sans marque' }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Article supprimé</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ number_format($order->total_amount ?? 0, 2) }} {{ $order->currency ?? 'USD' }}</strong>
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning',
                                        'confirmed' => 'bg-info',
                                        'shipped' => 'bg-primary',
                                        'delivered' => 'bg-success',
                                        'cancelled' => 'bg-danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'En attente',
                                        'confirmed' => 'Confirmé',
                                        'shipped' => 'Expédié',
                                        'delivered' => 'Livré',
                                        'cancelled' => 'Annulé'
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewOrder({{ $order->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($order->status === 'pending')
                                        <button class="btn btn-outline-success" onclick="confirmOrder({{ $order->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="cancelOrder({{ $order->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="card-footer">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5>Aucune commande trouvée</h5>
                <p class="text-muted">Il n'y a aucune commande correspondant à vos critères.</p>
            </div>
        @endif
    </div>
</div>

<script>
function viewOrder(orderId) {
    // Redirection vers la page de détails de la commande
    window.location.href = `/admin/orders/${orderId}`;
}

function confirmOrder(orderId) {
    if (confirm('Confirmer cette commande ?')) {
        // AJAX call to confirm order
        console.log('Confirming order:', orderId);
    }
}

function cancelOrder(orderId) {
    if (confirm('Annuler cette commande ?')) {
        // AJAX call to cancel order
        console.log('Cancelling order:', orderId);
    }
}
</script>
@endsection