@extends('app')

@section('content')
<style>
    .dashboard-card {
        border: none;
        border-radius: 1.2rem;
        box-shadow: 0 4px 24px rgba(124,58,237,0.08), 0 1.5px 4px rgba(0,0,0,0.04);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .dashboard-card:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 32px rgba(124,58,237,0.13), 0 2px 8px rgba(0,0,0,0.07);
    }
    .dashboard-card .card-title {
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    .dashboard-card .icon-bg {
        background: linear-gradient(135deg, #7c3aed 60%, #a78bfa 100%);
        color: #fff;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        box-shadow: 0 2px 8px rgba(124,58,237,0.10);
    }
    .dashboard-section .card-header {
        background: #f8fafc;
        border-bottom: none;
        border-radius: 1.2rem 1.2rem 0 0;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .dashboard-section .list-group-item {
        border: none;
        border-radius: 0.7rem;
        margin-bottom: 0.5rem;
        transition: background 0.15s;
    }
    .dashboard-section .list-group-item:hover {
        background: #f3f0ff;
    }
    .dashboard-badge {
        font-size: 1rem;
        border-radius: 1rem;
        padding: 0.4em 1em;
        font-weight: 500;
    }
</style>
<div class="container-fluid mt-4">
    <div class="row mb-4 row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <div class="col">
            <div class="card dashboard-card bg-white text-dark h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-bg"><i class="fas fa-box"></i></div>
                    <div>
                        <div class="card-title mb-1">Articles</div>
                        <div class="h2 mb-0">{{ $stats['total_items'] ?? 0 }}</div>
                        <small class="text-muted">Actifs : {{ $stats['active_items'] ?? 0 }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card dashboard-card bg-white text-dark h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-bg" style="background:linear-gradient(135deg,#22c55e 60%,#bbf7d0 100%)"><i class="fas fa-shopping-cart"></i></div>
                    <div>
                        <div class="card-title mb-1">Ventes</div>
                        <div class="h2 mb-0">{{ $stats['total_sales'] ?? 0 }}</div>
                        <small class="text-muted">Revenu : {{ number_format($stats['total_revenue'] ?? 0, 2) }} €</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card dashboard-card bg-white text-dark h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-bg" style="background:linear-gradient(135deg,#0ea5e9 60%,#bae6fd 100%)"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="card-title mb-1">Messages</div>
                        <div class="h2 mb-0">{{ $stats['unread_messages'] ?? 0 }}</div>
                        <small class="text-muted">Non lus</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card dashboard-card bg-white text-dark h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-bg" style="background:linear-gradient(135deg,#f59e42 60%,#fef08a 100%)"><i class="fas fa-bell"></i></div>
                    <div>
                        <div class="card-title mb-1">Notifications</div>
                        <div class="h2 mb-0">{{ $stats['unread_notifications'] ?? 0 }}</div>
                        <small class="text-muted">Non lues</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row dashboard-section">
        <div class="col-lg-6 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header"><i class="fas fa-box me-2"></i>Articles récents</div>
                <div class="card-body">
                    @if(isset($recentItems) && $recentItems->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentItems as $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $item->name }}</h6>
                                        <small class="text-muted">{{ $item->category->name ?? 'N/A' }}</small>
                                    </div>
                                    <span class="dashboard-badge bg-primary text-white">{{ $item->formatted_price }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun article récent</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header"><i class="fas fa-shopping-cart me-2"></i>Commandes récentes</div>
                <div class="card-body">
                    @if(isset($recentOrders) && $recentOrders->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentOrders as $order)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Commande #{{ $order->id }}</h6>
                                        <small class="text-muted">{{ $order->item->name ?? 'N/A' }}</small>
                                    </div>
                                    <span class="dashboard-badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }} text-white">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune commande récente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row dashboard-section">
        <div class="col-lg-6 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header"><i class="fas fa-envelope me-2"></i>Messages récents</div>
                <div class="card-body">
                    @if(isset($recentMessages) && $recentMessages->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentMessages as $msg)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $msg->sender->name ?? 'N/A' }}</h6>
                                        <p class="mb-1">{{ Str::limit($msg->content, 50) }}</p>
                                    </div>
                                    <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun message récent</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header"><i class="fas fa-bell me-2"></i>Notifications</div>
                <div class="card-body">
                    @if(isset($notifications) && $notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notif)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $notif->title }}</h6>
                                        <p class="mb-1">{{ Str::limit($notif->message, 50) }}</p>
                                    </div>
                                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune notification</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row dashboard-section">
        <div class="col-12 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header"><i class="fas fa-chart-line me-2"></i>Évolution des ventes (6 derniers mois)</div>
                <div class="card-body">
                    @if(isset($salesChart))
                        <div class="text-center">
                            <p class="text-muted">Graphique des ventes</p>
                            <div class="row">
                                @foreach($salesChart['labels'] as $index => $label)
                                    <div class="col">
                                        <div class="text-center">
                                            <div class="h4 mb-0">{{ $salesChart['data'][$index] ?? 0 }}</div>
                                            <small class="text-muted">{{ $label }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune donnée de vente disponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 