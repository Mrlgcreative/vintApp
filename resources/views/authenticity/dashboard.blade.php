@extends('app')

@section('title', 'Mes vérifications d\'authenticité')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Vérifications d'authenticité</h1>
            <p class="text-gray-600 dark:text-gray-300">Gérez toutes vos demandes de vérification et badges d'authenticité</p>
        </div>

        <!-- Statistiques -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Total demandes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_requests'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Produits vérifiés</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['verified_items'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">En cours</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_verifications'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <select class="border rounded-lg px-3 py-2 text-sm">
                        <option>Tous les statuts</option>
                        <option>En attente</option>
                        <option>Approuvé</option>
                        <option>Rejeté</option>
                        <option>Examen expert</option>
                    </select>
                    
                    <select class="border rounded-lg px-3 py-2 text-sm">
                        <option>Toutes les dates</option>
                        <option>Cette semaine</option>
                        <option>Ce mois</option>
                        <option>3 derniers mois</option>
                    </select>
                </div>
                
                <a href="{{ route('items.index') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">
                    + Nouveau produit
                </a>
            </div>
        </div>

        <!-- Liste des vérifications -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border">
            @if($checks->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($checks as $check)
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4">
                                    @if(count($check->item->images) > 0)
                                        <img src="{{ asset('storage/' . $check->item->images[0]) }}" 
                                             alt="{{ $check->item->name }}" 
                                             class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">No image</span>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $check->item->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                            {{ $check->item->brand->name ?? 'Marque non spécifiée' }} • 
                                            {{ $check->item->category->name ?? 'Catégorie' }}
                                        </p>
                                        <p class="text-lg font-bold text-indigo-600 mb-2">{{ $check->item->formatted_price }}</p>
                                        
                                        @if($check->item->isVerified())
                                            <div class="mb-2">
                                                {!! $check->item->getAuthenticityBadgeHtml() !!}
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Demande #{{ $check->id }}</span>
                                            <span>{{ $check->created_at->format('d/m/Y') }}</span>
                                            @if($check->expert)
                                                <span>Expert: {{ $check->expert->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right space-y-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $check->getStatusBadgeClass() }}">
                                        {{ $check->getStatusLabel() }}
                                    </span>
                                    
                                    <div class="space-y-1">
                                        @if(!$check->payment_completed)
                                            <a href="{{ route('authenticity.payment', $check) }}" 
                                               class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">
                                                Finaliser paiement
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('authenticity.status', $check->item) }}" 
                                           class="block w-full text-center bg-gray-100 dark:bg-gray-800 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 dark:bg-gray-700">
                                            Voir détails
                                        </a>
                                        
                                        <a href="{{ route('items.show', $check->item) }}" 
                                           class="block w-full text-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Voir produit
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progression rapide -->
                            <div class="mt-4 flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    @php
                                        $progress = 20; // Base
                                        if ($check->payment_completed) $progress = 40;
                                        if ($check->ai_completed_at) $progress = 60;
                                        if ($check->expert_assigned_at) $progress = 80;
                                        if ($check->final_decision_at) $progress = 100;
                                    @endphp
                                    <div class="h-2 rounded-full {{ $check->isApproved() ? 'bg-green-500' : ($check->isRejected() ? 'bg-red-500' : 'bg-blue-500') }}" 
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $progress }}%</span>
                            </div>
                            
                            @if($check->expert_notes)
                                <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Notes de l'expert :</h5>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ Str::limit($check->expert_notes, 150) }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($checks->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $checks->links() }}
                    </div>
                @endif
                
            @else
                <!-- État vide -->
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune vérification</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Vous n'avez pas encore demandé de vérification d'authenticité pour vos produits.</p>
                    <a href="{{ route('items.index') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
                        Voir mes produits
                    </a>
                </div>
            @endif
        </div>

        <!-- Information sur le processus -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">💡 Comment fonctionne la vérification ?</h3>
            <div class="grid md:grid-cols-2 gap-4 text-sm text-blue-800">
                <div>
                    <h4 class="font-medium mb-1">1. Soumission</h4>
                    <p>Fournissez photos HD, certificats et informations sur votre produit</p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">2. Analyse IA</h4>
                    <p>Notre intelligence artificielle examine les détails d'authenticité</p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">3. Expertise humaine</h4>
                    <p>Si nécessaire, un expert spécialisé examine votre produit</p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">4. Badge & Protection</h4>
                    <p>Badge visible + protection anti-fraude renforcée</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection