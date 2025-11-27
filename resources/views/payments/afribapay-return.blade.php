@extends('app')

@section('title', $payment->isCompleted() ? 'Paiement réussi' : 'Statut du paiement')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        
        @if($payment->isCompleted())
        {{-- Paiement réussi --}}
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-green-600 mb-2">Paiement réussi ! 🎉</h1>
            <p class="text-gray-600">Votre commande a été confirmée et sera traitée dans les plus brefs délais.</p>
        </div>

        {{-- Détails du paiement --}}
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <h2 class="font-semibold text-green-900 mb-4">✅ Résumé de la transaction</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-green-700">Référence</p>
                    <p class="font-mono text-sm font-semibold text-green-900">{{ $payment->transaction_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-green-700">Montant payé</p>
                    <p class="text-lg font-bold text-green-900">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</p>
                </div>
                <div>
                    <p class="text-sm text-green-700">Date</p>
                    <p class="text-sm font-medium text-green-900">{{ $payment->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-green-700">Méthode</p>
                    <p class="text-sm font-medium text-green-900">📱 AfribaPay Mobile Money</p>
                </div>
            </div>
        </div>

        {{-- Commandes créées --}}
        @if($payment->order_id || isset($payment->metadata['cart_items']))
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h2 class="font-semibold text-gray-900 mb-4">📦 Vos commandes</h2>
            
            @php
                $cartItems = $payment->metadata['cart_items'] ?? [];
            @endphp
            
            @if(count($cartItems) > 0)
            <div class="space-y-3">
                @foreach($cartItems as $item)
                <div class="flex justify-between items-center py-2 border-b last:border-0">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $item['name'] ?? 'Article' }}</p>
                        <p class="text-sm text-gray-600">Quantité: {{ $item['quantity'] ?? 1 }}</p>
                    </div>
                    <p class="font-semibold text-gray-900">
                        {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', ' ') }} {{ $payment->currency }}
                    </p>
                </div>
                @endforeach
            </div>
            @endif
            
            <div class="mt-4 pt-4 border-t">
                <p class="text-sm text-gray-600 mb-2">📧 Un email de confirmation a été envoyé à {{ auth()->user()->email }}</p>
                <p class="text-sm text-gray-600">📱 Vous pouvez suivre vos commandes dans votre espace "Mes achats"</p>
            </div>
        </div>
        @endif

        {{-- Prochaines étapes --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-900 text-sm mb-2">🚀 Prochaines étapes</h3>
            <ol class="text-sm text-blue-700 space-y-2 list-decimal list-inside">
                <li>Le vendeur sera notifié de votre achat</li>
                <li>Votre commande sera préparée et expédiée</li>
                <li>Vous recevrez une notification lors de l'expédition</li>
                <li>Suivez l'état de votre commande dans "Mes achats"</li>
            </ol>
        </div>

        @else
        {{-- Paiement non complété --}}
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-yellow-600 mb-2">Paiement en cours</h1>
            <p class="text-gray-600">Votre paiement est en cours de traitement.</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
            <h2 class="font-semibold text-yellow-900 mb-4">⏱️ Statut: {{ ucfirst($payment->status) }}</h2>
            <p class="text-sm text-yellow-700 mb-4">
                @if($payment->status === 'pending')
                Votre paiement est en attente de confirmation. Cela peut prendre quelques minutes.
                @elseif($payment->status === 'failed')
                Le paiement a échoué. Raison: {{ $payment->error_message ?? 'Erreur inconnue' }}
                @else
                Statut du paiement: {{ $payment->status }}
                @endif
            </p>
            
            <div class="mt-4">
                <p class="text-sm text-yellow-700 font-medium">Référence: <span class="font-mono">{{ $payment->transaction_id }}</span></p>
            </div>
        </div>
        @endif

        {{-- Boutons d'action --}}
        <div class="space-y-3">
            @if($payment->isCompleted())
            <a href="{{ route('orders.index') }}" 
               class="block w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-6 rounded-lg transition-all duration-200 text-center shadow-lg">
                📦 Voir mes commandes
            </a>
            
            <a href="{{ route('home') }}" 
               class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-all duration-200 text-center">
                🏠 Retour à l'accueil
            </a>
            @else
            <a href="{{ route('payments.afribapay.status', $payment) }}" 
               class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-6 rounded-lg transition-all duration-200 text-center">
                🔄 Vérifier le statut du paiement
            </a>
            
            <a href="{{ route('cart.checkout') }}" 
               class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-all duration-200 text-center">
                ← Retour au checkout
            </a>
            @endif
        </div>

        {{-- Support --}}
        <div class="mt-8 pt-6 border-t text-center">
            <p class="text-sm text-gray-600 mb-2">Besoin d'aide ?</p>
            <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                📧 Contactez notre support client
            </a>
        </div>
    </div>

    {{-- Recommandations de produits (optionnel) --}}
    @if($payment->isCompleted())
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">🛍️ Continuez vos achats</h2>
        <div class="bg-gray-50 rounded-lg p-6 text-center">
            <p class="text-gray-600 mb-4">Découvrez d'autres articles qui pourraient vous intéresser</p>
            <a href="{{ route('items.index') }}" 
               class="inline-block bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg transition-all duration-200">
                Voir les articles
            </a>
        </div>
    </div>
    @endif
</div>

@if($payment->isCompleted())
@push('scripts')
<script>
// Confetti animation pour célébrer le paiement réussi
document.addEventListener('DOMContentLoaded', function() {
    // Optionnel: Ajouter une librairie de confetti comme canvas-confetti
    console.log('🎉 Paiement réussi!');
});
</script>
@endpush
@endif
@endsection
