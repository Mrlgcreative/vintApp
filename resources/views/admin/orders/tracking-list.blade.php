@extends('layouts.admin')

@section('title', 'Traçage GPS des commandes')

@section('page-title', 'Traçage GPS des commandes')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Toutes les commandes
    </a>
    <button onclick="refreshTracking()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-sync-alt mr-2"></i>Actualiser
    </button>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6">
    @php
        $trackedOrders = \App\Models\Order::whereHas('trackings')->with(['buyer', 'seller', 'latestTracking'])->latest()->get();
    @endphp

    @if($trackedOrders->isEmpty())
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <i class="fas fa-map-marker-alt text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune commande tracée</h3>
            <p class="text-gray-500 mb-6">Les commandes avec traçage GPS apparaîtront ici.</p>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-shopping-cart mr-2"></i>Voir toutes les commandes
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-primary-500 to-primary-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Commande</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Acheteur</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Vendeur</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Position</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Dernière mise à jour</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($trackedOrders as $order)
                            @php
                                $tracking = $order->latestTracking;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-hashtag text-gray-400 mr-2"></i>
                                        <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $order->buyer->name }}</div>
                                        <div class="text-gray-500">{{ $order->buyer->email }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $order->seller->name }}</div>
                                        <div class="text-gray-500">{{ $order->seller->email }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($tracking)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $tracking->status_badge_class }}">
                                            <i class="{{ $tracking->status_icon }} mr-1"></i>
                                            {{ $tracking->status_text }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Non défini</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($tracking && $tracking->latitude && $tracking->longitude)
                                        <div class="text-sm">
                                            <div class="text-gray-900">{{ number_format($tracking->latitude, 6) }}, {{ number_format($tracking->longitude, 6) }}</div>
                                            @if($tracking->address)
                                                <div class="text-gray-500 text-xs">{{ Str::limit($tracking->address, 30) }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Position non définie</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($tracking)
                                        {{ $tracking->formatted_tracked_at }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.orders.tracking', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                            <i class="fas fa-map-marked-alt mr-1.5"></i>Carte
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-file-invoice mr-1.5"></i>Facture
                                        </a>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                            <i class="fas fa-eye mr-1.5"></i>Détails
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-boxes text-3xl text-blue-600"></i>
                    <span class="text-2xl font-bold text-blue-900">{{ $trackedOrders->count() }}</span>
                </div>
                <p class="text-sm text-blue-700 font-medium">Total tracées</p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-shipping-fast text-3xl text-green-600"></i>
                    <span class="text-2xl font-bold text-green-900">{{ $trackedOrders->where('latestTracking.status', 'in_transit')->count() }}</span>
                </div>
                <p class="text-sm text-green-700 font-medium">En transit</p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-truck text-3xl text-orange-600"></i>
                    <span class="text-2xl font-bold text-orange-900">{{ $trackedOrders->where('latestTracking.status', 'out_for_delivery')->count() }}</span>
                </div>
                <p class="text-sm text-orange-700 font-medium">En livraison</p>
            </div>

            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-check-circle text-3xl text-primary-600"></i>
                    <span class="text-2xl font-bold text-primary-900">{{ $trackedOrders->where('latestTracking.status', 'delivered')->count() }}</span>
                </div>
                <p class="text-sm text-primary-700 font-medium">Livrées</p>
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
