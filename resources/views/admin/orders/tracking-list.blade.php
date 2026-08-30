@extends('layouts.admin')

@section('title', 'Traçage GPS des commandes')
@section('page-title', 'Traçage GPS des commandes')
@section('page-subtitle', 'Suivi en temps réel des commandes tracées par GPS')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
        <i class="fas fa-arrow-left"></i>
        <span class="hidden sm:inline">Toutes les commandes</span>
        <span class="sm:hidden">Retour</span>
    </a>
    <button onclick="refreshTracking()" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
        <i class="fas fa-sync-alt"></i>Actualiser
    </button>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6">
    @php
        $trackedOrders = \App\Models\Order::whereHas('trackings')->with(['buyer', 'seller', 'latestTracking'])->latest()->get();
    @endphp

    @if($trackedOrders->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">
                <i class="fas fa-map-marker-alt text-4xl text-slate-400"></i>
            </div>
            <h3 class="mb-2 text-xl font-bold text-slate-900 dark:text-white">Aucune commande tracée</h3>
            <p class="mb-6 text-slate-500 dark:text-slate-400">Les commandes avec traçage GPS apparaîtront ici.</p>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                <i class="fas fa-shopping-cart"></i>Voir toutes les commandes
            </a>
        </div>
    @else
        {{-- Statistiques --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
            <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Total tracées</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $trackedOrders->count() }}</p>
                <div class="absolute right-4 top-4">
                    <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                        <i class="fas fa-boxes text-[10px] text-sky-500"></i>
                        Tracées
                    </span>
                </div>
                <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                    <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                        <i class="fas fa-map-location-dot text-xs text-sky-500"></i>
                        Commandes avec GPS
                    </div>
                    <div class="text-xs text-slate-400">Suivi actif</div>
                </div>
            </div>

            <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">En transit</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $trackedOrders->where('latestTracking.status', 'in_transit')->count() }}</p>
                <div class="absolute right-4 top-4">
                    <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <i class="fas fa-shipping-fast text-[10px]"></i>
                        Transit
                    </span>
                </div>
                <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                    <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                        <i class="fas fa-shipping-fast text-xs text-emerald-500"></i>
                        En cours de transport
                    </div>
                    <div class="text-xs text-slate-400">En route</div>
                </div>
            </div>

            <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">En livraison</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $trackedOrders->where('latestTracking.status', 'out_for_delivery')->count() }}</p>
                <div class="absolute right-4 top-4">
                    <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                        <i class="fas fa-truck text-[10px]"></i>
                        Livraison
                    </span>
                </div>
                <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                    <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                        <i class="fas fa-truck text-xs text-amber-500"></i>
                        Sur site client
                    </div>
                    <div class="text-xs text-slate-400">Dernier kilomètre</div>
                </div>
            </div>

            <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Livrées</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $trackedOrders->where('latestTracking.status', 'delivered')->count() }}</p>
                <div class="absolute right-4 top-4">
                    <span class="inline-flex items-center gap-1 rounded-lg border border-primary-200 bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 dark:border-primary-500/30 dark:bg-primary-500/10 dark:text-primary-400">
                        <i class="fas fa-circle-check text-[10px]"></i>
                        Livrées
                    </span>
                </div>
                <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                    <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                        <i class="fas fa-circle-check text-xs text-primary-500"></i>
                        Livraison confirmée
                    </div>
                    <div class="text-xs text-slate-400">Terminées</div>
                </div>
            </div>
        </div>

        {{-- Liste --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="flex items-center gap-2 text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-map-location-dot text-primary-600"></i>
                    Commandes tracées
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        {{ $trackedOrders->count() }} total
                    </span>
                </h3>
                <span class="text-xs text-slate-500 dark:text-slate-400">Mise à jour automatique toutes les 30 s</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Commande</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Acheteur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vendeur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Position</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dernière mise à jour</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($trackedOrders as $order)
                            @php
                                $tracking = $order->latestTracking;
                            @endphp
                            <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/30">
                                <td class="whitespace-nowrap px-4 py-3 align-middle">
                                    <div class="flex items-center">
                                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300">
                                            <i class="fas fa-hashtag"></i>
                                        </div>
                                        <span class="font-semibold text-slate-900 dark:text-white">{{ $order->order_number }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 align-middle">
                                    <div class="text-sm">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $order->buyer->name }}</div>
                                        <div class="text-slate-500 dark:text-slate-400">{{ $order->buyer->email }}</div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 align-middle">
                                    <div class="text-sm">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $order->seller->name }}</div>
                                        <div class="text-slate-500 dark:text-slate-400">{{ $order->seller->email }}</div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 align-middle">
                                    @if($tracking)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ring-slate-500/10 {{ $tracking->status_badge_class }}">
                                            <i class="{{ $tracking->status_icon }} mr-1"></i>
                                            {{ $tracking->status_text }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">Non défini</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 align-middle">
                                    @if($tracking && $tracking->latitude && $tracking->longitude)
                                        <div class="text-sm">
                                            <div class="font-medium tabular-nums text-slate-900 dark:text-white">{{ number_format($tracking->latitude, 6) }}, {{ number_format($tracking->longitude, 6) }}</div>
                                            @if($tracking->address)
                                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ Str::limit($tracking->address, 30) }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">Position non définie</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 align-middle text-sm text-slate-500 dark:text-slate-400">
                                    @if($tracking)
                                        {{ $tracking->formatted_tracked_at }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 align-middle text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.orders.tracking', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:bg-primary-700">
                                            <i class="fas fa-map-marked-alt"></i>Carte
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                                            <i class="fas fa-file-invoice"></i>Facture
                                        </a>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                                            <i class="fas fa-eye"></i>Détails
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function refreshTracking() {
    location.reload();
}

// Auto-refresh toutes les 30 secondes
setInterval(refreshTracking, 30000);
</script>
@endpush
@endsection