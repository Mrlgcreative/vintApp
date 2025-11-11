@extends('app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors duration-200">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Mes Commandes
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-blue-600">{{ $order->order_number }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Contenu principal -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-2xl shadow-xl shadow-blue-600/10 border border-gray-100/50 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-shopping-cart text-white text-xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold">Commande {{ $order->order_number }}</h1>
                                    <p class="text-blue-100 text-sm">Détails complets de votre commande</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ $order->status_badge_class === 'bg-success' ? 'bg-emerald-100 text-emerald-800' : ($order->status_badge_class === 'bg-warning' ? 'bg-yellow-100 text-yellow-800' : ($order->status_badge_class === 'bg-info' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }} border border-white/20">
                                {{ $order->status_text }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-8">
                        <!-- Informations de l'article -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex justify-center">
                                @if($order->item->images && count($order->item->images) > 0)
                                    <div class="w-full max-w-sm bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                                        <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                             class="w-full h-64 object-cover" 
                                             alt="{{ $order->item->name }}"
                                             loading="lazy">
                                    </div>
                                @else
                                    <div class="w-full max-w-sm h-64 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-200">
                                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $order->item->name }}</h2>
                                    <p class="text-gray-600 leading-relaxed">{{ $order->item->description }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                                        <p class="text-xs text-blue-600 font-medium mb-1">Prix unitaire</p>
                                        <p class="text-xl font-bold text-blue-800">{{ $order->formatted_unit_price }}</p>
                                    </div>
                                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                                        <p class="text-xs text-emerald-600 font-medium mb-1">Quantité</p>
                                        <p class="text-xl font-bold text-emerald-800">{{ $order->quantity }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ $order->item->category->name }}
                                    </span>
                                    @if($order->item->brand)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ $order->item->brand->name }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                        {{ $order->item->condition === 'excellent' ? 'bg-emerald-100 text-emerald-800' : 
                                           ($order->item->condition === 'good' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($order->item->condition === 'fair' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->item->condition)) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-purple-50 rounded-xl border border-purple-100">
                                        <p class="text-xs text-purple-600 font-medium mb-1">Vendeur</p>
                                        <p class="font-bold text-purple-900">{{ $order->item->user->name }}</p>
                                    </div>
                                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                                        <p class="text-xs text-indigo-600 font-medium mb-1">Acheteur</p>
                                        <p class="font-bold text-indigo-900">{{ $order->buyer->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Separator -->
                        <div class="border-t border-gray-200"></div>

                        <!-- Informations de livraison et paiement -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Adresse de livraison -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                                    Adresse de livraison
                                </h3>
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                                    @if($order->deliveryAddress)
                                        <div class="space-y-4">
                                            <div class="flex items-start">
                                                <i class="fas fa-user text-purple-500 mt-1 mr-3"></i>
                                                <div>
                                                    <p class="font-semibold text-gray-900">Destinataire</p>
                                                    <p class="text-gray-700">{{ $order->deliveryAddress->full_name }}</p>
                                                </div>
                                            </div>

                                            @if($order->deliveryAddress->email)
                                                <div class="flex items-start">
                                                    <i class="fas fa-envelope text-blue-500 mt-1 mr-3"></i>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">Email</p>
                                                        <a href="mailto:{{ $order->deliveryAddress->email }}" 
                                                           class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                                            {{ $order->deliveryAddress->email }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="flex items-start">
                                                <i class="fas fa-phone text-emerald-500 mt-1 mr-3"></i>
                                                <div>
                                                    <p class="font-semibold text-gray-900">Téléphone</p>
                                                    <a href="tel:{{ $order->deliveryAddress->phone }}" 
                                                       class="text-emerald-600 hover:text-emerald-800 transition-colors duration-200">
                                                        {{ $order->deliveryAddress->phone }}
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="flex items-start">
                                                <i class="fas fa-city text-indigo-500 mt-1 mr-3"></i>
                                                <div>
                                                    <p class="font-semibold text-gray-900">Ville / Commune</p>
                                                    <p class="text-gray-700">{{ $order->deliveryAddress->city }}, {{ $order->deliveryAddress->commune }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-start">
                                                <i class="fas fa-home text-blue-500 mt-1 mr-3"></i>
                                                <div>
                                                    <p class="font-semibold text-gray-900">Adresse complète</p>
                                                    <p class="text-gray-600">{{ $order->deliveryAddress->address }}</p>
                                                </div>
                                            </div>

                                            @if($order->deliveryAddress->notes)
                                                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    <div class="flex items-start">
                                                        <i class="fas fa-sticky-note text-yellow-500 mt-1 mr-2"></i>
                                                        <div>
                                                            <p class="text-sm font-medium text-yellow-800">Note:</p>
                                                            <p class="text-sm text-yellow-700">{{ $order->deliveryAddress->notes }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="space-y-4">
                                            @if($order->shipping_city && $order->shipping_city !== 'À définir')
                                                <div class="flex items-start">
                                                    <i class="fas fa-city text-indigo-500 mt-1 mr-3"></i>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">Ville</p>
                                                        <p class="text-gray-700">{{ $order->shipping_city }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($order->shipping_phone)
                                                <div class="flex items-start">
                                                    <i class="fas fa-phone text-emerald-500 mt-1 mr-3"></i>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">Téléphone</p>
                                                        <a href="tel:{{ $order->shipping_phone }}" 
                                                           class="text-emerald-600 hover:text-emerald-800 transition-colors duration-200">
                                                            {{ $order->shipping_phone }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($order->shipping_address && $order->shipping_address !== 'À définir')
                                                <div class="flex items-start">
                                                    <i class="fas fa-home text-blue-500 mt-1 mr-3"></i>
                                                    <div>
                                                        <p class="font-semibold text-gray-900">Adresse complète</p>
                                                        <p class="text-gray-600">{{ $order->shipping_address }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                            @if((!$order->shipping_city || $order->shipping_city === 'À définir') && 
                                                (!$order->shipping_address || $order->shipping_address === 'À définir') &&
                                                !$order->deliveryAddress)
                                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                                    <div class="flex">
                                                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-3"></i>
                                                        <div>
                                                            <h4 class="text-sm font-semibold text-yellow-800">Adresse non définie</h4>
                                                            <p class="text-sm text-yellow-700 mt-1">L'adresse de livraison n'a pas encore été définie pour cette commande.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Détails du paiement -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-money-bill text-emerald-500 mr-2"></i>
                                    Détails du paiement
                                </h3>
                                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Prix unitaire:</span>
                                            <span class="font-semibold text-gray-900">{{ $order->formatted_unit_price }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Quantité:</span>
                                            <span class="font-semibold text-gray-900">{{ $order->quantity }}</span>
                                        </div>
                                        <div class="border-t border-gray-200 pt-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-lg font-bold text-gray-900">Total:</span>
                                                <span class="text-lg font-bold text-blue-600">{{ $order->formatted_total_amount }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($order->notes)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>
                                    Notes
                                </h3>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                                    <p class="text-gray-800">{{ $order->notes }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Historique des statuts -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-history text-purple-500 mr-2"></i>
                                Historique
                            </h3>
                            <div class="relative">
                                <!-- Timeline line -->
                                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                                
                                <div class="space-y-6">
                                    <!-- Commande créée -->
                                    <div class="relative flex items-start">
                                        <div class="absolute left-4 w-4 h-4 bg-emerald-500 rounded-full border-4 border-white shadow-sm"></div>
                                        <div class="ml-12">
                                            <h4 class="text-sm font-semibold text-gray-900">Commande créée</h4>
                                            <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($order->paid_at)
                                        <div class="relative flex items-start">
                                            <div class="absolute left-4 w-4 h-4 bg-blue-500 rounded-full border-4 border-white shadow-sm"></div>
                                            <div class="ml-12">
                                                <h4 class="text-sm font-semibold text-gray-900">Paiement confirmé</h4>
                                                <p class="text-sm text-gray-600">{{ $order->paid_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($order->shipped_at)
                                        <div class="relative flex items-start">
                                            <div class="absolute left-4 w-4 h-4 bg-indigo-500 rounded-full border-4 border-white shadow-sm"></div>
                                            <div class="ml-12">
                                                <h4 class="text-sm font-semibold text-gray-900">Expédiée</h4>
                                                <p class="text-sm text-gray-600">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($order->delivered_at)
                                        <div class="relative flex items-start">
                                            <div class="absolute left-4 w-4 h-4 bg-emerald-500 rounded-full border-4 border-white shadow-sm"></div>
                                            <div class="ml-12">
                                                <h4 class="text-sm font-semibold text-gray-900">Livrée</h4>
                                                <p class="text-sm text-gray-600">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions principales -->
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('orders.index') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Retour aux commandes
                            </a>
                            
                            <div class="flex flex-col sm:flex-row gap-3">
                                {{-- Acheteur : Bouton payer si commande en attente (pending) --}}
                                @if($order->buyer_id === Auth::id() && $order->status === 'pending')
                                    <form method="POST" action="{{ route('orders.confirm-payment', $order) }}">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir confirmer le paiement de cette commande ?')"
                                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                                            <i class="fas fa-credit-card mr-2"></i>
                                            Confirmer le paiement
                                        </button>
                                    </form>
                                @endif
                                
                                {{-- Vendeur : Bouton expédier si commande confirmée (payée) --}}
                                @if($order->item->user_id === Auth::id() && $order->status === 'confirmed')
                                    <form method="POST" action="{{ route('orders.mark-shipped', $order) }}">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Marquer cette commande comme expédiée ?')"
                                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:from-blue-600 hover:to-blue-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                                            <i class="fas fa-shipping-fast mr-2"></i>
                                            📦 Expédier la commande
                                        </button>
                                    </form>
                                @endif
                                
                                {{-- Vendeur : Bouton marquer comme livrée si commande expédiée --}}
                                @if($order->item->user_id === Auth::id() && $order->status === 'shipped')
                                    <form method="POST" action="{{ route('orders.mark-delivered', $order) }}">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Marquer cette commande comme livrée ?')"
                                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            ✅ Marquer comme livrée
                                        </button>
                                    </form>
                                @endif
                                
                                {{-- Acheteur : Annuler si pas encore payé --}}
                                @if($order->buyer_id === Auth::id() && $order->status === 'pending')
                                    <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 hover:from-red-600 hover:to-red-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300">
                                            <i class="fas fa-times mr-2"></i>
                                            Annuler
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Actions rapides -->
                <div class="bg-white rounded-2xl shadow-xl shadow-gray-600/10 border border-gray-100/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-cogs text-gray-500 mr-2"></i>
                            Actions rapides
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('items.show', $order->item) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 border border-blue-300 text-blue-700 font-semibold rounded-xl hover:bg-blue-50 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            Voir l'article
                        </a>
                        
                        @if($order->item->user_id === Auth::id())
                            <a href="{{ route('items.edit', $order->item) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-3 border border-yellow-300 text-yellow-700 font-semibold rounded-xl hover:bg-yellow-50 hover:border-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier l'article
                            </a>
                        @endif
                        
                        <a href="{{ route('items.show', $order->item) }}#contact" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 border border-indigo-300 text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <i class="fas fa-envelope mr-2"></i>
                            Contacter {{ $order->buyer_id === Auth::id() ? 'le vendeur' : 'l\'acheteur' }}
                        </a>

                        {{-- Bouton demande de remboursement pour l'acheteur --}}
                        @if($order->buyer_id === Auth::id() && $order->confirmed_by_buyer_at && !$order->refunds()->exists())
                            <button onclick="openRefundModal()" 
                                   class="w-full inline-flex items-center justify-center px-4 py-3 border border-red-300 text-red-700 font-semibold rounded-xl hover:bg-red-50 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                <i class="fas fa-undo mr-2"></i>
                                Demander un remboursement
                            </button>
                        @endif

                        {{-- Affichage du statut de remboursement existant --}}
                        @if($order->refunds()->exists())
                            @php $refund = $order->refunds()->latest()->first(); @endphp
                            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                                <div class="flex">
                                    <i class="fas fa-undo text-orange-500 mt-0.5 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-semibold text-orange-800">Demande de remboursement</h4>
                                        <p class="text-sm text-orange-700 mt-1">
                                            Statut: {{ $refund->status_display }}
                                            @if($refund->status === 'negotiation')
                                                <br>Contre-offre: {{ $refund->formatted_counter_offer }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Messages d'état selon le statut de la commande --}}
                        @if($order->status === 'pending')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex">
                                    <i class="fas fa-clock text-yellow-500 mt-0.5 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-semibold text-yellow-800">En attente de paiement</h4>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            @if($order->buyer_id === Auth::id())
                                                Veuillez confirmer le paiement pour continuer
                                            @else
                                                L'acheteur n'a pas encore payé
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($order->status === 'confirmed')
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex">
                                    <i class="fas fa-box text-blue-500 mt-0.5 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-semibold text-blue-800">Paiement confirmé</h4>
                                        <p class="text-sm text-blue-700 mt-1">
                                            @if($order->item->user_id === Auth::id())
                                                Vous pouvez maintenant expédier la commande
                                            @else
                                                En attente d'expédition par le vendeur
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($order->status === 'shipped' && !$order->confirmed_by_buyer_at)
                            @if($order->buyer_id === Auth::id())
                                <button onclick="confirmDelivery()"
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    ✅ Commande Reçue
                                </button>
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                    <div class="flex">
                                        <i class="fas fa-truck text-blue-500 mt-0.5 mr-3"></i>
                                        <div>
                                            <p class="text-sm text-blue-700">Cliquez sur "Commande Reçue" une fois la livraison effectuée</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                    <div class="flex">
                                        <i class="fas fa-shipping-fast text-blue-500 mt-0.5 mr-3"></i>
                                        <div>
                                            <h4 class="text-sm font-semibold text-blue-800">Commande expédiée</h4>
                                            <p class="text-sm text-blue-700 mt-1">En attente de confirmation de réception par l'acheteur</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif($order->status === 'delivered' && !$order->confirmed_by_buyer_at)
                            @if($order->buyer_id === Auth::id())
                                <button onclick="confirmDelivery()"
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    ✅ Commande Reçue
                                </button>
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                    <div class="flex">
                                        <i class="fas fa-home text-blue-500 mt-0.5 mr-3"></i>
                                        <div>
                                            <p class="text-sm text-blue-700">Confirmez la réception pour finaliser la transaction</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                    <div class="flex">
                                        <i class="fas fa-check text-emerald-500 mt-0.5 mr-3"></i>
                                        <div>
                                            <h4 class="text-sm font-semibold text-emerald-800">Commande livrée</h4>
                                            <p class="text-sm text-emerald-700 mt-1">En attente de confirmation par l'acheteur</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Confirmation de réception effectuée --}}
                        @if($order->confirmed_by_buyer_at)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <div class="flex">
                                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-semibold text-emerald-800">✅ Réception confirmée</h4>
                                        <p class="text-sm text-emerald-700 mt-1">
                                            Le {{ $order->confirmed_by_buyer_at->format('d/m/Y à H:i') }}
                                        </p>
                                        @if($order->buyer_confirmation_note)
                                            <p class="text-sm text-emerald-600 mt-2 italic">"{{ $order->buyer_confirmation_note }}"</p>
                                        @endif
                                        <div class="border-t border-emerald-200 mt-3 pt-3">
                                            <p class="text-xs text-emerald-600 flex items-center">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                La distribution des fonds a été effectuée
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="bg-white rounded-2xl shadow-xl shadow-gray-600/10 border border-gray-100/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                            Informations
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Numéro:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Créée le:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $order->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Devise:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $order->currency }}</span>
                        </div>
                        @if($order->updated_at !== $order->created_at)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Modifiée le:</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $order->updated_at->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de demande de remboursement -->
<div id="refundModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-undo text-red-500 mr-2"></i>
                Demande de remboursement
            </h3>
            
            <form id="refundForm">
                @csrf
                
                <div class="mb-4">
                    <label for="refundType" class="block text-sm font-medium text-gray-700 mb-2">Type de remboursement</label>
                    <select id="refundType" name="refund_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Sélectionnez un type</option>
                        <option value="full">Remboursement complet</option>
                        <option value="partial">Remboursement partiel</option>
                    </select>
                </div>

                <div class="mb-4" id="partialAmountDiv" style="display: none;">
                    <label for="refundAmount" class="block text-sm font-medium text-gray-700 mb-2">Montant souhaité</label>
                    <div class="relative">
                        <input type="number" id="refundAmount" name="refund_amount" step="0.01" min="0" max="{{ $order->total_amount }}"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="refundReason" class="block text-sm font-medium text-gray-700 mb-2">Raison du remboursement</label>
                    <textarea id="refundReason" name="reason" rows="4" required
                              placeholder="Décrivez pourquoi vous demandez ce remboursement..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"></textarea>
                </div>

                <div class="mb-6">
                    <label for="evidencePhotos" class="block text-sm font-medium text-gray-700 mb-2">Photos de preuves (optionnel)</label>
                    <input type="file" id="evidencePhotos" name="evidence_photos[]" multiple accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <p class="text-xs text-gray-500 mt-1">Vous pouvez joindre des photos pour appuyer votre demande</p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRefundModal()"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200">
                        Soumettre la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonctions pour gérer le modal de remboursement
function openRefundModal() {
    document.getElementById('refundModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('refundForm').reset();
    document.getElementById('partialAmountDiv').style.display = 'none';
}

// Gérer l'affichage du champ montant selon le type de remboursement
document.getElementById('refundType').addEventListener('change', function() {
    const partialDiv = document.getElementById('partialAmountDiv');
    if (this.value === 'partial') {
        partialDiv.style.display = 'block';
        document.getElementById('refundAmount').required = true;
    } else {
        partialDiv.style.display = 'none';
        document.getElementById('refundAmount').required = false;
    }
});

// Fermer le modal en cliquant en dehors
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});

// Soumission du formulaire de remboursement
document.getElementById('refundForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Ajouter le token CSRF au FormData
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Debug: Afficher ce qui est envoyé
    console.log('Données à envoyer:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    // Désactiver le bouton et afficher le chargement
    submitButton.disabled = true;
    submitButton.textContent = 'Traitement...';
    
    fetch('{{ route('refund.request', $order) }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response body:', text);
                throw new Error(`HTTP error! status: ${response.status} - ${text.substring(0, 200)}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message || 'Demande de remboursement soumise avec succès !');
            closeRefundModal();
            window.location.reload();
        } else {
            alert(data.error || 'Erreur lors de la soumission de la demande');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de la soumission: ' + error.message);
    })
    .finally(() => {
        // Réactiver le bouton
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});

// Script pour confirmer la réception de la commande
// Script pour confirmer la réception de la commande
function confirmDelivery() {
    const note = prompt('Confirmez-vous avoir reçu votre commande ?\n\nVous pouvez ajouter un commentaire (optionnel) :');
    
    if (note !== null) { // L'utilisateur n'a pas cliqué sur Annuler
        fetch('{{ route('orders.confirm-delivery', $order) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                note: note || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.error || 'Erreur lors de la confirmation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la confirmation');
        });
    }
}

console.log('Page de commande chargée');
</script>
@endsection 