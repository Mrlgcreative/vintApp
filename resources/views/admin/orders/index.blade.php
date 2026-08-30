@extends('layouts.admin')

@section('title', 'Gestion des commandes')
@section('page-title', 'Gestion des commandes')
@section('page-subtitle', 'Suivi des commandes de la plateforme')

@push('styles')
<style>
    @keyframes pulse-dot { 0%,100%{ box-shadow:0 0 0 0 rgba(16,185,129,.55) } 50%{ box-shadow:0 0 0 6px rgba(16,185,129,0) } }
    .pulse-dot{ animation:pulse-dot 2s infinite }
</style>
@endpush

@section('content')
{{-- Statistiques --}}
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total commandes</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($orders->total()) }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                <i class="fas fa-shopping-cart text-[10px] text-sky-500"></i>
                Commandes
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-cart-plus text-xs text-sky-500"></i>
                Commandes reçues
            </div>
            <div class="text-xs text-slate-400">Sur toute la plateforme</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($orders->where('status', 'pending')->count()) }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                <i class="fas fa-clock text-[10px]"></i>
                À traiter
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-hourglass-half text-xs text-amber-500"></i>
                En attente de traitement
            </div>
            <div class="text-xs text-slate-400">À confirmer</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Livrées</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($orders->where('status', 'delivered')->count()) }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                <i class="fas fa-truck text-[10px]"></i>
                Terminées
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-circle-check text-xs text-emerald-500"></i>
                Commandes terminées
            </div>
            <div class="text-xs text-slate-400">Livraison confirmée</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Annulées</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($orders->where('status', 'cancelled')->count()) }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                <i class="fas fa-ban text-[10px]"></i>
                Annulées
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-ban text-xs text-red-500"></i>
                Commandes annulées
            </div>
            <div class="text-xs text-slate-400">Sans suite</div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="mb-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="p-5 sm:p-6">
        <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4 lg:grid-cols-6">
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Statut</label>
                <select class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="status" name="status">
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
                <input type="date" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            
            <div>
                <label for="date_to" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Date fin</label>
                <input type="date" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            
            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Recherche</label>
                <input type="text" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="search" name="search" 
                       placeholder="ID commande, utilisateur..." value="{{ request('search') }}">
            </div>
            
            <div class="flex flex-col items-end gap-3 md:col-span-2 sm:flex-row sm:items-end">
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-search"></i> Filtrer
                </button>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Liste des commandes -->
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-4 border-b border-slate-100 dark:border-slate-700">
        <h3 class="flex items-center gap-2 text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
            <i class="fas fa-shopping-cart text-primary-600"></i>
            Liste des commandes
            @if(isset($orders))
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ $orders->total() ?? 0 }} total
                </span>
            @endif
        </h3>
        @if(isset($orders))
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Page {{ $orders->currentPage() }}/{{ $orders->lastPage() }}
            </span>
        @endif
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
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($orders as $order)
                        <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/30">
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
                                                 class="mr-3 h-10 w-10 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600" alt="Article">
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
                                <span class="text-sm font-semibold tabular-nums text-slate-900 dark:text-white">{{ number_format($order->total_amount ?? 0, 2) }} <span class="text-xs text-slate-400">{{ $order->currency ?? 'USD' }}</span></span>
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

            {{-- Vue cartes Mobile --}}
            <div class="divide-y divide-slate-100 lg:hidden dark:divide-slate-700">
                @foreach($orders as $order)
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
                    <div class="p-4 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        {{-- En-tête : acheteur + statut --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                @if($order->buyer)
                                    @if($order->buyer->avatar)
                                        <img src="{{ $order->buyer->avatar_url }}" class="h-10 w-10 flex-shrink-0 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600" alt="Avatar">
                                    @else
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400 text-xs font-semibold text-white">
                                            {{ $order->buyer->initial }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $order->buyer->name }}</div>
                                        <div class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $order->buyer->email }}</div>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">Utilisateur supprimé</span>
                                @endif
                            </div>
                            <div class="flex flex-shrink-0 flex-col items-end gap-1.5">
                                <span class="text-xs font-medium text-slate-400">#{{ $order->id }}</span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </div>
                        </div>

                        {{-- Article --}}
                        <div class="mt-3 flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-900/30">
                            @if($order->item)
                                @if($order->item->images && count($order->item->images) > 0)
                                    <img src="{{ asset('storage/' . $order->item->images[0]) }}"
                                         class="h-12 w-12 flex-shrink-0 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600" alt="Article">
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ Str::limit($order->item->title, 40) }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->item->brand->name ?? 'Sans marque' }}</div>
                                </div>
                            @else
                                <span class="text-sm text-slate-400">Article supprimé</span>
                            @endif
                            <div class="text-sm font-semibold tabular-nums text-slate-900 dark:text-white">
                                {{ number_format($order->total_amount ?? 0, 2) }}
                                <span class="text-xs text-slate-400">{{ $order->currency ?? 'USD' }}</span>
                            </div>
                        </div>

                        {{-- Pied : vendeur + date + actions --}}
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <div class="min-w-0 text-xs text-slate-500 dark:text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-store text-[10px]"></i>
                                    <span class="truncate">{{ $order->seller?->name ?? 'Vendeur supprimé' }}</span>
                                </div>
                                <div class="mt-0.5 flex items-center gap-1">
                                    <i class="fas fa-clock text-[10px]"></i>
                                    {{ $order->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-2">
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
                        </div>
                    </div>
                @endforeach
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

@push('scripts')
<script>
function viewOrder(orderId) {
    window.location.href = `/admin/orders/${orderId}`;
}

function confirmOrder(orderId) {
    if (confirm('Confirmer cette commande ?')) {
        console.log('Confirming order:', orderId);
    }
}

function cancelOrder(orderId) {
    if (confirm('Annuler cette commande ?')) {
        console.log('Cancelling order:', orderId);
    }
}
</script>
@endpush
@endsection
