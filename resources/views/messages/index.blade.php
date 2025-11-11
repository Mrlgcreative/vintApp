@extends('app')

@section('title', 'Mes conversations avec les vendeurs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-blue-600 flex items-center">
                <i class="fas fa-store mr-3"></i>
                Mes conversations avec les vendeurs
            </h2>
            <p class="text-gray-600 mt-2">Gérez vos discussions et demandes de réduction</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au tableau de bord
            </a>
        </div>
    </div>

    @if($vendorContacts->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($vendorContacts as $contact)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <!-- En-tête avec le vendeur -->
                    <div class="bg-gray-50 rounded-t-xl p-4 flex items-center">
                        <div class="mr-3">
                            @if($contact->vendor->avatar)
                                <img src="{{ Storage::url($contact->vendor->avatar) }}" 
                                     alt="{{ $contact->vendor->name }}" 
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                                    {{ strtoupper(substr($contact->vendor->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h6 class="font-semibold text-gray-900 truncate">{{ $contact->vendor->name }}</h6>
                            <p class="text-sm text-gray-500 flex items-center">
                                <i class="fas fa-calendar mr-1"></i>
                                Contacté {{ $contact->contact_date->diffForHumans() }}
                            </p>
                        </div>
                        @if($contact->unread_count > 0)
                            <span class="bg-red-500 text-white text-xs font-medium px-2 py-1 rounded-full animate-pulse">
                                {{ $contact->unread_count }}
                            </span>
                        @endif
                    </div>

                    <!-- Produit concerné -->
                    <div class="p-4">
                        <div class="flex items-start mb-4">
                            @if($contact->item && $contact->item->images && count($contact->item->images) > 0)
                                <img src="{{ Storage::url($contact->item->images[0]) }}" 
                                     alt="{{ $contact->item->name }}" 
                                     class="w-20 h-20 rounded-lg object-cover mr-3 flex-shrink-0">
                            @else
                                <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-content-center mr-3 flex-shrink-0">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                @if($contact->item)
                                    <h6 class="font-semibold text-gray-900 mb-1 truncate">{{ $contact->item->name }}</h6>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-blue-600 font-bold">{{ $contact->item->formatted_price }}</span>
                                        @if($contact->has_discount)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full flex items-center">
                                                <i class="fas fa-tag mr-1"></i>
                                                Réduction accordée
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500">{{ $contact->item->category->name }}</p>
                                @else
                                    <h6 class="font-semibold text-gray-500 mb-1">Article non disponible</h6>
                                @endif
                            </div>
                        </div>

                        <!-- Dernier message -->
                        @if($contact->last_message)
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex items-start">
                                    <div class="mr-2 mt-1">
                                        @if($contact->last_message->sender_id === Auth::id())
                                            <i class="fas fa-reply text-blue-500"></i>
                                        @else
                                            <i class="fas fa-comment text-green-500"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-700 mb-1 truncate">
                                            @if($contact->last_message->sender_id === Auth::id())
                                                <span class="font-medium">Vous :</span>
                                            @else
                                                <span class="font-medium">{{ $contact->vendor->name }} :</span>
                                            @endif
                                            {{ $contact->last_message->content ?: 'Fichier joint' }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $contact->last_message_time }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="p-4 pt-0">
                        <div class="flex gap-2">
                            <a href="{{ route('messages.show', ['user' => $contact->vendor_id, 'item_id' => $contact->item_id]) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-comments mr-2"></i>
                                Ouvrir la conversation
                            </a>
                            @if($contact->item)
                                <a href="{{ route('items.show', $contact->item) }}" 
                                   class="bg-gray-100 text-gray-700 p-2 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-gray-100 transition-all duration-300 hover:-translate-y-1">
                <i class="fas fa-store text-4xl text-blue-600 mb-3"></i>
                <h5 class="text-2xl font-bold text-gray-900">{{ $vendorContacts->count() }}</h5>
                <p class="text-sm text-gray-600">Vendeurs contactés</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-gray-100 transition-all duration-300 hover:-translate-y-1">
                <i class="fas fa-tag text-4xl text-green-600 mb-3"></i>
                <h5 class="text-2xl font-bold text-gray-900">{{ $vendorContacts->where('has_discount', true)->count() }}</h5>
                <p class="text-sm text-gray-600">Réductions obtenues</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-6 text-center hover:bg-gray-100 transition-all duration-300 hover:-translate-y-1">
                <i class="fas fa-envelope text-4xl text-yellow-600 mb-3"></i>
                <h5 class="text-2xl font-bold text-gray-900">{{ $vendorContacts->sum('unread_count') }}</h5>
                <p class="text-sm text-gray-600">Messages non lus</p>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <div class="mb-6">
                <i class="fas fa-store text-6xl text-gray-400"></i>
            </div>
            <h4 class="text-2xl font-medium text-gray-500 mb-4">Aucun vendeur contacté</h4>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Vous n'avez pas encore contacté de vendeurs pour demander des réductions.<br>
                Parcourez les produits et utilisez le bouton "Contacter le vendeur" pour commencer.
            </p>
            <a href="{{ route('items.index') }}" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Parcourir les produits
            </a>
        </div>
    @endif
</div>
@endsection 