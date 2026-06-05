@extends('app')
@section('title', 'Mes Ventes')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête avec gradient indigo -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-primary-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold flex items-center gap-3">
                            <i class="fas fa-store text-indigo-200"></i>
                            Mes Ventes
                        </h1>
                        <p class="text-indigo-100 mt-2">Gérez vos commandes et suivez vos ventes</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <span class="text-lg font-semibold">
                            {{ $orders->total() }} commande(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation rapide -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Navigation rapide</h2>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Accédez rapidement aux différentes sections</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('local-delivery.create') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg shadow-green-500/25 hover:from-green-600 hover:to-green-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300">
                            <i class="fas fa-plus mr-2"></i>
                            Proposer Livraison
                        </a>
                        <a href="{{ route('local-delivery.user', 'seller') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-orange-500/25 hover:from-orange-600 hover:to-orange-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300">
                            <i class="fas fa-shipping-fast mr-2"></i>
                            Mes Livraisons
                        </a>
                        <a href="{{ route('orders.index') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 border border-indigo-300 text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Mes Achats
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        @if($orders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- En attente (à payer) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-yellow-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-yellow-600 text-sm font-semibold uppercase tracking-wide mb-2">En attente</h3>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $orders->where('status', 'pending')->count() }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Paiement attendu</p>
                            </div>
                            <div class="bg-yellow-100 rounded-full p-3">
                                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payées (à expédier) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-blue-600 text-sm font-semibold uppercase tracking-wide mb-2">À expédier</h3>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $orders->where('status', 'confirmed')->count() }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Prêtes à envoyer</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-3">
                                <i class="fas fa-box text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expédiées -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-indigo-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-indigo-600 text-sm font-semibold uppercase tracking-wide mb-2">En transit</h3>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $orders->where('status', 'shipped')->count() }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">En livraison</p>
                            </div>
                            <div class="bg-indigo-100 rounded-full p-3">
                                <i class="fas fa-shipping-fast text-indigo-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Livrées/Terminées -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-green-600 text-sm font-semibold uppercase tracking-wide mb-2">Terminées</h3>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $orders->whereIn('status', ['delivered', 'completed'])->count() }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Paiement distribué</p>
                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filtres rapides -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mb-8">
            <div class="p-6">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('orders.my-sales') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 {{ !request('status') ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 hover:bg-gray-200 dark:bg-gray-700' }}">
                        <i class="fas fa-list"></i>
                        Toutes ({{ $orders->total() }})
                    </a>
                    <a href="{{ route('orders.my-sales', ['status' => 'pending']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 {{ request('status') === 'pending' ? 'bg-yellow-500 text-white shadow-lg' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }}">
                        <i class="fas fa-clock"></i>
                        En attente
                    </a>
                    <a href="{{ route('orders.my-sales', ['status' => 'confirmed']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 {{ request('status') === 'confirmed' ? 'bg-blue-500 text-white shadow-lg' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                        <i class="fas fa-box"></i>
                        À expédier
                    </a>
                    <a href="{{ route('orders.my-sales', ['status' => 'shipped']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 {{ request('status') === 'shipped' ? 'bg-indigo-500 text-white shadow-lg' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                        <i class="fas fa-shipping-fast"></i>
                        Expédiées
                    </a>
                    <a href="{{ route('orders.my-sales', ['status' => 'delivered,completed']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 {{ in_array(request('status'), ['delivered', 'completed']) ? 'bg-green-500 text-white shadow-lg' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                        <i class="fas fa-check-circle"></i>
                        Terminées
                    </a>
                </div>
            </div>
        </div>

        <!-- Liste des commandes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Commande</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Article</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Acheteur</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">#{{ $order->id }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->order_number }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Qté: {{ $order->quantity }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($order->item && $order->item->images && count($order->item->images) > 0)
                                                <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                                     class="w-12 h-12 rounded-lg object-cover shadow-sm"
                                                     alt="{{ $order->item->name }}"
                                                     loading="lazy">
                                            @else
                                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ Str::limit($order->item->name ?? 'Article supprimé', 30) }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->item->category->name ?? 'Catégorie inconnue' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($order->buyer && $order->buyer->avatar)
                                                <img src="{{ $order->buyer->avatar_url }}" 
                                                     class="w-9 h-9 rounded-full object-cover"
                                                     alt="Avatar"
                                                     loading="lazy">
                                            @else
                                                <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-content center text-indigo-600 font-semibold text-sm">
                                                    {{ $order->buyer->initial ?? '?' }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ $order->buyer->name ?? 'Utilisateur inconnu' }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    {{ $order->shipping_city ?? 'Ville non spécifiée' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($order->unit_price, 2) }} × {{ $order->quantity }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusConfig = [
                                                'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock'],
                                                'confirmed' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-check'],
                                                'shipped' => ['class' => 'bg-indigo-100 text-indigo-800', 'icon' => 'fa-shipping-fast'],
                                                'delivered' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-box-check'],
                                                'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle'],
                                                'cancelled' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle'],
                                            ];
                                            $config = $statusConfig[$order->status] ?? ['class' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100', 'icon' => 'fa-question'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium {{ $config['class'] }}">
                                            <i class="fas {{ $config['icon'] }}"></i>
                                            {{ $order->status_text }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900 dark:text-white">{{ $order->created_at->format('d/m/Y') }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Voir détails -->
                                            <a href="{{ route('orders.show', $order) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors duration-200"
                                               title="Voir détails">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>
                                            
                                            <!-- Proposer livraison locale (si confirmée et pas de livraison locale) -->
                                            @if(in_array($order->status, ['confirmed', 'shipped']) && !$order->localDelivery)
                                                <a href="{{ route('local-delivery.create', ['order_id' => $order->id]) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 hover:bg-emerald-200 transition-colors duration-200"
                                                   title="Proposer livraison locale">
                                                    <i class="fas fa-handshake text-sm"></i>
                                                </a>
                                            @endif

                                            <!-- Voir livraison locale existante -->
                                            @if($order->localDelivery)
                                                <a href="{{ route('local-delivery.show', $order->localDelivery) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-100 text-orange-600 hover:bg-orange-200 transition-colors duration-200"
                                                   title="Voir livraison locale">
                                                    <i class="fas fa-truck text-sm"></i>
                                                </a>
                                            @endif
                                            
                                            <!-- Expédier (si confirmée) -->
                                            @if($order->status === 'confirmed')
                                                <form method="POST" action="{{ route('orders.mark-shipped', $order) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            onclick="return confirm('Marquer cette commande comme expédiée ?')"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-colors duration-200"
                                                            title="Expédier">
                                                        <i class="fas fa-shipping-fast text-sm"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <!-- Marquer livrée (si expédiée) -->
                                            @if($order->status === 'shipped')
                                                <form method="POST" action="{{ route('orders.mark-delivered', $order) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            onclick="return confirm('Marquer cette commande comme livrée ?')"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 transition-colors duration-200"
                                                            title="Marquer livrée">
                                                        <i class="fas fa-check-circle text-sm"></i>
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
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 dark:bg-gray-900">
                        <div class="flex justify-center">
                            {{ $orders->links() }}
                        </div>
                    </div>
                @endif
            @else
                <!-- État vide -->
                <div class="text-center py-16">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full">
                            <i class="fas fa-store text-3xl text-gray-400"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Aucune vente pour le moment</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">Vous n'avez pas encore reçu de commandes pour vos articles.</p>
                    <a href="{{ route('items.create') }}" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-primary-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-primary-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus"></i>
                        Vendre un article
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 