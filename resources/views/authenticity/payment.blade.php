@extends('app')

@section('title', 'Paiement de la vérification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Finaliser le paiement</h1>
            <p class="text-gray-600 dark:text-gray-300">Confirmez votre paiement pour lancer la vérification d'authenticité</p>
        </div>

        <!-- Résumé de la commande -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Résumé de la vérification</h2>
            
            <div class="flex items-center space-x-4 mb-4">
                @if(count($check->item->images) > 0)
                    <img src="{{ asset('storage/' . $check->item->images[0]) }}" 
                         alt="{{ $check->item->name }}" 
                         class="w-16 h-16 object-cover rounded-lg">
                @else
                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <span class="text-gray-400 text-xs">Pas d'image</span>
                    </div>
                @endif
                
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $check->item->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $check->item->brand->name ?? 'Marque non spécifiée' }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Catégorie: {{ $check->item->category->name ?? 'Non spécifiée' }}</p>
                </div>
                
                <div class="text-right">
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $check->item->formatted_price }}</p>
                </div>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-300">Frais de vérification d'authenticité</span>
                    <span class="text-xl font-bold text-blue-600">${{ number_format($check->verification_fee, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Ce qui est inclus -->
        <div class="bg-blue-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">✓ Ce qui est inclus</h3>
            <div class="space-y-3">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Analyse par Intelligence Artificielle</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Vérification automatique en quelques minutes</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Expertise humaine si nécessaire</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Vérification par un expert certifié de la catégorie</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Badge d'authenticité permanent</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Visible sur votre annonce pour rassurer les acheteurs</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Protection anti-fraude renforcée</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Réduction des litiges et remboursements frauduleux</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Processus de paiement -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Méthode de paiement</h3>
            
            <!-- Options de paiement (placeholder) -->
            <div class="space-y-4 mb-6">
                <div class="border rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <input type="radio" id="mobile_money" name="payment_method" value="mobile_money" checked
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                        <label for="mobile_money" class="flex-1">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">📱</span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Mobile Money</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Orange Money, Airtel Money</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4 opacity-75">
                    <div class="flex items-center space-x-3">
                        <input type="radio" id="card" name="payment_method" value="card" disabled
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                        <label for="card" class="flex-1">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">💳</span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Carte bancaire</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Bientôt disponible</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Informations importantes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Information importante</h3>
                        <div class="mt-1 text-sm text-yellow-700">
                            <p>• Le paiement lance immédiatement le processus de vérification</p>
                            <p>• Résultat sous 24h en moyenne</p>
                            <p>• Remboursement uniquement si erreur technique de notre part</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de confirmation -->
            <form action="{{ route('authenticity.payment.confirm', $check) }}" method="POST">
                @csrf
                
                <div class="flex items-start space-x-3 mb-6">
                    <input type="checkbox" id="payment_terms" required
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded mt-1">
                    <label for="payment_terms" class="text-sm text-gray-700 dark:text-gray-200">
                        Je confirme avoir lu et accepté les <a href="#" class="text-blue-600 hover:underline">conditions de vérification</a> 
                        et je comprends que ce paiement lance immédiatement le processus d'analyse. <span class="text-red-500">*</span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('authenticity.request', $check->item) }}" 
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:text-gray-100 font-medium">
                        ← Modifier ma demande
                    </a>
                    
                    <button type="submit" 
                            class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Confirmer le paiement - ${{ number_format($check->verification_fee, 2) }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Processus après paiement -->
        <div class="mt-6 bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
            <h4 class="font-medium text-gray-900 dark:text-white mb-2">🚀 Après confirmation du paiement :</h4>
            <ol class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                <li>1. Analyse automatique par IA (2-10 minutes)</li>
                <li>2. Si nécessaire, assignation à un expert (sous 2h)</li>
                <li>3. Examen expert et décision finale (sous 24h)</li>
                <li>4. Notification du résultat et attribution du badge</li>
            </ol>
        </div>
    </div>
</div>
@endsection