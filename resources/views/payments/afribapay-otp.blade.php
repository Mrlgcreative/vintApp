@extends('app')

@section('title', 'Vérification OTP - AfribaPay')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        {{-- En-tête --}}
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Vérification requise</h1>
            <p class="text-gray-600 mt-2">Code OTP nécessaire pour valider votre paiement</p>
        </div>

        {{-- Détails du paiement --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Montant</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Référence</p>
                    <p class="text-xs font-mono text-gray-900">{{ $payment->transaction_id }}</p>
                </div>
            </div>
        </div>

        {{-- Instructions USSD --}}
        @if($ussdCode)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-semibold text-amber-900 text-sm mb-2">📱 Comment obtenir votre code OTP</h3>
                    <ol class="text-sm text-amber-800 space-y-1 list-decimal list-inside">
                        <li>Composez sur votre téléphone : <code class="bg-amber-100 px-2 py-1 rounded font-mono text-xs">{{ $ussdCode }}</code></li>
                        <li>Suivez les instructions à l'écran</li>
                        <li>Vous recevrez un code à 6 chiffres par SMS</li>
                        <li>Entrez ce code ci-dessous</li>
                    </ol>
                </div>
            </div>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-900 text-sm">📲 Vérifiez votre téléphone</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Un code de vérification à 6 chiffres a été envoyé par SMS à votre numéro Mobile Money.
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Formulaire OTP --}}
        <form action="{{ route('payments.afribapay.verify-otp', $payment) }}" method="POST" id="otp-form">
            @csrf
            
            <div class="mb-6">
                <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                    Code OTP (6 chiffres)
                </label>
                <input type="text" name="otp" id="otp" required
                       maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                       placeholder="000000"
                       class="w-full px-4 py-4 text-2xl text-center font-mono border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary tracking-widest"
                       autofocus>
                
                @if(config('services.afribapay.environment') === 'sandbox')
                <p class="mt-2 text-xs text-center text-gray-500">
                    🧪 Mode TEST : N'importe quel code sauf 000000-444444 fonctionnera
                </p>
                @endif
                
                @error('otp')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Boutons --}}
            <div class="space-y-3">
                <button type="submit" id="verify-btn"
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 shadow-lg">
                    Vérifier le code
                </button>

                <button type="button" onclick="resendOTP()" id="resend-btn"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-all duration-200">
                    Renvoyer le code OTP
                </button>

                <div class="text-center">
                    <a href="{{ route('cart.checkout') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        Annuler le paiement
                    </a>
                </div>
            </div>
        </form>

        {{-- Timer de session --}}
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                Code valide pendant <span id="timer" class="font-semibold text-gray-700">10:00</span>
            </p>
        </div>
    </div>

    {{-- Aide --}}
    <div class="mt-6 bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-900 text-sm mb-2">❓ Vous n'avez pas reçu le code ?</h3>
        <ul class="text-xs text-gray-600 space-y-1 list-disc list-inside">
            <li>Vérifiez que votre téléphone a du réseau</li>
            <li>Patientez quelques secondes, le SMS peut prendre du temps</li>
            <li>Vérifiez votre boîte de messages/SMS</li>
            <li>Cliquez sur "Renvoyer le code OTP" si nécessaire</li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
// Auto-format de l'OTP (ajout automatique des espaces)
const otpInput = document.getElementById('otp');
otpInput.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
    
    // Auto-submit si 6 chiffres
    if (value.length === 6) {
        setTimeout(() => {
            document.getElementById('otp-form').submit();
        }, 500);
    }
});

// Timer de 10 minutes
let timeLeft = 600; // 10 minutes en secondes
const timerElement = document.getElementById('timer');

function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeLeft > 0) {
        timeLeft--;
        setTimeout(updateTimer, 1000);
    } else {
        timerElement.textContent = 'Expiré';
        timerElement.classList.add('text-red-600');
        document.getElementById('verify-btn').disabled = true;
        alert('Le code OTP a expiré. Veuillez recommencer le paiement.');
    }
}

updateTimer();

// Renvoyer l'OTP
function resendOTP() {
    const resendBtn = document.getElementById('resend-btn');
    resendBtn.disabled = true;
    resendBtn.textContent = 'Envoi en cours...';
    
    // Simulation (à implémenter avec vraie API)
    setTimeout(() => {
        resendBtn.textContent = 'Code renvoyé !';
        timeLeft = 600; // Reset le timer
        setTimeout(() => {
            resendBtn.disabled = false;
            resendBtn.textContent = 'Renvoyer le code OTP';
        }, 3000);
    }, 2000);
}

// Disable submit pendant traitement
document.getElementById('otp-form').addEventListener('submit', function(e) {
    const verifyBtn = document.getElementById('verify-btn');
    verifyBtn.disabled = true;
    verifyBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
});
</script>
@endpush
@endsection
