@extends('layouts.admin')

@section('title', 'Gestion des commandes')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des commandes</h1>
</div>

<!-- Filtres -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
    <div class="p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Statut</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Expédié</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Livré</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Date début</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Date fin</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Recherche</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" id="search" name="search" 
                       placeholder="ID commande, utilisateur..." value="{{ request('search') }}">
            </div>
            
            <div class="md:col-span-2 flex items-end gap-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 transition-all duration-200 font-medium">
                    <i class="fas fa-search mr-2"></i> Filtrer
                </button>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 transition-all duration-200 font-medium">
                    <i class="fas fa-times mr-2"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Liste des commandes -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            <i class="fas fa-shopping-cart text-primary-600 mr-2"></i>
            Liste des commandes 
            @if(isset($orders))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 ml-2">
                    {{ $orders->total() ?? 0 }} total
                </span>
            @endif
        </h3>
    </div>
    <div class="p-0">
        @if(isset($orders) && $orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acheteur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendeur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Article</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">#{{ $order->id }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->buyer)
                                    <div class="flex items-center">
                                        @if($order->buyer->avatar)
                                            <img src="{{ $order->buyer->avatar_url }}" class="w-8 h-8 rounded-full mr-3" alt="Avatar">
                                        @else
                                            <div class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center font-semibold text-xs mr-3">
                                                {{ $order->buyer->initial }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->buyer->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->buyer->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Utilisateur supprimé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->seller)
                                    <div class="flex items-center">
                                        @if($order->seller->avatar)
                                            <img src="{{ $order->seller->avatar_url }}" class="w-8 h-8 rounded-full mr-3" alt="Avatar">
                                        @else
                                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-semibold text-xs mr-3">
                                                {{ $order->seller->initial }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->seller->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->seller->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Utilisateur supprimé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->item)
                                    <div class="flex items-center">
                                        @if($order->item->images && count($order->item->images) > 0)
                                            <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                                 class="w-10 h-10 rounded-lg mr-3 object-cover" alt="Article">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($order->item->title, 30) }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->item->brand->name ?? 'Sans marque' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Article supprimé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($order->total_amount ?? 0, 2) }} {{ $order->currency ?? 'USD' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-blue-100 text-blue-800',
                                        'shipped' => 'bg-indigo-100 text-indigo-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'En attente',
                                        'confirmed' => 'Confirmé',
                                        'shipped' => 'Expédié',
                                        'delivered' => 'Livré',
                                        'cancelled' => 'Annulé'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div>{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewOrder({{ $order->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-150"
                                            title="Voir détails">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    @if($order->status === 'pending')
                                        <button onclick="confirmOrder({{ $order->id }})" 
                                                class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-150"
                                                title="Confirmer">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                        <button onclick="cancelOrder({{ $order->id }})" 
                                                class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-150"
                                                title="Annuler">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        @else
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
                    <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
                </div>
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune commande trouvée</h5>
                <p class="text-gray-500 dark:text-gray-400">Il n'y a aucune commande correspondant à vos critères.</p>
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    @if(isset($orders) && $orders->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
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