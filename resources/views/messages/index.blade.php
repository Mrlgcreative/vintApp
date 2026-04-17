@extends('app')

@section('title', 'Mes conversations')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                        <i class="fas fa-comments text-white text-xl"></i>
                    </div>
                    Mes conversations
                </h1>
                <p class="text-gray-600 dark:text-gray-400">Gérez vos discussions avec les vendeurs</p>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>

        @if($vendorContacts->count() > 0)
            <!-- Statistiques -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-store text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $vendorContacts->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Vendeurs contactés</p>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tag text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $vendorContacts->where('has_discount', true)->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Réductions obtenues</p>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-envelope text-2xl text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $vendorContacts->sum('unread_count') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Messages non lus</p>
                    </div>
                </div>
            </div>

            <!-- Liste des conversations -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($vendorContacts as $contact)
                        @if(!$contact->vendor) @continue @endif
                        <a href="{{ route('messages.show', ['user' => $contact->vendor_id, 'item_id' => $contact->item_id]) }}" 
                           class="flex items-center gap-4 p-4 sm:p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            
                            <!-- Avatar vendeur -->
                            <div class="relative flex-shrink-0">
                                @if($contact->vendor->avatar_url)
                                    <img src="{{ $contact->vendor->avatar_url }}" 
                                         alt="{{ $contact->vendor->name }}" 
                                         class="w-14 h-14 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700">
                                @else
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-lg font-bold ring-2 ring-gray-100 dark:ring-gray-700">
                                        {{ strtoupper(substr($contact->vendor->name, 0, 1)) }}
                                    </div>
                                @endif
                                @if($contact->unread_count > 0)
                                    <span class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse shadow-lg">
                                        {{ $contact->unread_count > 9 ? '9+' : $contact->unread_count }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Contenu principal -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $contact->vendor->name }}
                                    </h3>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">
                                        {{ $contact->last_message ? $contact->last_message_time : $contact->contact_date->diffForHumans() }}
                                    </span>
                                </div>
                                
                                <!-- Article -->
                                <div class="flex items-center gap-2 mb-2">
                                    @if($contact->item)
                                        <span class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ $contact->item->name }}</span>
                                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $contact->item->formatted_price }}</span>
                                        @if($contact->has_discount)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full">
                                                <i class="fas fa-tag mr-1 text-[10px]"></i>Réduction
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500 italic">Article non disponible</span>
                                    @endif
                                </div>
                                
                                <!-- Dernier message -->
                                @if($contact->last_message)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        @if($contact->last_message->sender_id === Auth::id())
                                            <span class="text-blue-600 dark:text-blue-400">Vous :</span>
                                        @endif
                                        {{ $contact->last_message->content ?: '📎 Fichier joint' }}
                                    </p>
                                @endif
                            </div>
                            
                            <!-- Image article -->
                            <div class="hidden sm:block flex-shrink-0">
                                @if($contact->item && $contact->item->images && count($contact->item->images) > 0)
                                    <img src="{{ Storage::url($contact->item->images[0]) }}" 
                                         alt="{{ $contact->item->name }}" 
                                         class="w-16 h-16 rounded-xl object-cover">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Chevron -->
                            <div class="flex-shrink-0 text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        @else
            <!-- État vide -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-12 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-comments text-4xl text-blue-500 dark:text-blue-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Aucune conversation</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    Vous n'avez pas encore contacté de vendeurs.<br>
                    Parcourez les articles et contactez les vendeurs pour négocier !
                </p>
                <a href="{{ route('items.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Parcourir les articles
                </a>
            </div>
        @endif

        <!-- Aide -->
        <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lightbulb text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-2">Conseils pour négocier</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Soyez poli et respectueux dans vos messages</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Proposez un prix raisonnable basé sur l'état de l'article</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Répondez rapidement aux messages des vendeurs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 