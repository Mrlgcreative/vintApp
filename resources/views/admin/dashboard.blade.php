@extends('layouts.admin')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Carte Utilisateurs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-2">Utilisateurs</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-xs text-gray-500 mt-1">+{{ $stats['new_users_today'] }} aujourd'hui</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-2xl text-primary-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-600"></div>
    </div>

    <!-- Carte Revenus USD -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Revenus USD</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_revenue_usd'], 2) }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $stats['transactions_today'] }} transactions aujourd'hui</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
    </div>

    <!-- Carte Revenus CDF -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-2">Revenus CDF</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue_cdf'], 0, ',', ' ') }} FC</div>
                    <div class="text-xs text-gray-500 mt-1">Franc Congolais</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-coins text-2xl text-emerald-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
    </div>

    <!-- Carte Wallets en attente -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-yellow-600 uppercase tracking-wider mb-2">Wallets Pending</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['pending_wallets'] }}</div>
                    <div class="text-xs mt-1 space-y-1">
                        <div class="text-gray-600">${{ number_format($stats['pending_wallets_usd'], 2) }} USD</div>
                        <div class="text-gray-600">{{ number_format($stats['pending_wallets_cdf'], 0, ',', ' ') }} CDF</div>
                        <a href="{{ route('admin.wallets.pending') }}" class="text-yellow-600 hover:text-yellow-700 font-medium inline-block mt-1">Voir détails</a>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-yellow-500 to-yellow-600"></div>
    </div>

    <!-- Carte Articles actifs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Articles actifs</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_items']) }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ number_format($stats['total_items']) }} au total</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
    </div>

    <!-- Carte Vérifications d'authenticité -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-orange-600 uppercase tracking-wider mb-2">Vérifications</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_verifications'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $stats['pending_verifications'] ?? 0 }} en attente</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-certificate text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-orange-500 to-orange-600"></div>
    </div>

    <!-- Carte Commissions USD (Sous-wallet Commission) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-purple-600 uppercase tracking-wider mb-2">Commissions USD</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['enterprise_commission_usd'] ?? 0, 2) }}</div>
                    <div class="text-xs text-gray-500 mt-1">Sous-wallet commission</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-purple-500 to-purple-600"></div>
    </div>
</div>

<!-- Nouveau panel pour les sous-wallets entreprise -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Sous-wallet Transport -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-cyan-600 uppercase tracking-wider mb-2">Transport</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['enterprise_transport_usd'] ?? 0, 2) }}</div>
                    <div class="text-xs text-gray-500 mt-1">Sous-wallet transport</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-truck text-2xl text-cyan-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-cyan-500 to-cyan-600"></div>
    </div>

    <!-- Sous-wallet Boost -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-pink-600 uppercase tracking-wider mb-2">Boost</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['enterprise_boost_usd'] ?? 0, 2) }}</div>
                    <div class="text-xs text-gray-500 mt-1">Sous-wallet boost</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rocket text-2xl text-pink-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-pink-500 to-pink-600"></div>
    </div>

    <!-- Revenus Vérifications -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-2">Revenus Vérifications</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['verification_revenue_usd'] ?? 0, 2) }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $stats['completed_verifications'] ?? 0 }} vérifications payées</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-2xl text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-amber-500 to-amber-600"></div>
    </div>

    <!-- Total Entreprise -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Total Entreprise</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format(($stats['enterprise_commission_usd'] ?? 0) + ($stats['enterprise_transport_usd'] ?? 0) + ($stats['enterprise_boost_usd'] ?? 0), 2) }}</div>
                    <div class="text-xs text-gray-500 mt-1">Tous sous-wallets USD</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-2xl text-gray-700"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-gray-500 to-gray-700"></div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <!-- Graphique des statistiques quotidiennes -->
    <div class="xl:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Évolution des 30 derniers jours</h3>
            </div>
            <div class="p-6">
                <div class="relative">
                    <canvas id="dailyStatsChart" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="xl:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Activité récente</h3>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Utilisateurs actifs (7 jours)</div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['active_users'] }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Commandes en attente</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Total des fonds en wallets</div>
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_wallet_balance'], 2) }} USD</div>
                    </div>
                </div>

                <div class="mt-8 space-y-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
                        <i class="fas fa-users mr-2"></i>
                        Gérer les utilisateurs
                    </a>
                    <a href="{{ route('admin.wallets.pending') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                        <i class="fas fa-wallet mr-2"></i>
                        Wallets pending (en attente confirmation)
                    </a>
                    <a href="{{ route('admin.transactions.index') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Voir les transactions
                    </a>
                    <a href="#" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors duration-200">
                        <i class="fas fa-certificate mr-2"></i>
                        Vérifications d'authenticité
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Dernières transactions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Dernières transactions</h3>
        </div>
        <div class="p-6">
            @if($recentTransactions->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTransactions as $transaction)
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($transaction->status === 'completed')
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                @elseif($transaction->status === 'pending')
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-clock text-yellow-600"></i>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-times text-red-600"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $transaction->user->name }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ $transaction->description }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-4">
                                        <p class="text-sm font-semibold text-gray-900">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</p>
                                        <p class="text-xs text-gray-500">{{ $transaction->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('admin.transactions.index') }}" 
                       class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700">
                        Voir toutes les transactions
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Aucune transaction récente</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Nouveaux utilisateurs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Nouveaux utilisateurs</h3>
        </div>
        <div class="p-6">
            @if($recentUsers->count() > 0)
                <div class="space-y-4">
                    @foreach($recentUsers as $user)
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                                @else
                                    <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ $user->initial }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-4">
                                        <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                        @if($user->isOnline())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                                En ligne
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700">
                        Voir tous les utilisateurs
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Aucun nouvel utilisateur récemment</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des statistiques quotidiennes
    const ctx = document.getElementById('dailyStatsChart').getContext('2d');
    const dailyStats = @json($dailyStats);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyStats.map(stat => {
                const date = new Date(stat.date);
                return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Nouveaux utilisateurs',
                data: dailyStats.map(stat => stat.users),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1
            }, {
                label: 'Transactions',
                data: dailyStats.map(stat => stat.transactions),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1
            }, {
                label: 'Revenus (USD)',
                data: dailyStats.map(stat => stat.revenue),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
});
</script>
@endpush