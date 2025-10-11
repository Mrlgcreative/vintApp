@extends('app')

@section('content')
<div class="container py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">
            <i class="fas fa-store me-2 text-primary"></i>
            Mes Ventes
        </h1>
        <span class="badge bg-primary rounded-pill fs-6">
            {{ $orders->total() }} commande(s)
        </span>
    </div>

    <!-- Statistiques rapides -->
    @if($orders->count() > 0)
        <div class="row g-3 mb-4">
            <!-- En attente (à payer) -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-start border-warning border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-warning text-uppercase fw-semibold small mb-1">En attente</h6>
                                <h2 class="mb-0 fw-bold">{{ $orders->where('status', 'pending')->count() }}</h2>
                                <p class="text-muted small mb-0">Paiement attendu</p>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payées (à expédier) -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-start border-primary border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-primary text-uppercase fw-semibold small mb-1">À expédier</h6>
                                <h2 class="mb-0 fw-bold">{{ $orders->where('status', 'confirmed')->count() }}</h2>
                                <p class="text-muted small mb-0">Prêtes à envoyer</p>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-box fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expédiées -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-start border-info border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-info text-uppercase fw-semibold small mb-1">En transit</h6>
                                <h2 class="mb-0 fw-bold">{{ $orders->where('status', 'shipped')->count() }}</h2>
                                <p class="text-muted small mb-0">En livraison</p>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-shipping-fast fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Livrées/Terminées -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-start border-success border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-success text-uppercase fw-semibold small mb-1">Terminées</h6>
                                <h2 class="mb-0 fw-bold">{{ $orders->whereIn('status', ['delivered', 'completed'])->count() }}</h2>
                                <p class="text-muted small mb-0">Paiement distribué</p>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtres rapides -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('orders.my-sales') }}" 
                   class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="fas fa-list me-2"></i>
                    Toutes ({{ $orders->total() }})
                </a>
                <a href="{{ route('orders.my-sales', ['status' => 'pending']) }}" 
                   class="btn {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    <i class="fas fa-clock me-2"></i>
                    En attente
                </a>
                <a href="{{ route('orders.my-sales', ['status' => 'confirmed']) }}" 
                   class="btn {{ request('status') === 'confirmed' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-box me-2"></i>
                    À expédier
                </a>
                <a href="{{ route('orders.my-sales', ['status' => 'shipped']) }}" 
                   class="btn {{ request('status') === 'shipped' ? 'btn-info' : 'btn-outline-info' }}">
                    <i class="fas fa-shipping-fast me-2"></i>
                    Expédiées
                </a>
                <a href="{{ route('orders.my-sales', ['status' => 'delivered,completed']) }}" 
                   class="btn {{ in_array(request('status'), ['delivered', 'completed']) ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle me-2"></i>
                    Terminées
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="card shadow-sm">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Commande</th>
                            <th>Article</th>
                            <th>Acheteur</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <div class="fw-bold">#{{ $order->id }}</div>
                                    <small class="text-muted">{{ $order->order_number }}</small>
                                    <div><small class="text-muted">Qté: {{ $order->quantity }}</small></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($order->item && $order->item->images && count($order->item->images) > 0)
                                            <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                                 class="rounded me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 alt="{{ $order->item->name }}">
                                        @else
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ Str::limit($order->item->name ?? 'Article supprimé', 30) }}</div>
                                            <small class="text-muted">{{ $order->item->category->name ?? 'Catégorie inconnue' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($order->buyer && $order->buyer->avatar)
                                            <img src="{{ $order->buyer->avatar_url }}" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 35px; height: 35px; object-fit: cover;"
                                                 alt="Avatar">
                                        @else
                                            <div class="bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center text-primary fw-semibold" 
                                                 style="width: 35px; height: 35px; font-size: 0.75rem;">
                                                {{ $order->buyer->initial ?? '?' }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $order->buyer->name ?? 'Utilisateur inconnu' }}</div>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $order->shipping_city ?? 'Ville non spécifiée' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</div>
                                    <small class="text-muted">{{ number_format($order->unit_price, 2) }} × {{ $order->quantity }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-warning', 'icon' => 'fa-clock'],
                                            'confirmed' => ['class' => 'bg-primary', 'icon' => 'fa-check'],
                                            'shipped' => ['class' => 'bg-info', 'icon' => 'fa-shipping-fast'],
                                            'delivered' => ['class' => 'bg-success', 'icon' => 'fa-box-check'],
                                            'completed' => ['class' => 'bg-success', 'icon' => 'fa-check-circle'],
                                            'cancelled' => ['class' => 'bg-danger', 'icon' => 'fa-times-circle'],
                                        ];
                                        $config = $statusConfig[$order->status] ?? ['class' => 'bg-secondary', 'icon' => 'fa-question'];
                                    @endphp
                                    <span class="badge {{ $config['class'] }}">
                                        <i class="fas {{ $config['icon'] }} me-1"></i>
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Voir détails -->
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="btn btn-outline-primary"
                                           title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Expédier (si confirmée) -->
                                        @if($order->status === 'confirmed')
                                            <form method="POST" action="{{ route('orders.mark-shipped', $order) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Marquer cette commande comme expédiée ?')"
                                                        class="btn btn-outline-info"
                                                        title="Expédier">
                                                    <i class="fas fa-shipping-fast"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <!-- Marquer livrée (si expédiée) -->
                                        @if($order->status === 'shipped')
                                            <form method="POST" action="{{ route('orders.mark-delivered', $order) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Marquer cette commande comme livrée ?')"
                                                        class="btn btn-outline-success"
                                                        title="Marquer livrée">
                                                    <i class="fas fa-check-circle"></i>
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
            @if($orders->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fas fa-store fa-3x text-muted"></i>
                    </div>
                </div>
                <h3 class="h4 mb-3">Aucune vente pour le moment</h3>
                <p class="text-muted mb-4">Vous n'avez pas encore reçu de commandes pour vos articles.</p>
                <a href="{{ route('items.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Vendre un article
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 