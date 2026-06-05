@extends('layouts.admin')

@section('title', 'Détails de la commande #' . $order->id)
@section('page-title', 'Détails de la commande #' . $order->id)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700">
        <i class="fas fa-arrow-left mr-2"></i> Retour aux commandes
    </a>
    
    <!-- 🆕 Boutons d'actions rapides -->
    <div class="flex gap-3">
        <a href="{{ route('admin.orders.tracking', $order->id) }}" 
            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-md hover:shadow-lg">
            <i class="fas fa-map-marked-alt mr-2"></i> Traçage GPS
        </a>
        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md hover:shadow-lg">
            <i class="fas fa-file-invoice mr-2"></i> Facture
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne principale -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Informations de la commande -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-shopping-cart text-primary-600 mr-2"></i>
                    Commande #{{ $order->id }}
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Date de commande</label>
                        <p class="text-base text-gray-900 dark:text-white">
                            <i class="far fa-calendar text-gray-400 mr-2"></i>
                            {{ $order->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Statut</label>
                        <p>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-primary-100 text-primary-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'En attente',
                                    'confirmed' => 'Confirmée',
                                    'shipped' => 'Expédiée',
                                    'delivered' => 'Livrée',
                                    'cancelled' => 'Annulée',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Quantité</label>
                        <p class="text-base text-gray-900 dark:text-white">
                            <i class="fas fa-box text-gray-400 mr-2"></i>
                            {{ $order->quantity }} article(s)
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Montant total</label>
                        <p class="text-xl font-bold text-primary-600">
                            {{ number_format($order->total_price, 2) }} {{ $order->currency ?? 'USD' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Article commandé -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-tag text-primary-600 mr-2"></i>
                    Article commandé
                </h3>
            </div>
            <div class="p-6">
                @if($order->item)
                <div class="flex items-start gap-4">
                    @if(!empty($order->item->images) && is_array($order->item->images))
                    <img src="{{ Storage::url($order->item->images[0]) }}" 
                         alt="{{ $order->item->name }}" 
                         class="w-24 h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                    @else
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 dark:text-white text-lg mb-2">{{ $order->item->name }}</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2">{{ $order->item->description }}</p>
                        <div class="flex flex-wrap gap-3">
                            @if($order->item->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-folder mr-1"></i> {{ $order->item->category->name }}
                            </span>
                            @endif
                            @if($order->item->brand)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                <i class="fas fa-copyright mr-1"></i> {{ $order->item->brand->name }}
                            </span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-dollar-sign mr-1"></i> {{ number_format($order->item->price, 2) }} {{ $order->item->currency ?? 'USD' }}
                            </span>
                        </div>
                    </div>
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Article non disponible
                </p>
                @endif
            </div>
        </div>

        <!-- Transaction associée -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-receipt text-primary-600 mr-2"></i>
                    Informations de paiement
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Numéro de commande</label>
                        <p class="text-base text-gray-900 dark:text-white font-mono">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Montant payé</label>
                        <p class="text-base text-gray-900 dark:text-white font-semibold">
                            {{ number_format($order->total_amount, 2) }} {{ $order->currency ?? 'USD' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Statut du paiement</label>
                        <p>
                            @if($order->paid_at)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Payé
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> En attente
                            </span>
                            @endif
                        </p>
                    </div>
                    @if($order->paid_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Date de paiement</label>
                        <p class="text-base text-gray-900 dark:text-white">
                            <i class="far fa-calendar text-gray-400 mr-2"></i>
                            {{ $order->paid_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Colonne latérale -->
    <div class="space-y-6">
        <!-- Acheteur -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-blue-50">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Acheteur
                </h3>
            </div>
            <div class="p-6">
                @if($order->buyer)
                <div class="text-center mb-4">
                    @if($order->buyer->profile_image)
                    <img src="{{ Storage::url($order->buyer->profile_image) }}" 
                         alt="{{ $order->buyer->name }}" 
                         class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-blue-200">
                    @else
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user text-blue-600 text-2xl"></i>
                    </div>
                    @endif
                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $order->buyer->name }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->buyer->email }}</p>
                </div>
                <div class="space-y-2 pt-4 border-t border-gray-100">
                    @if($order->buyer->phone)
                    <p class="text-sm">
                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                        {{ $order->buyer->phone }}
                    </p>
                    @endif
                    @if($order->buyer->city)
                    <p class="text-sm">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                        {{ $order->buyer->city }}
                    </p>
                    @endif
                    <a href="{{ route('admin.users.show', $order->buyer_id) }}" 
                       class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 mt-2">
                        <i class="fas fa-external-link-alt mr-1"></i> Voir le profil
                    </a>
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-center">Utilisateur non disponible</p>
                @endif
            </div>
        </div>

        <!-- Vendeur -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-green-50">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-store text-green-600 mr-2"></i>
                    Vendeur
                </h3>
            </div>
            <div class="p-6">
                @if($order->seller)
                <div class="text-center mb-4">
                    @if($order->seller->profile_image)
                    <img src="{{ Storage::url($order->seller->profile_image) }}" 
                         alt="{{ $order->seller->name }}" 
                         class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-green-200">
                    @else
                    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user text-green-600 text-2xl"></i>
                    </div>
                    @endif
                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $order->seller->name }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->seller->email }}</p>
                </div>
                <div class="space-y-2 pt-4 border-t border-gray-100">
                    @if($order->seller->phone)
                    <p class="text-sm">
                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                        {{ $order->seller->phone }}
                    </p>
                    @endif
                    @if($order->seller->city)
                    <p class="text-sm">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                        {{ $order->seller->city }}
                    </p>
                    @endif
                    <a href="{{ route('admin.users.show', $order->seller_id) }}" 
                       class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 mt-2">
                        <i class="fas fa-external-link-alt mr-1"></i> Voir le profil
                    </a>
                </div>
                @else
                <p class="text-gray-500 dark:text-gray-400 text-center">Utilisateur non disponible</p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-cog text-primary-600 mr-2"></i>
                    Actions
                </h3>
            </div>
            <div class="p-6 space-y-3">
                <button onclick="window.print()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-print mr-2"></i> Imprimer
                </button>
                <a href="{{ route('admin.orders.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-list mr-2"></i> Toutes les commandes
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
@media print {
    .sidebar, nav, button, a[href] {
        display: none !important;
    }
    .lg\:col-span-2 {
        grid-column: span 3 / span 3 !important;
    }
}
</style>
