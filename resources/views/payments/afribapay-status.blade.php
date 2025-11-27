@extends('app')

@section('title', 'Vérification du paiement - AfribaPay')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        {{-- Animation de chargement --}}
        <div class="text-center mb-6" id="loading-state">
            <div class="w-20 h-20 mx-auto mb-4">
                <svg class="animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Vérification du paiement</h1>
            <p class="text-gray-600 mt-2">Veuillez patienter pendant la confirmation...</p>
            <div class="mt-4">
                <div class="inline-flex items-center space-x-2">
                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>

        {{-- État de succès (caché par défaut) --}}
        <div class="text-center hidden" id="success-state">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-green-600">Paiement réussi !</h1>
            <p class="text-gray-600 mt-2">Votre commande a été confirmée</p>
        </div>

        {{-- État d'échec (caché par défaut) --}}
        <div class="text-center hidden" id="failed-state">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-red-600">Paiement échoué</h1>
            <p class="text-gray-600 mt-2" id="error-message">Une erreur s'est produite</p>
        </div>

        {{-- Détails du paiement --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Référence</span>
                    <span class="font-mono text-xs text-gray-900">{{ $payment->transaction_id }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Montant</span>
                    <span class="font-bold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Statut</span>
                    <span id="status-badge" class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                        En attente
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Date</span>
                    <span class="text-gray-900">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Instructions Mobile Money --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-900 text-sm">📱 Vérifiez votre téléphone</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Vous devriez recevoir une notification sur votre téléphone Mobile Money pour confirmer le paiement.
                        Suivez les instructions affichées sur votre écran.
                    </p>
                </div>
            </div>
        </div>

        {{-- Historique des vérifications --}}
        <div class="text-center text-xs text-gray-500 mb-4">
            <p>Vérification automatique en cours...</p>
            <p class="mt-1">Tentative <span id="check-count">1</span> sur 40</p>
        </div>

        {{-- Boutons d'action --}}
        <div class="space-y-3">
            <button onclick="checkNow()" id="check-now-btn"
                    class="w-full bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-3 px-6 rounded-lg transition-all duration-200">
                Vérifier maintenant
            </button>

            <a href="{{ route('cart.checkout') }}" class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-all duration-200">
                Retour au checkout
            </a>
        </div>
    </div>

    {{-- Aide --}}
    <div class="mt-6 bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-900 text-sm mb-2">⏱️ Le paiement prend du temps ?</h3>
        <ul class="text-xs text-gray-600 space-y-1 list-disc list-inside">
            <li>Vérifiez que vous avez confirmé le paiement sur votre téléphone</li>
            <li>Assurez-vous d'avoir un solde suffisant</li>
            <li>Vérifiez votre connexion réseau</li>
            <li>Le traitement peut prendre jusqu'à 2 minutes</li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
let checkCount = 0;
const maxChecks = 40; // 40 × 3s = 2 minutes max
const paymentId = {{ $payment->id }};

// Fonction de vérification du statut
async function checkPaymentStatus() {
    try {
        const response = await fetch(`/payments/afribapay/${paymentId}/check-status`);
        const data = await response.json();
        
        checkCount++;
        document.getElementById('check-count').textContent = checkCount;
        
        const status = data.status;
        const statusBadge = document.getElementById('status-badge');
        
        // Mettre à jour le badge de statut
        if (status === 'completed') {
            statusBadge.textContent = 'Réussi';
            statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800';
            showSuccess();
            return true; // Arrêter le polling
        } else if (status === 'failed') {
            statusBadge.textContent = 'Échoué';
            statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800';
            showFailed(data.message || 'Le paiement a échoué');
            return true; // Arrêter le polling
        } else if (status === 'cancelled') {
            statusBadge.textContent = 'Annulé';
            statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800';
            showFailed('Le paiement a été annulé');
            return true; // Arrêter le polling
        }
        
        // Continuer le polling
        return false;
        
    } catch (error) {
        console.error('Erreur lors de la vérification:', error);
        return false;
    }
}

// Afficher l'état de succès
function showSuccess() {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('failed-state').classList.add('hidden');
    document.getElementById('success-state').classList.remove('hidden');
    
    // Confetti animation (optionnel)
    setTimeout(() => {
        window.location.href = `/payments/afribapay/return?payment=${paymentId}`;
    }, 2000);
}

// Afficher l'état d'échec
function showFailed(message) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('success-state').classList.add('hidden');
    document.getElementById('failed-state').classList.remove('hidden');
    document.getElementById('error-message').textContent = message;
}

// Polling automatique
async function startPolling() {
    const shouldStop = await checkPaymentStatus();
    
    if (!shouldStop && checkCount < maxChecks) {
        setTimeout(startPolling, 3000); // Vérifier toutes les 3 secondes
    } else if (checkCount >= maxChecks) {
        showFailed('Délai d\'attente dépassé. Veuillez vérifier votre compte ou contacter le support.');
    }
}

// Vérification manuelle
async function checkNow() {
    const btn = document.getElementById('check-now-btn');
    btn.disabled = true;
    btn.textContent = 'Vérification...';
    
    await checkPaymentStatus();
    
    setTimeout(() => {
        btn.disabled = false;
        btn.textContent = 'Vérifier maintenant';
    }, 2000);
}

// Démarrer le polling au chargement de la page
startPolling();
</script>
@endpush
@endsection
