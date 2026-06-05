@extends('app')

@section('title', 'Statut de vérification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête avec statut -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    @if(count($item->images) > 0)
                        <img src="{{ asset('storage/' . $item->images[0]) }}" 
                             alt="{{ $item->name }}" 
                             class="w-20 h-20 object-cover rounded-lg">
                    @else
                        <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <span class="text-gray-400 text-xs">Pas d'image</span>
                        </div>
                    @endif
                    
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $item->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-300 mb-2">{{ $item->brand->name ?? 'Marque non spécifiée' }} • {{ $item->category->name ?? 'Catégorie' }}</p>
                        <p class="text-lg font-semibold text-indigo-600">{{ $item->formatted_price }}</p>
                        
                        @if($item->isVerified())
                            <div class="mt-2">
                                {!! $item->getAuthenticityBadgeHtml() !!}
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $check->getStatusBadgeClass() }}">
                        {{ $check->getStatusLabel() }}
                    </span>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Demande #{{ $check->id }}</p>
                </div>
            </div>
        </div>

        <!-- Statut principal -->
        @if($check->isApproved())
            <!-- Statut Approuvé -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-green-900">🎉 Produit authentifié avec succès!</h2>
                        <p class="text-green-700 mt-1">Votre produit a été vérifié et est maintenant affiché avec le badge d'authenticité VintApp.</p>
                        @if($check->final_decision_at)
                            <p class="text-sm text-green-600 mt-2">Vérifié le {{ $check->final_decision_at->format('d/m/Y à H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
        @elseif($check->isRejected())
            <!-- Statut Rejeté -->
            <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-lg border border-red-200 p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-red-900">Vérification non concluante</h2>
                        <p class="text-red-700 mt-1">Nous n'avons pas pu certifier l'authenticité de ce produit avec notre processus strict.</p>
                        @if($check->final_decision_at)
                            <p class="text-sm text-red-600 mt-2">Décision prise le {{ $check->final_decision_at->format('d/m/Y à H:i') }}</p>
                        @endif
                    </div>
                </div>
                
                @if($check->expert_notes)
                    <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Notes de l'expert :</h4>
                        <p class="text-gray-700 dark:text-gray-200 text-sm">{{ $check->expert_notes }}</p>
                    </div>
                @endif
            </div>
            
        @else
            <!-- Statut En cours -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-blue-900">Vérification en cours...</h2>
                        @if($check->status === 'pending')
                            <p class="text-blue-700 mt-1">Votre demande est en attente de traitement.</p>
                        @elseif($check->status === 'expert_review')
                            <p class="text-blue-700 mt-1">Un expert examine actuellement votre produit.</p>
                            @if($check->expert)
                                <p class="text-sm text-blue-600 mt-2">Expert assigné : {{ $check->expert->name }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Progression -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Progression de la vérification</h3>
            
            <div class="relative">
                <!-- Ligne de progression -->
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                
                <!-- Étapes -->
                <div class="space-y-6">
                    <!-- Soumission -->
                    <div class="relative flex items-start">
                        <div class="relative z-10 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-900 dark:text-white">Demande soumise</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $check->submitted_at ? $check->submitted_at->format('d/m/Y à H:i') : 'En attente' }}</p>
                            @if($check->payment_completed)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                                    Paiement confirmé
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Analyse IA -->
                    <div class="relative flex items-start">
                        @if($check->ai_completed_at)
                            <div class="relative z-10 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @elseif($check->payment_completed)
                            <div class="relative z-10 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        @else
                            <div class="relative z-10 w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-900 dark:text-white">Analyse par IA</h4>
                            @if($check->ai_completed_at)
                                <p class="text-sm text-gray-600 dark:text-gray-300">Terminée le {{ $check->ai_completed_at->format('d/m/Y à H:i') }}</p>
                                @if($check->ai_confidence_score)
                                    <p class="text-sm text-blue-600">Score de confiance: {{ $check->ai_confidence_score }}%</p>
                                @endif
                            @elseif($check->payment_completed)
                                <p class="text-sm text-blue-600">En cours d'analyse...</p>
                            @else
                                <p class="text-sm text-gray-400">En attente du paiement</p>
                            @endif
                        </div>
                    </div>

                    <!-- Examen expert (si nécessaire) -->
                    @if($check->needsExpertReview() || $check->expert_completed_at)
                        <div class="relative flex items-start">
                            @if($check->expert_completed_at)
                                <div class="relative z-10 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @elseif($check->expert_assigned_at)
                                <div class="relative z-10 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="relative z-10 w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="ml-4">
                                <h4 class="font-medium text-gray-900 dark:text-white">Examen par expert</h4>
                                @if($check->expert_completed_at)
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Terminé le {{ $check->expert_completed_at->format('d/m/Y à H:i') }}</p>
                                    @if($check->expert)
                                        <p class="text-sm text-primary-600">Expert: {{ $check->expert->name }}</p>
                                    @endif
                                @elseif($check->expert_assigned_at)
                                    <p class="text-sm text-blue-600">En cours d'examen par un expert</p>
                                    @if($check->expert)
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Expert assigné: {{ $check->expert->name }}</p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-400">En attente d'assignation</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Décision finale -->
                    <div class="relative flex items-start">
                        @if($check->final_decision_at)
                            @if($check->isApproved())
                                <div class="relative z-10 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="relative z-10 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            @endif
                        @else
                            <div class="relative z-10 w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-900 dark:text-white">Décision finale</h4>
                            @if($check->final_decision_at)
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $check->final_decision_at->format('d/m/Y à H:i') }}</p>
                                @if($check->isApproved())
                                    <p class="text-sm text-green-600">✓ Produit authentifié</p>
                                @else
                                    <p class="text-sm text-red-600">✗ Authenticité non confirmée</p>
                                @endif
                            @else
                                <p class="text-sm text-gray-400">En attente</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Images soumises -->
        @if($check->verificationImages->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Images soumises pour vérification</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($check->verificationImages as $image)
                        <div class="relative group">
                            <img src="{{ $image->getImageUrl() }}" 
                                 alt="{{ $image->getTypeLabel() }}"
                                 class="w-full h-32 object-cover rounded-lg border cursor-pointer hover:opacity-75 transition-opacity">
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-75 text-white text-xs p-2 rounded-b-lg">
                                {{ $image->getTypeLabel() }}
                                @if($image->image_quality_score)
                                    <span class="float-right">{{ $image->image_quality_score }}%</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('items.show', $item) }}" 
               class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:text-gray-100 font-medium">
                ← Retour au produit
            </a>
            
            <div class="space-x-3">
                @if(!$check->payment_completed)
                    <a href="{{ route('authenticity.payment', $check) }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700">
                        Finaliser le paiement
                    </a>
                @endif
                
                <a href="{{ route('authenticity.dashboard') }}" 
                   class="bg-gray-100 dark:bg-gray-800 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-200 dark:bg-gray-700">
                    Mes vérifications
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh pour les vérifications en cours
@if(!$check->final_decision_at)
    setInterval(function() {
        location.reload();
    }, 30000); // Refresh toutes les 30 secondes
@endif
</script>
@endsection