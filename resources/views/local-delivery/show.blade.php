@extends('app')

@section('title', 'Détails de la livraison locale')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Livraison Locale #{{ $localDelivery->id }}
                </h1>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @switch($localDelivery->status)
                        @case('proposed')
                            bg-blue-100 text-blue-800
                            @break
                        @case('accepted')
                            bg-green-100 text-green-800
                            @break
                        @case('in_transit')
                            bg-yellow-100 text-yellow-800
                            @break
                        @case('delivered')
                            bg-primary-100 text-primary-800
                            @break
                        @case('cancelled')
                            bg-red-100 text-red-800
                            @break
                        @default
                            bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                    @endswitch
                ">
                    {{ ucfirst(str_replace('_', ' ', $localDelivery->status)) }}
                </span>
            </div>
            
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                <p>Commande: <a href="{{ route('orders.show', $localDelivery->order->id) }}" 
                    class="text-blue-600 hover:text-blue-800">#{{ $localDelivery->order->order_number }}</a></p>
                <p>Type de livraison: {{ $localDelivery->delivery_type_text }}</p>
                <p>Distance: {{ $localDelivery->distance_km }} km</p>
                <p>Frais de livraison: {{ $localDelivery->delivery_fee }} {{ $localDelivery->currency }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Informations vendeur -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    <i class="fas fa-user-tie mr-2"></i>Vendeur
                </h2>
                <div class="space-y-3">
                    <p><strong>Nom:</strong> {{ $localDelivery->seller->name }}</p>
                    <p><strong>Email:</strong> {{ $localDelivery->seller->email }}</p>
                    @if($localDelivery->seller_phone)
                        <p><strong>Téléphone:</strong> {{ $localDelivery->seller_phone }}</p>
                    @endif
                    @if($localDelivery->seller_address)
                        <p><strong>Adresse:</strong> {{ $localDelivery->seller_address }}</p>
                    @endif
                    @if($localDelivery->seller_latitude && $localDelivery->seller_longitude)
                        <a href="{{ $localDelivery->getGoogleMapsDirectionUrl() }}" 
                           target="_blank" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                            <i class="fas fa-map-marker-alt mr-2"></i>Voir l'itinéraire
                        </a>
                    @endif
                </div>
            </div>

            <!-- Informations acheteur -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    <i class="fas fa-user mr-2"></i>Acheteur
                </h2>
                <div class="space-y-3">
                    <p><strong>Nom:</strong> {{ $localDelivery->buyer->name }}</p>
                    <p><strong>Email:</strong> {{ $localDelivery->buyer->email }}</p>
                    @if($localDelivery->buyer_phone)
                        <p><strong>Téléphone:</strong> {{ $localDelivery->buyer_phone }}</p>
                    @endif
                    @if($localDelivery->buyer_address)
                        <p><strong>Adresse:</strong> {{ $localDelivery->buyer_address }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions selon le statut et l'utilisateur -->
        @auth
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Actions</h2>
            
            @if($localDelivery->status === 'proposed' && auth()->id() === $localDelivery->buyer_id)
                <!-- L'acheteur peut accepter la proposition -->
                <div class="flex space-x-4">
                    <form action="{{ route('local-delivery.accept', $localDelivery) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md">
                            <i class="fas fa-check mr-2"></i>Accepter la livraison
                        </button>
                    </form>
                    
                    <form action="{{ route('local-delivery.cancel', $localDelivery) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="reason" value="Refusé par l'acheteur">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md">
                            <i class="fas fa-times mr-2"></i>Refuser
                        </button>
                    </form>
                </div>
            @endif

            @if($localDelivery->status === 'accepted' && auth()->id() === $localDelivery->seller_id)
                <!-- Le vendeur peut marquer comme en transit -->
                <form action="{{ route('local-delivery.in-transit', $localDelivery) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-md">
                        <i class="fas fa-truck mr-2"></i>Marquer en transit
                    </button>
                </form>
            @endif

            @if($localDelivery->status === 'in_transit')
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                    <p class="text-yellow-800">
                        <strong>Code de vérification:</strong> {{ $localDelivery->delivery_code }}
                    </p>
                    <p class="text-sm text-yellow-700 mt-2">
                        Communiquez ce code à l'acheteur lors de la remise en main propre.
                    </p>
                </div>

                @if(auth()->id() === $localDelivery->buyer_id)
                    <!-- L'acheteur peut confirmer la livraison avec le code -->
                    <form action="{{ route('local-delivery.delivered', $localDelivery) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="delivery_code" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Code de vérification reçu du vendeur:
                            </label>
                            <input type="text" name="delivery_code" id="delivery_code" required 
                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-md">
                            <i class="fas fa-check-circle mr-2"></i>Confirmer la réception
                        </button>
                    </form>
                @endif
            @endif

            @if(in_array($localDelivery->status, ['proposed', 'accepted', 'in_transit']) && 
                (auth()->id() === $localDelivery->seller_id || auth()->id() === $localDelivery->buyer_id))
                <!-- Annulation possible avant la livraison -->
                <form action="{{ route('local-delivery.cancel', $localDelivery) }}" method="POST" class="inline mt-4">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Raison de l'annulation (optionnel):
                            </label>
                            <input type="text" name="reason" id="reason" 
                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Expliquez pourquoi vous annulez...">
                        </div>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md">
                            <i class="fas fa-ban mr-2"></i>Annuler la livraison
                        </button>
                    </div>
                </form>
            @endif
        </div>
        @endauth

        <!-- Historique -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Historique</h2>
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">Livraison proposée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $localDelivery->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                @if($localDelivery->accepted_at)
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">Livraison acceptée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $localDelivery->accepted_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
                @endif

                @if($localDelivery->pickup_time)
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">En transit</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $localDelivery->pickup_time->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
                @endif

                @if($localDelivery->actual_delivery_time)
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-primary-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">Livraison effectuée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $localDelivery->actual_delivery_time->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
                @endif

                @if($localDelivery->status === 'cancelled')
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div>
                        <p class="font-medium">Livraison annulée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $localDelivery->updated_at->format('d/m/Y à H:i') }}</p>
                        @if($localDelivery->cancellation_reason)
                            <p class="text-sm text-red-600">Raison: {{ $localDelivery->cancellation_reason }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection