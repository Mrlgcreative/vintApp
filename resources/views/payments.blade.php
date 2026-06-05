@extends('app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- En-tête Mobile Money -->
            <div class="bg-green-600 text-white px-6 py-5 text-center">
                <h4 class="text-xl font-semibold flex items-center justify-center">
                    <i class="fas fa-mobile-alt mr-3"></i>Paiement Mobile Money
                </h4>
                <p class="text-green-100 text-sm mt-1">Tous opérateurs Mobile Money RDC</p>
                <div class="flex items-center justify-center gap-4 mt-3">
                    <img src="/images/operators/orange.png" alt="Orange" class="h-8 w-8 rounded-full bg-white p-1">
                    <img src="/images/operators/mpesa.png" alt="M-Pesa" class="h-8 w-8 rounded-full bg-white p-1">
                    <img src="/images/operators/airtel.png" alt="Airtel" class="h-8 w-8 rounded-full bg-white p-1">
                    <img src="/images/operators/africell.png" alt="Africell" class="h-8 w-8 rounded-full bg-white p-1">
                </div>
            </div>
            <div class="p-6">
                @if(isset($cart) && !empty($cart))
                    <div class="mb-6">
                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Votre commande</h5>
                        <div class="space-y-3 mb-4">
                            @foreach($cart as $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        @if(isset($item['image']) && $item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}" 
                                                 alt="{{ $item['name'] }}" 
                                                 class="w-8 h-8 rounded object-cover">
                                        @endif
                                        <span class="text-gray-900 dark:text-white">{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        @php
                            // Déterminer la devise prioritaire (la plus fréquente)
                            $currencyCounts = [];
                            foreach($cart as $item) {
                                $currency = $item['currency'] ?? 'USD';
                                $currencyCounts[$currency] = ($currencyCounts[$currency] ?? 0) + 1;
                            }
                            arsort($currencyCounts);
                            $priorityCurrency = array_key_first($currencyCounts);
                            
                            // Calculer le sous-total dans la devise prioritaire
                            $exchangeRate = 2650; // Taux par défaut, sera récupéré via API
                            $subtotalInPriority = 0;
                            
                            foreach($cart as $item) {
                                $itemTotal = $item['price'] * $item['quantity'];
                                $itemCurrency = $item['currency'] ?? 'USD';
                                
                                if ($itemCurrency !== $priorityCurrency) {
                                    if ($priorityCurrency === 'USD' && $itemCurrency === 'CDF') {
                                        $itemTotal = $itemTotal / $exchangeRate;
                                    } elseif ($priorityCurrency === 'CDF' && $itemCurrency === 'USD') {
                                        $itemTotal = $itemTotal * $exchangeRate;
                                    }
                                }
                                
                                $subtotalInPriority += $itemTotal;
                            }
                            
                            $subtotalInPriority = round($subtotalInPriority, 2);
                            
                            // Calculer les frais de transport dans la devise prioritaire
                            $transportFeeInPriority = ($subtotalInPriority * $transportFeePercentage) / 100;
                            $transportFeeInPriority = round($transportFeeInPriority, 2);
                            
                            // Total final
                            $totalInPriority = $subtotalInPriority + $transportFeeInPriority;
                        @endphp
                        
                        <!-- Récapitulatif des montants -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-gray-700 dark:text-gray-200">Sous-total :</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($subtotalInPriority, 2) }} {{ $priorityCurrency }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-gray-700 dark:text-gray-200">
                                    Frais de livraison :
                                    <span class="text-sm text-gray-500 dark:text-gray-400">({{ $transportFeePercentage }}%)</span>
                                </span>
                                <span class="font-semibold text-blue-600">+{{ number_format($transportFeeInPriority, 2) }} {{ $priorityCurrency }}</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-semibold text-gray-900 dark:text-white">Total à payer :</span>
                                    <span class="text-xl font-bold text-green-600">{{ number_format($totalInPriority, 2) }} {{ $priorityCurrency }}</span>
                                </div>
                                @if(count($currencyCounts) > 1)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i> Devises mixtes converties en {{ $priorityCurrency }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                
                <form id="payment-form">
                    @csrf
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Numéro Mobile Money</label>
                        <div id="operator-info" class="mb-4 hidden">
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="w-16 h-16 rounded-full overflow-hidden shadow-sm bg-white dark:bg-gray-800 mr-4 flex-shrink-0">
                                    <img id="operator-logo" src="" alt="" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h5 id="operator-name" class="font-semibold text-gray-900 dark:text-white mb-1"></h5>
                                    <p id="operator-format" class="text-sm text-gray-600 dark:text-gray-300 mb-2"></p>
                                    <div class="flex items-center">
                                        <i class="fas fa-signal text-green-500 mr-2"></i>
                                        <span class="text-sm text-green-600">Réseau disponible</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-r-0 border-gray-300 rounded-l-lg text-sm font-medium text-gray-700 dark:text-gray-200">+243</span>
                            <input type="tel" 
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                   id="phone" 
                                   name="phone" 
                                   maxlength="9" 
                                   placeholder="Ex: 850123456" 
                                   required>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Entrez votre numéro Mobile Money (Orange: 84/85, M-Pesa: 81/82, Airtel: 97/99, Africell: 90-93)
                        </p>
                        <input type="hidden" id="provider" name="provider">
                    </div>
                    
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Montant à payer</label>
                        <div class="flex">
                            <input type="number" 
                                   class="flex-1 px-3 py-2 border border-r-0 border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors bg-gray-50 dark:bg-gray-900" 
                                   id="amount" 
                                   name="amount" 
                                   min="1" 
                                   step="0.01" 
                                   required 
                                   readonly
                                   value="{{ isset($totalInPriority) ? $totalInPriority : (isset($total) ? $total : '') }}">
                            <span class="inline-flex items-center px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 rounded-r-lg text-sm font-medium text-gray-700 dark:text-gray-200">{{ isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD') }}</span>
                        </div>
                        @php
                            $displayCurrency = isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD');
                        @endphp
                        @if($displayCurrency === 'USD')
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                Environ <span id="amount-cdf" class="font-medium">0</span> CDF (1 USD = <span id="rate-display">2650</span> CDF)
                            </p>
                        @elseif($displayCurrency === 'CDF')
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                Environ <span id="amount-usd" class="font-medium">0</span> USD (1 USD = <span id="rate-display">2650</span> CDF)
                            </p>
                        @endif
                    </div>
                    
                    <input type="hidden" id="purpose" name="purpose" value="Paiement commande">
                    
                    <button type="submit" 
                            class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors font-medium text-lg">
                        <i class="fas fa-paper-plane mr-2"></i>Payer maintenant
                    </button>
                </form>
                
                <div id="payment-status" class="mt-6 hidden"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const buyerId = {{ Auth::id() !== null ? Auth::id() : 'null' }};

// Récupérer la devise du produit (devise prioritaire du panier)
const productCurrency = '{{ isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]["currency"] ?? "USD") : "USD") }}';

// Taux de change (sera récupéré dynamiquement)
let exchangeRate = 2650; // Valeur par défaut

// Récupérer le taux de change en temps réel
fetch('/exchange/rate')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            exchangeRate = data.rate;
            console.log('Taux de change USD/CDF:', exchangeRate);
            
            // Mettre à jour l'affichage du taux
            const rateDisplayElements = document.querySelectorAll('#rate-display');
            rateDisplayElements.forEach(element => {
                element.textContent = exchangeRate.toLocaleString('fr-FR');
            });
            
            // Mettre à jour la conversion affichée
            updateCurrencyConversion();
        }
    })
    .catch(error => {
        console.error('Erreur récupération taux:', error);
    });

// Configuration des opérateurs
const operators = {
    '84': { 
        name: 'Orange Money', 
        provider: 'Orange Money',
        logo: '/images/operators/orange.png',
        pattern: '^8[45][0-9]{7}$',
        format: '84XXXXXXX ou 85XXXXXXX'
    },
    '85': { 
        name: 'Orange Money', 
        provider: 'Orange Money',
        logo: '/images/operators/orange.png',
        pattern: '^8[45][0-9]{7}$',
        format: '84XXXXXXX ou 85XXXXXXX'
    },
    '89': { 
        name: 'Orange Money', 
        provider: 'Orange Money',
        logo: '/images/operators/orange.png',
        pattern: '^8[45][0-9]{7}$',
        format: '84XXXXXXX ou 85XXXXXXX'
    },
    '81': { 
        name: 'Vodacom M-Pesa', 
        provider: 'Vodacom M-Pesa',
        logo: '/images/operators/mpesa.png',
        pattern: '^8[12][0-9]{7}$',
        format: '81XXXXXXX ou 82XXXXXXX'
    },
    '83': { 
        name: 'Vodacom M-Pesa', 
        provider: 'Vodacom M-Pesa',
        logo: '/images/operators/mpesa.png',
        pattern: '^8[12][0-9]{7}$',
        format: '81XXXXXXX ou 82XXXXXXX'
    },
    '82': { 
        name: 'Vodacom M-Pesa', 
        provider: 'Vodacom M-Pesa',
        logo: '/images/operators/mpesa.png',
        pattern: '^8[12][0-9]{7}$',
        format: '81XXXXXXX ou 82XXXXXXX'
    },
    '97': { 
        name: 'Airtel Money', 
        provider: 'Airtel Money',
        logo: '/images/operators/airtel.png',
        pattern: '^9[79][0-9]{7}$',
        format: '97XXXXXXX ou 99XXXXXXX'
    },
    '98': { 
        name: 'Airtel Money', 
        provider: 'Airtel Money',
        logo: '/images/operators/airtel.png',
        pattern: '^9[79][0-9]{7}$',
        format: '97XXXXXXX ou 99XXXXXXX'
    },
    '99': { 
        name: 'Airtel Money', 
        provider: 'Airtel Money',
        logo: '/images/operators/airtel.png',
        pattern: '^9[79][0-9]{7}$',
        format: '97XXXXXXX ou 99XXXXXXX'
    },
    '90': { 
        name: 'Africell Money', 
        provider: 'Africell Money',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '91': { 
        name: 'Africell Money', 
        provider: 'Africell Money',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '92': { 
        name: 'Africell Money', 
        provider: 'Africell Money',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '93': { 
        name: 'Africell Money', 
        provider: 'Africell Money',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    }
};

// Conversion de devise en temps réel (USD <-> CDF)
function updateCurrencyConversion() {
    const amountInput = document.getElementById('amount');
    const amount = parseFloat(amountInput.value) || 0;
    
    if (productCurrency === 'USD') {
        // Si le produit est en USD, afficher l'équivalent en CDF
        const amountCDF = Math.round(amount * exchangeRate);
        const cdfElement = document.getElementById('amount-cdf');
        if (cdfElement) {
            cdfElement.textContent = amountCDF.toLocaleString('fr-FR');
        }
    } else if (productCurrency === 'CDF') {
        // Si le produit est en CDF, afficher l'équivalent en USD
        const amountUSD = (amount / exchangeRate).toFixed(2);
        const usdElement = document.getElementById('amount-usd');
        if (usdElement) {
            usdElement.textContent = parseFloat(amountUSD).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}

// Calculer la conversion au chargement
updateCurrencyConversion();

// Détecter l'opérateur à partir du numéro
document.getElementById('phone').addEventListener('input', function(e) {
    const prefix = e.target.value.substring(0, 2);
    const operatorInfo = document.getElementById('operator-info');
    const providerInput = document.getElementById('provider');
    const operatorLogo = document.getElementById('operator-logo');
    const operatorName = document.getElementById('operator-name');
    const operatorFormat = document.getElementById('operator-format');
    
    if (operators[prefix]) {
        // Afficher les informations de l'opérateur avec animation
        operatorInfo.style.opacity = '0';
        operatorInfo.classList.remove('hidden');
        setTimeout(() => {
            operatorInfo.style.transition = 'opacity 0.3s ease-in-out';
            operatorInfo.style.opacity = '1';
        }, 50);
        
        operatorLogo.src = operators[prefix].logo;
        operatorLogo.alt = operators[prefix].name;
        operatorName.textContent = operators[prefix].name;
        operatorFormat.textContent = operators[prefix].format;
        providerInput.value = operators[prefix].provider;

        // Mettre à jour automatiquement le motif du paiement
        const purposeInput = document.getElementById('purpose');
        const amount = document.getElementById('amount').value;
        purposeInput.value = `Paiement ${operators[prefix].name} - ${amount ? amount + ' USD' : ''}`;
        
        // Valider le format du numéro
        if (new RegExp(operators[prefix].pattern).test(e.target.value)) {
            e.target.classList.remove('border-red-300', 'ring-red-500');
            e.target.classList.add('border-green-300', 'ring-green-500');
        } else {
            e.target.classList.remove('border-green-300', 'ring-green-500');
            if (e.target.value.length === 9) {
                e.target.classList.add('border-red-300', 'ring-red-500');
            }
        }
    } else {
        // Cacher les informations de l'opérateur si le préfixe n'est pas reconnu
        operatorInfo.classList.add('hidden');
        providerInput.value = '';
        e.target.classList.remove('border-green-300', 'ring-green-500', 'border-red-300', 'ring-red-500');
    }
});

// Gestion de la soumission du formulaire
document.getElementById('payment-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    console.log('Formulaire soumis !'); // Debug
    
    // Récupération des valeurs du formulaire
    const provider = document.getElementById('provider').value;
    const amount = document.getElementById('amount').value;
    const phone = document.getElementById('phone').value;
    const purpose = document.getElementById('purpose').value;
    
    console.log('Données:', { provider, amount, phone, purpose, buyerId }); // Debug
    
    // Éléments d'interface
    const statusDiv = document.getElementById('payment-status');
    const submitButton = this.querySelector('button[type="submit"]');
    
    // Validation
    if (!provider) {
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
    
    if (!amount || amount <= 0) {
        statusDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                    <span class="text-red-800">Veuillez entrer un montant valide</span>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
        return;
    }
    
    if (!phone || phone.length !== 9) {
        statusDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                    <span class="text-red-800">Veuillez entrer un numéro de téléphone valide (9 chiffres)</span>
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
                    <span class="text-red-800">Vous devez être connecté pour effectuer un paiement</span>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
        return;
    }

    try {
        // Désactiver le bouton et afficher l'indicateur de chargement
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement en cours...';
        submitButton.className = 'w-full bg-gray-400 text-white py-3 px-6 rounded-lg cursor-not-allowed font-medium text-lg';
        
        statusDiv.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-semibold text-blue-900 mb-1">Transaction en cours</h5>
                        <p class="text-blue-800 mb-1">Veuillez patienter pendant le traitement de votre paiement...</p>
                        <p class="text-sm text-blue-600">Cela peut prendre quelques secondes</p>
                    </div>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');

        console.log('Envoi de la requête...'); // Debug

        // Router vers l'API du fournisseur sélectionné
        const providerRoutes = {
            'Orange Money': '{{ route("payments.orange_money") }}',
            'Vodacom M-Pesa': '{{ route("payments.mpesa") }}',
            'Airtel Money': '{{ route("payments.airtel_money") }}',
            'Africell Money': '{{ route("payments.africell") }}',
        };
        const apiRoute = providerRoutes[provider] || '{{ route("payments.orange_money") }}';

        const response = await fetch(apiRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                provider: provider,
                amount: parseFloat(amount),
                currency: productCurrency,
                phone: phone,
                purpose: purpose,
                buyer_id: buyerId
            })
        });

        const data = await response.json();

        if (response.ok && data.status === 'success') {
            // Rediriger vers la page de succès avec l'ID de transaction
            console.log('Redirection vers success'); // Debug
            window.location.href = '{{ route("payments.success", ":transaction_id") }}'.replace(':transaction_id', data.transaction_id);
        } else {
            // Rediriger vers la page d'erreur avec les détails
            console.log('Redirection vers error'); // Debug
            const errorParams = new URLSearchParams({
                error: data.message || 'Une erreur est survenue',
                amount: amount,
                provider: provider,
                currency: productCurrency
            });
            window.location.href = '{{ route("payments.error") }}?' + errorParams.toString();
        }
    } catch (error) {
        console.error('Erreur lors du paiement:', error);
        statusDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-semibold text-red-900 mb-1">Erreur de paiement</h5>
                        <p class="text-red-800 mb-1">Une erreur est survenue lors du traitement de votre paiement. Veuillez réessayer.</p>
                        <p class="text-sm text-red-600">Erreur: ${error.message}</p>
                    </div>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
    } finally {
        // Réactiver le bouton et restaurer son texte original
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Payer maintenant';
        submitButton.className = 'w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors font-medium text-lg';
    }
});
</script>
@endpush
@endsection
