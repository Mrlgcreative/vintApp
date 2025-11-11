@extends('app')

@section('title', 'Mes livraisons locales')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête avec onglets -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <a href="{{ route('local-delivery.user', 'seller') }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm
                       @if(request('type') === 'seller')
                           border-indigo-500 text-indigo-600
                       @else
                           border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300
                       @endif">
                        Mes ventes
                        @if(isset($counts['seller']) && $counts['seller'] > 0)
                            <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $counts['seller'] }}
                            </span>
                        @endif
                    </a>
                    
                    <a href="{{ route('local-delivery.user', 'buyer') }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm
                       @if(request('type') === 'buyer')
                           border-indigo-500 text-indigo-600
                       @else
                           border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300
                       @endif">
                        Mes achats
                        @if(isset($counts['buyer']) && $counts['buyer'] > 0)
                            <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $counts['buyer'] }}
                            </span>
                        @endif
                    </a>
                </nav>
            </div>

            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    @if(request('type') === 'seller')
                        Mes livraisons en tant que vendeur
                    @else
                        Mes livraisons en tant qu'acheteur
                    @endif
                </h1>
                <p class="text-gray-600 mt-2">
                    Gérez vos livraisons locales et suivez leur progression.
                </p>
            </div>
        </div>

        <!-- Liste des livraisons -->
        @if($deliveries->count() > 0)
        <div class="space-y-4">
            @foreach($deliveries as $delivery)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Commande #{{ $delivery->order->order_number }}
                            </h3>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @switch($delivery->status)
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
                                        bg-purple-100 text-purple-800
                                        @break
                                    @case('cancelled')
                                        bg-red-100 text-red-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">
                                    @if(request('type') === 'seller')
                                        Acheteur:
                                    @else
                                        Vendeur:
                                    @endif
                                </span>
                                <p>
                                    @if(request('type') === 'seller')
                                        {{ $delivery->buyer->name }}
                                    @else
                                        {{ $delivery->seller->name }}
                                    @endif
                                </p>
                            </div>

                            <div>
                                <span class="font-medium">Type:</span>
                                <p>{{ $delivery->delivery_type_text }}</p>
                            </div>

                            <div>
                                <span class="font-medium">Distance:</span>
                                <p>{{ $delivery->distance_km }} km</p>
                            </div>

                            <div>
                                <span class="font-medium">Frais:</span>
                                <p>{{ $delivery->delivery_fee }} {{ $delivery->currency }}</p>
                            </div>
                        </div>

                        <div class="mt-3 text-sm text-gray-600">
                            <span class="font-medium">Créé le:</span>
                            {{ $delivery->created_at->format('d/m/Y à H:i') }}
                            
                            @if($delivery->status === 'in_transit' && $delivery->delivery_code)
                                <span class="ml-4 font-medium text-yellow-700">
                                    Code: {{ $delivery->delivery_code }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Actions rapides selon le statut -->
                        @if($delivery->status === 'proposed' && request('type') === 'buyer')
                            <form action="{{ route('local-delivery.accept', $delivery) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                                    Accepter
                                </button>
                            </form>
                        @endif

                        @if($delivery->status === 'accepted' && request('type') === 'seller')
                            <form action="{{ route('local-delivery.in-transit', $delivery) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm">
                                    En transit
                                </button>
                            </form>
                        @endif

                        <!-- Bouton voir détails -->
                        <a href="{{ route('local-delivery.show', $delivery) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                            Détails
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($deliveries->hasPages())
        <div class="mt-6">
            {{ $deliveries->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <!-- État vide -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <div class="max-w-sm mx-auto">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8v.01M6 5v.01" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune livraison</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request('type') === 'seller')
                        Vous n'avez encore proposé aucune livraison locale.
                    @else
                        Vous n'avez encore reçu aucune proposition de livraison locale.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('orders.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Voir mes commandes
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection