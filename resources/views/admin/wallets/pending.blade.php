@extends('layouts.admin')

@section('title', 'Wallets Pending')
@section('page-title', 'Wallets Pending - Argent en Attente de Confirmation')

@section('content')
<!-- Section d'information - Responsive -->
<div class="mb-6">
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 md:p-6 rounded-r-lg">
        <div class="flex flex-col sm:flex-row sm:items-start">
            <div class="flex-shrink-0 mb-3 sm:mb-0">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="sm:ml-3">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">À propos des Wallets Pending</h3>
                <div class="text-sm text-blue-700 space-y-2">
                    <p>• Ces wallets contiennent de l'argent en attente de confirmation de réception par l'acheteur</p>
                    <p>• L'argent reste bloqué jusqu'à ce que l'acheteur clique sur "Commande Reçue"</p>
                    <p>• Après confirmation, la distribution automatique transfère : Commission (10%) + Transport (5%) → Plateforme | Reste (85%) → Vendeur</p>
                    <p>• <strong>Type</strong> : "pending" (sécurisé, non retirable) | <strong>Type</strong> : "main" (retirable après confirmation)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Carte principale avec en-tête responsive -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- En-tête avec totaux - Responsive -->
    <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-4 md:px-6 py-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-wallet text-yellow-600 mr-2"></i>
                <span class="hidden sm:inline">Wallets Pending</span>
                <span class="sm:hidden">Pending</span>
                <span class="ml-2 bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm font-medium">
                    {{ $pendingWallets->total() }}
                </span>
            </h3>
            
            <!-- Totaux en grille responsive -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:flex lg:items-center lg:space-x-6">
                <div class="text-center lg:text-right">
                    <span class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Total USD</span>
                    <span class="text-lg font-bold text-green-600">
                        ${{ number_format($pendingWallets->where('currency', 'USD')->sum('balance'), 2) }}
                    </span>
                </div>
                <div class="text-center lg:text-right">
                    <span class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Total CDF</span>
                    <span class="text-lg font-bold text-blue-600">
                        {{ number_format($pendingWallets->where('currency', 'CDF')->sum('balance'), 0, ',', ' ') }} FC
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal - Table sur desktop, cartes sur mobile -->
    <div class="p-0">
        @if($pendingWallets->count() > 0)
            <!-- Vue tableau - Desktop et tablettes -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendeur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Devise</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dernière MAJ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                        @foreach($pendingWallets as $wallet)
                            <tr class="hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($wallet->user?->avatar)
                                            <img src="{{ $wallet->user->avatar_url }}" class="w-10 h-10 rounded-full mr-3" alt="Avatar">
                                        @else
                                            <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                                                {{ $wallet->user?->initial ?? 'U' }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $wallet->user?->name ?? 'Utilisateur supprimé' }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $wallet->user?->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wallet->currency === 'USD' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $wallet->currency }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($wallet->balance, $wallet->currency === 'USD' ? 2 : 0, ',', ' ') }} {{ $wallet->currency }}
                                    </div>
                                    @php
                                        $commission = $wallet->balance * 0.10;
                                        $transport = $wallet->balance * 0.05;
                                        $seller = $wallet->balance - $commission - $transport;
                                    @endphp
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Distribution : {{ number_format($seller, $wallet->currency === 'USD' ? 2 : 0) }} vendeur + {{ number_format($commission + $transport, $wallet->currency === 'USD' ? 2 : 0) }} plateforme
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-lock mr-1"></i>
                                        {{ ucfirst($wallet->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wallet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ $wallet->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div>{{ $wallet->updated_at->format('d/m/Y H:i') }}</div>
                                    <div class="text-xs text-gray-400">{{ $wallet->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if($wallet->user)
                                            <a href="{{ route('admin.users.show', $wallet->user) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-150"
                                               title="Voir utilisateur">
                                                <i class="fas fa-user text-sm"></i>
                                                <span class="ml-1.5 text-xs font-medium">Profil</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('orders.index') }}?seller_id={{ $wallet->user_id }}&status=pending" 
                                           class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-150"
                                           title="Voir commandes en attente">
                                            <i class="fas fa-shopping-cart text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Commandes</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Vue cartes - Mobile et petites tablettes -->
            <div class="lg:hidden">
                <div class="space-y-4 p-4">
                    @foreach($pendingWallets as $wallet)
                        <div class="wallet-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-150">
                            <!-- En-tête de la carte avec utilisateur -->
                            <div class="flex items-center mb-4">
                                @if($wallet->user?->avatar)
                                    <img src="{{ $wallet->user->avatar_url }}" class="w-12 h-12 rounded-full mr-3" alt="Avatar">
                                @else
                                    <div class="w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                                        {{ $wallet->user?->initial ?? 'U' }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $wallet->user?->name ?? 'Utilisateur supprimé' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $wallet->user?->email ?? 'N/A' }}</div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wallet->currency === 'USD' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $wallet->currency }}
                                </span>
                            </div>
                            
                            <!-- Informations financières -->
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 mb-4">
                                <div class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    {{ number_format($wallet->balance, $wallet->currency === 'USD' ? 2 : 0, ',', ' ') }} {{ $wallet->currency }}
                                </div>
                                @php
                                    $commission = $wallet->balance * 0.10;
                                    $transport = $wallet->balance * 0.05;
                                    $seller = $wallet->balance - $commission - $transport;
                                @endphp
                                <div class="text-xs text-gray-600 dark:text-gray-300">
                                    <div class="mb-1">
                                        <span class="font-medium">Distribution prévue :</span>
                                    </div>
                                    <div class="space-y-1">
                                        <div>• Vendeur : {{ number_format($seller, $wallet->currency === 'USD' ? 2 : 0) }} {{ $wallet->currency }}</div>
                                        <div>• Plateforme : {{ number_format($commission + $transport, $wallet->currency === 'USD' ? 2 : 0) }} {{ $wallet->currency }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Statuts et informations -->
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-lock mr-1"></i>
                                    {{ ucfirst($wallet->type) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wallet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-circle text-xs mr-1"></i>
                                    {{ $wallet->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $wallet->updated_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row gap-2">
                                @if($wallet->user)
                                    <a href="{{ route('admin.users.show', $wallet->user) }}" 
                                       class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-150"
                                       title="Voir utilisateur">
                                        <i class="fas fa-user text-sm mr-2"></i>
                                        <span class="text-sm font-medium">Voir Profil</span>
                                    </a>
                                @endif
                                <a href="{{ route('orders.index') }}?seller_id={{ $wallet->user_id }}&status=pending" 
                                   class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-150"
                                   title="Voir commandes en attente">
                                    <i class="fas fa-shopping-cart text-sm mr-2"></i>
                                    <span class="text-sm font-medium">Commandes</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- État vide - Responsive -->
            <div class="text-center py-12 px-4">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-600"></i>
                </div>
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun wallet pending</h5>
                <p class="text-gray-500 dark:text-gray-400 mb-4 max-w-md mx-auto">Tous les paiements ont été confirmés et distribués.</p>
                <p class="text-sm text-gray-400 max-w-lg mx-auto">Les wallets pending apparaissent ici lorsqu'un acheteur paie mais n'a pas encore confirmé la réception.</p>
            </div>
        @endif
    </div>
    
    <!-- Pagination responsive -->
    @if($pendingWallets->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 md:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="text-sm text-gray-700 dark:text-gray-200 text-center sm:text-left">
                    Affichage de 
                    <span class="font-medium">{{ $pendingWallets->firstItem() ?? 0 }}</span>
                    à 
                    <span class="font-medium">{{ $pendingWallets->lastItem() ?? 0 }}</span>
                    sur 
                    <span class="font-medium">{{ $pendingWallets->total() }}</span>
                    résultats
                </div>
                <div class="flex justify-center">
                    {{ $pendingWallets->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Améliorations responsive personnalisées */
    @media (max-width: 640px) {
        .truncate-mobile {
            max-width: 120px;
        }
    }
    
    /* Animation pour les cartes mobile */
    @media (max-width: 1024px) {
        .wallet-card {
            transform: translateY(0);
            transition: all 0.2s ease;
        }
        
        .wallet-card:hover {
            transform: translateY(-2px);
        }
    }
    
    /* Amélioration des badges responsive */
    .badge-responsive {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
    }
    
    @media (max-width: 640px) {
        .badge-responsive {
            font-size: 0.6875rem;
            padding: 0.1875rem 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Amélioration UX pour les cartes mobiles
    const walletCards = document.querySelectorAll('.wallet-card');
    
    walletCards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        card.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Auto-refresh des timestamps toutes les minutes
    function updateTimestamps() {
        const timestamps = document.querySelectorAll('[data-timestamp]');
        timestamps.forEach(element => {
            const timestamp = element.getAttribute('data-timestamp');
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) {
                element.textContent = 'À l\'instant';
            } else if (diff < 3600000) {
                element.textContent = `Il y a ${Math.floor(diff / 60000)} min`;
            } else if (diff < 86400000) {
                element.textContent = `Il y a ${Math.floor(diff / 3600000)}h`;
            }
        });
    }
    
    // Mettre à jour toutes les minutes
    setInterval(updateTimestamps, 60000);
    
    // Performance monitoring pour mobile
    if (window.innerWidth < 768) {
        console.log('🚀 Mode mobile optimisé activé');
        
        // Lazy loading des images avatar
        const avatars = document.querySelectorAll('img[src*="googleusercontent"]');
        avatars.forEach(img => {
            img.loading = 'lazy';
        });
    }
});
</script>
@endpush
@endsection