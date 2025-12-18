@extends('app')

@section('title', 'Paiement MaishaPay')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header MaishaPay -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-mobile-alt mr-3"></i>Paiement MaishaPay
                    </h4>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-shield-alt mr-1"></i>Sécurisé
                    </span>
                </div>
                <p class="text-green-100 text-sm mt-2">
                    Paiement unifié pour tous les opérateurs Mobile Money RDC
                </p>
            </div>
            
            <div class="p-6">
                <!-- Opérateurs supportés -->
                <div class="mb-6">
                    <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>Opérateurs supportés
                    </h5>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="flex flex-col items-center p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 transition-colors">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/512px-M-PESA_LOGO-01.svg.png" alt="M-Pesa" class="w-10 h-10 object-contain mb-2">
                            <span class="text-xs text-gray-600 dark:text-gray-300">M-Pesa</span>
                            <span class="text-xs text-gray-400">81/82/83</span>
                        </div>
                        <div class="flex flex-col items-center p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-orange-500 transition-colors">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Orange_logo.svg/512px-Orange_logo.svg.png" alt="Orange" class="w-10 h-10 object-contain mb-2">
                            <span class="text-xs text-gray-600 dark:text-gray-300">Orange</span>
                            <span class="text-xs text-gray-400">84/85/89</span>
                        </div>
                        <div class="flex flex-col items-center p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-red-500 transition-colors">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Airtel_logo.svg/512px-Airtel_logo.svg.png" alt="Airtel" class="w-10 h-10 object-contain mb-2">
                            <span class="text-xs text-gray-600 dark:text-gray-300">Airtel</span>
                            <span class="text-xs text-gray-400">97/98/99</span>
                        </div>
                        <div class="flex flex-col items-center p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-500 transition-colors">
                            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/c/cf/Africell_logo.svg/512px-Africell_logo.svg.png" alt="Africell" class="w-10 h-10 object-contain mb-2">
                            <span class="text-xs text-gray-600 dark:text-gray-300">Africell</span>
                            <span class="text-xs text-gray-400">90/91</span>
                        </div>
                    </div>
                </div>

                @if(isset($cart) && !empty($cart))
                    <!-- Récapitulatif commande -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-shopping-cart mr-2 text-green-600"></i>Votre commande
                        </h5>
                        <div class="space-y-2 mb-4">
                            @foreach($cart as $item)
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                    <div class="flex items-center space-x-3">
                                        @if(isset($item['image']) && $item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}" 
                                                 alt="{{ $item['name'] }}" 
                                                 class="w-10 h-10 rounded object-cover">
                                        @endif
                                        <span class="text-gray-700 dark:text-gray-200">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        
                        @php
                            $displayTotal = isset($total) ? $total : (isset($totalInPriority) ? $totalInPriority : 0);
                            // Déterminer la devise depuis le panier ou la variable passée
                            $displayCurrency = isset($currency) ? $currency : (isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD'));
                        @endphp
                        
                        <div class="border-t-2 border-green-500 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Total à payer</span>
                                <span class="text-2xl font-bold text-green-600">
                                    {{ number_format($displayTotal, 2) }} {{ $displayCurrency }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Formulaire de paiement -->
                <form id="maishapay-form">
                    @csrf
                    
                    <!-- Numéro de téléphone -->
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-phone mr-1"></i>Numéro Mobile Money
                        </label>
                        
                        <!-- Info opérateur détecté -->
                        <div id="operator-detected" class="mb-3 hidden">
                            <div class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                                <img id="detected-logo" src="" alt="" class="w-12 h-12 rounded-full object-contain bg-white p-1 mr-3">
                                <div>
                                    <h6 id="detected-name" class="font-semibold text-green-800 dark:text-green-200"></h6>
                                    <p id="detected-status" class="text-sm text-green-600 dark:text-green-300">
                                        <i class="fas fa-check-circle mr-1"></i>Opérateur détecté
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <span class="inline-flex items-center px-4 py-3 bg-green-600 text-white border border-r-0 border-green-600 rounded-l-lg font-medium">
                                +243
                            </span>
                            <input type="tel" 
                                   class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-r-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors text-lg" 
                                   id="phone" 
                                   name="phone" 
                                   maxlength="9" 
                                   placeholder="Ex: 812345678" 
                                   required
                                   autocomplete="tel">
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            L'opérateur sera détecté automatiquement selon votre numéro
                        </p>
                        <input type="hidden" id="operator" name="operator">
                    </div>
                    
                    <!-- Montant -->
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-money-bill-wave mr-1"></i>Montant
                        </label>
                        <div class="flex">
                            <input type="number" 
                                   class="flex-1 px-4 py-3 border border-r-0 border-gray-300 rounded-l-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors text-lg font-semibold bg-gray-50 dark:bg-gray-900" 
                                   id="amount" 
                                   name="amount" 
                                   min="1" 
                                   step="0.01" 
                                   required 
                                   readonly
                                   value="{{ $displayTotal ?? $total ?? '' }}">
                            <span class="inline-flex items-center px-4 py-3 bg-gray-100 dark:bg-gray-700 border border-gray-300 rounded-r-lg font-medium text-gray-700 dark:text-gray-200">
                                {{ $displayCurrency ?? $currency ?? (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD') }}
                            </span>
                        </div>
                        <input type="hidden" name="currency" value="{{ $displayCurrency ?? $currency ?? (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD') }}">
                    </div>

                    <!-- Description -->
                    <input type="hidden" id="description" name="description" value="Paiement VintApp">
                    <input type="hidden" id="buyer_id" name="buyer_id" value="{{ Auth::id() }}">
                    @if(isset($deliveryAddress))
                        <input type="hidden" name="delivery_address_id" value="{{ $deliveryAddress->id }}">
                    @endif
                    
                    @if(isset($deliveryAddress))
                    <!-- Adresse de livraison -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                        <h6 class="font-semibold text-blue-800 dark:text-blue-200 mb-2 flex items-center">
                            <i class="fas fa-truck mr-2"></i>Livraison à
                        </h6>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>{{ $deliveryAddress->full_name }}</strong><br>
                            {{ $deliveryAddress->address }}, {{ $deliveryAddress->commune }}<br>
                            {{ $deliveryAddress->city }}<br>
                            <i class="fas fa-phone mr-1"></i>{{ $deliveryAddress->phone }}
                        </p>
                    </div>
                    @endif
                    
                    <!-- Bouton de paiement -->
                    <button type="submit" 
                            id="pay-btn"
                            class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-4 px-6 rounded-lg hover:from-green-700 hover:to-green-800 transition-all font-semibold text-lg shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>
                        Payer avec MaishaPay
                    </button>
                    
                    <!-- Sécurité -->
                    <div class="mt-4 flex items-center justify-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-shield-alt text-green-500 mr-1"></i>SSL sécurisé
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-lock text-green-500 mr-1"></i>Paiement crypté
                        </span>
                    </div>
                </form>
                
                <!-- Status du paiement -->
                <div id="payment-status" class="mt-6 hidden"></div>
            </div>
        </div>
        
        <!-- Avantages MaishaPay -->
        <div class="mt-6 grid grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
                <div class="text-3xl mb-2">⚡</div>
                <h6 class="font-semibold text-gray-900 dark:text-white">Rapide</h6>
                <p class="text-xs text-gray-500">Paiement instantané</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
                <div class="text-3xl mb-2">🔒</div>
                <h6 class="font-semibold text-gray-900 dark:text-white">Sécurisé</h6>
                <p class="text-xs text-gray-500">Cryptage SSL</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
                <div class="text-3xl mb-2">📱</div>
                <h6 class="font-semibold text-gray-900 dark:text-white">Simple</h6>
                <p class="text-xs text-gray-500">Tous opérateurs</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const buyerId = {{ Auth::id() ?? 'null' }};

// Configuration des opérateurs MaishaPay
const operators = {
    '81': { name: 'Vodacom M-Pesa', code: 'VODACOM', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/512px-M-PESA_LOGO-01.svg.png' },
    '82': { name: 'Vodacom M-Pesa', code: 'VODACOM', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/512px-M-PESA_LOGO-01.svg.png' },
    '83': { name: 'Vodacom M-Pesa', code: 'VODACOM', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/512px-M-PESA_LOGO-01.svg.png' },
    '84': { name: 'Orange Money', code: 'ORANGE', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Orange_logo.svg/512px-Orange_logo.svg.png' },
    '85': { name: 'Orange Money', code: 'ORANGE', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Orange_logo.svg/512px-Orange_logo.svg.png' },
    '89': { name: 'Orange Money', code: 'ORANGE', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Orange_logo.svg/512px-Orange_logo.svg.png' },
    '97': { name: 'Airtel Money', code: 'AIRTEL', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Airtel_logo.svg/512px-Airtel_logo.svg.png' },
    '98': { name: 'Airtel Money', code: 'AIRTEL', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Airtel_logo.svg/512px-Airtel_logo.svg.png' },
    '99': { name: 'Airtel Money', code: 'AIRTEL', logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Airtel_logo.svg/512px-Airtel_logo.svg.png' },
    '90': { name: 'Africell Money', code: 'AFRICELL', logo: 'https://upload.wikimedia.org/wikipedia/en/thumb/c/cf/Africell_logo.svg/512px-Africell_logo.svg.png' },
    '91': { name: 'Africell Money', code: 'AFRICELL', logo: 'https://upload.wikimedia.org/wikipedia/en/thumb/c/cf/Africell_logo.svg/512px-Africell_logo.svg.png' },
};

// Détection de l'opérateur
document.getElementById('phone').addEventListener('input', function(e) {
    const phone = e.target.value.replace(/\D/g, '');
    const prefix = phone.substring(0, 2);
    const operatorDetected = document.getElementById('operator-detected');
    const operatorInput = document.getElementById('operator');
    
    if (operators[prefix]) {
        const op = operators[prefix];
        operatorDetected.classList.remove('hidden');
        document.getElementById('detected-logo').src = op.logo;
        document.getElementById('detected-name').textContent = op.name;
        operatorInput.value = op.code;
        
        // Validation visuelle
        if (phone.length === 9) {
            e.target.classList.add('border-green-500', 'ring-2', 'ring-green-200');
            e.target.classList.remove('border-gray-300');
        }
    } else {
        operatorDetected.classList.add('hidden');
        operatorInput.value = '';
        e.target.classList.remove('border-green-500', 'ring-2', 'ring-green-200');
        e.target.classList.add('border-gray-300');
    }
});

// Soumission du formulaire
document.getElementById('maishapay-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const phone = document.getElementById('phone').value;
    const amount = document.getElementById('amount').value;
    const operator = document.getElementById('operator').value;
    const currency = document.querySelector('input[name="currency"]').value;
    const statusDiv = document.getElementById('payment-status');
    const submitBtn = document.getElementById('pay-btn');
    
    // Validation
    if (!operator) {
        statusDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                    <span class="text-red-800">Veuillez entrer un numéro Mobile Money valide</span>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
        return;
    }
    
    if (!buyerId) {
        statusDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                    <span class="text-red-800">Vous devez être connecté pour payer</span>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
        return;
    }
    
    // Désactiver le bouton
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement en cours...';
    
    statusDiv.innerHTML = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin mr-3 text-blue-600 text-xl"></i>
                <div>
                    <p class="font-semibold text-blue-900">Paiement en cours...</p>
                    <p class="text-sm text-blue-700">Veuillez confirmer sur votre téléphone</p>
                </div>
            </div>
        </div>
    `;
    statusDiv.classList.remove('hidden');
    
    try {
        const response = await fetch('{{ route("api.v1.payments.maishapay.initiate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phone,
                amount: parseFloat(amount),
                currency: currency,
                operator: operator,
                buyer_id: buyerId,
                description: 'Paiement VintApp'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            statusDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-600 text-xl"></i>
                        <div>
                            <p class="font-semibold text-green-900">Paiement initié!</p>
                            <p class="text-sm text-green-700">${data.message}</p>
                            <p class="text-xs text-green-600 mt-1">Réf: ${data.transaction_id}</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Redirection après succès
            setTimeout(() => {
                window.location.href = '{{ route("payments.success", ":id") }}'.replace(':id', data.transaction_id);
            }, 2000);
        } else {
            throw new Error(data.message || 'Erreur de paiement');
        }
    } catch (error) {
        statusDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-times-circle mr-3 text-red-600 text-xl"></i>
                    <div>
                        <p class="font-semibold text-red-900">Échec du paiement</p>
                        <p class="text-sm text-red-700">${error.message}</p>
                    </div>
                </div>
            </div>
        `;
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-lock mr-2"></i>Payer avec MaishaPay';
    }
});
</script>
@endpush
@endsection
