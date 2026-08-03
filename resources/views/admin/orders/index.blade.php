@extends('layouts.admin')

@section('title', 'Gestion des commandes')
@section('page-title', 'Gestion des commandes')

@section('content')
<!-- Filtres -->
<div class="mb-8 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="p-5 sm:p-6">
        <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4 lg:grid-cols-6">
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Statut</label>
                <select class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Expédié</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Livré</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Date début</label>
                <input type="date" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            
            <div>
                <label for="date_to" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Date fin</label>
                <input type="date" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            
            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Recherche</label>
                <input type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="search" name="search" 
                       placeholder="ID commande, utilisateur..." value="{{ request('search') }}">
            </div>
            
            <div class="flex flex-col items-end gap-3 md:col-span-2 sm:flex-row sm:items-end">
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-search"></i> Filtrer
                </button>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Liste des commandes -->
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="border-b border-slate-100 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-900">
        <h3 class="font-semibold text-slate-900 dark:text-white">
            <i class="fas fa-shopping-cart mr-2 text-primary-600"></i>
            Liste des commandes 
            @if(isset($orders))
                <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800 dark:bg-slate-800 dark:text-slate-100">
                    {{ $orders->total() ?? 0 }} total
                </span>
            @endif
        </h3>
    </div>
    <div>
        @if(isset($orders) && $orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Acheteur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vendeur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Article</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Montant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($orders as $order)
                        <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30">
                            <td class="whitespace-nowrap px-4 py-3 align-middle">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">#{{ $order->id }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle">
                                @if($order->buyer)
                                    <div class="flex items-center">
                                        @if($order->buyer->avatar)
                                            <img src="{{ $order->buyer->avatar_url }}" class="mr-3 h-8 w-8 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600" alt="Avatar">
                                        @else
                                            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400 text-xs font-semibold text-white">
                                                {{ $order->buyer->initial }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $order->buyer->name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->buyer->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">Utilisateur supprimé</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle">
                                @if($order->seller)
                                    <div class="flex items-center">
                                        @if($order->seller->avatar)
                                            <img src="{{ $order->seller->avatar_url }}" class="mr-3 h-8 w-8 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600" alt="Avatar">
                                        @else
                                            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 text-xs font-semibold text-white">
                                                {{ $order->seller->initial }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $order->seller->name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->seller->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">Utilisateur supprimé</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle">
                                @if($order->item)
                                    <div class="flex items-center">
                                        @if($order->item->images && count($order->item->images) > 0)
                                            <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                                 class="mr-3 h-10 w-10 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-600" alt="Article">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ Str::limit($order->item->title, 30) }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->item->brand->name ?? 'Sans marque' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">Article supprimé</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($order->total_amount ?? 0, 2) }} {{ $order->currency ?? 'USD' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
                                        'confirmed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300',
                                        'shipped' => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-900/30 dark:text-violet-300',
                                        'delivered' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'En attente',
                                        'confirmed' => 'Confirmé',
                                        'shipped' => 'Expédié',
                                        'delivered' => 'Livré',
                                        'cancelled' => 'Annulé'
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle text-sm text-slate-500 dark:text-slate-400">
                                <div>{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-slate-400">{{ $order->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 align-middle text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewOrder({{ $order->id }})" 
                                            class="inline-flex items-center rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-medium text-sky-700 transition-colors hover:bg-sky-100 dark:bg-sky-900/30 dark:text-sky-300 dark:hover:bg-sky-900/50"
                                            title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($order->status === 'pending')
                                        <button onclick="confirmOrder({{ $order->id }})" 
                                                class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-medium text-emerald-700 transition-colors hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50"
                                                title="Confirmer">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="cancelOrder({{ $order->id }})" 
                                                class="inline-flex items-center rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-700 transition-colors hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50"
                                                title="Annuler">
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
            
        @else
            <div class="py-12 text-center">
                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                    <i class="fas fa-shopping-cart text-3xl text-slate-400"></i>
                </div>
                <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucune commande trouvée</h5>
                <p class="text-slate-500 dark:text-slate-400">Il n'y a aucune commande correspondant à vos critères.</p>
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    @if(isset($orders) && $orders->hasPages())
        <div class="border-t border-slate-100 px-5 py-4 dark:border-slate-700">
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
