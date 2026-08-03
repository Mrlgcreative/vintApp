@extends('layouts.admin')

@section('title', 'Traçage GPS des commandes')

@section('page-title', 'Traçage GPS des commandes')

@section('page-actions')
<div class="flex flex-col gap-2 sm:flex-row sm:items-center">
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
        <i class="fas fa-arrow-left"></i>Toutes les commandes
    </a>
    <button onclick="refreshTracking()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
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
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <i class="fas fa-map-marker-alt mb-4 text-6xl text-slate-300 dark:text-slate-600"></i>
            <h3 class="mb-2 text-xl font-bold text-slate-900 dark:text-white">Aucune commande tracée</h3>
            <p class="mb-6 text-slate-500 dark:text-slate-400">Les commandes avec traçage GPS apparaîtront ici.</p>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                <i class="fas fa-shopping-cart"></i>Voir toutes les commandes
            </a>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
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
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($trackedOrders as $order)
                            @php
                                $tracking = $order->latestTracking;
                            @endphp
                            <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30">
                                <td class="whitespace-nowrap px-4 py-3 align-middle">
                                    <div class="flex items-center">
                                        <i class="fas fa-hashtag mr-2 text-slate-400"></i>
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
                                            <div class="text-slate-900 dark:text-white">{{ number_format($tracking->latitude, 6) }}, {{ number_format($tracking->longitude, 6) }}</div>
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
                                        <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:bg-sky-700">
                                            <i class="fas fa-file-invoice"></i>Facture
                                        </a>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-600 px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:bg-slate-700">
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

        <!-- Statistiques -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl bg-gradient-to-br from-sky-50 to-sky-100 p-5 sm:p-6 dark:from-sky-900/20 dark:to-sky-900/10">
                <div class="mb-2 flex items-center justify-between">
                    <i class="fas fa-boxes text-3xl text-sky-600"></i>
                    <span class="text-2xl font-bold text-sky-900 dark:text-sky-300">{{ $trackedOrders->count() }}</span>
                </div>
                <p class="text-sm font-medium text-sky-700 dark:text-sky-400">Total tracées</p>
            </div>

            <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 p-5 sm:p-6 dark:from-emerald-900/20 dark:to-emerald-900/10">
                <div class="mb-2 flex items-center justify-between">
                    <i class="fas fa-shipping-fast text-3xl text-emerald-600"></i>
                    <span class="text-2xl font-bold text-emerald-900 dark:text-emerald-300">{{ $trackedOrders->where('latestTracking.status', 'in_transit')->count() }}</span>
                </div>
                <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">En transit</p>
            </div>

            <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100 p-5 sm:p-6 dark:from-amber-900/20 dark:to-amber-900/10">
                <div class="mb-2 flex items-center justify-between">
                    <i class="fas fa-truck text-3xl text-amber-600"></i>
                    <span class="text-2xl font-bold text-amber-900 dark:text-amber-300">{{ $trackedOrders->where('latestTracking.status', 'out_for_delivery')->count() }}</span>
                </div>
                <p class="text-sm font-medium text-amber-700 dark:text-amber-400">En livraison</p>
            </div>

            <div class="rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 p-5 sm:p-6 dark:from-primary-900/20 dark:to-primary-900/10">
                <div class="mb-2 flex items-center justify-between">
                    <i class="fas fa-check-circle text-3xl text-primary-600"></i>
                    <span class="text-2xl font-bold text-primary-900 dark:text-primary-300">{{ $trackedOrders->where('latestTracking.status', 'delivered')->count() }}</span>
                </div>
                <p class="text-sm font-medium text-primary-700 dark:text-primary-400">Livrées</p>
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
