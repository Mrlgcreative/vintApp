@extends('app')

@section('title', 'Paiement MaishaPay')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header MaishaPay -->
            <div class="bg-gradient-to-r from-green-600 via-green-500 to-emerald-600 text-white px-6 py-6 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 400 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="40" fill="white"/>
                        <circle cx="150" cy="30" r="30" fill="white"/>
                        <circle cx="280" cy="60" r="50" fill="white"/>
                        <circle cx="380" cy="20" r="25" fill="white"/>
                    </svg>
                </div>
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <h4 class="text-xl font-bold flex items-center">
                            <span class="bg-white/20 p-2 rounded-lg mr-3">
                                <i class="fas fa-mobile-alt text-white"></i>
                            </span>
                            Paiement Mobile Money
                        </h4>
                        <p class="text-green-100 text-sm mt-1">
                            Payez en toute sécurité depuis votre téléphone
                        </p>
                    </div>
                    <span class="bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-medium flex items-center">
                        <i class="fas fa-shield-alt mr-1.5"></i>Sécurisé
                    </span>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <!-- Opérateurs supportés -->
                <div class="mb-8">
                    <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 flex items-center">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>
                        Opérateurs acceptés
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="operator-card group cursor-pointer" data-operator="VODACOM">
                            <div class="relative p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-750 rounded-xl border-2 border-transparent hover:border-green-500 transition-all duration-300 text-center hover:shadow-lg hover:-translate-y-0.5">
                                <div class="w-14 h-14 mx-auto mb-2.5 p-1.5 bg-white rounded-xl shadow-sm flex items-center justify-center">
                                    <img src="{{ asset('images/operators/mpesa.jpeg') }}" alt="M-Pesa" class="w-full h-full object-contain">
                                </div>
                                <span class="block text-xs font-semibold text-gray-700 dark:text-gray-200">M-Pesa</span>
                                <span class="block text-[10px] text-gray-400 mt-0.5">81/82/83</span>
                            </div>
                        </div>
                        <div class="operator-card group cursor-pointer" data-operator="ORANGE">
                            <div class="relative p-4 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-gray-800 dark:to-gray-750 rounded-xl border-2 border-transparent hover:border-orange-500 transition-all duration-300 text-center hover:shadow-lg hover:-translate-y-0.5">
                                <div class="w-14 h-14 mx-auto mb-2.5 p-1.5 bg-white rounded-xl shadow-sm flex items-center justify-center">
                                    <img src="{{ asset('images/operators/orange.jpeg') }}" alt="Orange Money" class="w-full h-full object-contain">
                                </div>
                                <span class="block text-xs font-semibold text-orange-600 dark:text-orange-400">Orange Money</span>
                                <span class="block text-[10px] text-gray-400 mt-0.5">84/85/89</span>
                            </div>
                        </div>
                        <div class="operator-card group cursor-pointer" data-operator="AIRTEL">
                            <div class="relative p-4 bg-gradient-to-br from-red-50 to-red-100 dark:from-gray-800 dark:to-gray-750 rounded-xl border-2 border-transparent hover:border-red-500 transition-all duration-300 text-center hover:shadow-lg hover:-translate-y-0.5">
                                <div class="w-14 h-14 mx-auto mb-2.5 p-1.5 bg-white rounded-xl shadow-sm flex items-center justify-center">
                                    <img src="{{ asset('images/operators/airtel.jpeg') }}" alt="Airtel Money" class="w-full h-full object-contain">
                                </div>
                                <span class="block text-xs font-semibold text-red-600 dark:text-red-400">Airtel Money</span>
                                <span class="block text-[10px] text-gray-400 mt-0.5">97/98/99</span>
                            </div>
                        </div>
                        <div class="operator-card group cursor-pointer" data-operator="AFRICELL">
                            <div class="relative p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-750 rounded-xl border-2 border-transparent hover:border-blue-500 transition-all duration-300 text-center hover:shadow-lg hover:-translate-y-0.5">
                                <div class="w-14 h-14 mx-auto mb-2.5 p-1.5 bg-white rounded-xl shadow-sm flex items-center justify-center">
                                    <img src="{{ asset('images/operators/africell.jpeg') }}" alt="Africell Money" class="w-full h-full object-contain">
                                </div>
                                <span class="block text-xs font-semibold text-blue-600 dark:text-blue-400">Africell Money</span>
                                <span class="block text-[10px] text-gray-400 mt-0.5">90/91</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($cart) && !empty($cart))
                    <!-- Récapitulatif commande -->
                    <div class="mb-8 p-5 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-750 rounded-xl border border-gray-200 dark:border-gray-700">
                        <h5 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <span class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-shopping-cart text-green-600 dark:text-green-400 text-sm"></i>
                            </span>
                            Votre commande
                        </h5>
                        <div class="space-y-2 mb-4">
                            @foreach($cart as $item)
                                <div class="flex items-center justify-between py-2.5 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        @if(isset($item['image']) && $item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}" 
                                                 alt="{{ $item['name'] }}" 
                                                 class="w-11 h-11 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-11 h-11 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 block truncate">{{ $item['name'] }}</span>
                                            <span class="text-xs text-gray-400">x{{ $item['quantity'] }}</span>
                                        </div>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-white text-sm flex-shrink-0 ml-3">
                                        {{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @php
                            $displayTotal = isset($total) ? $total : (isset($totalInPriority) ? $totalInPriority : 0);
                            $displayCurrency = isset($currency) ? $currency : (isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD'));
                        @endphp

                        <div class="border-t-2 border-green-500 pt-4 mt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-gray-900 dark:text-white">Total à payer</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($displayTotal, 2) }} {{ $displayCurrency }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-info-circle text-amber-500 mt-0.5 flex-shrink-0"></i>
                                <p class="text-xs text-amber-700 dark:text-amber-300">
                                    <strong>Note :</strong> Des frais de transaction de l'opérateur mobile money (environ 3-4%) peuvent s'ajouter.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Formulaire de paiement -->
                <form id="maishapay-form">
                    @csrf

                    <!-- Numéro de téléphone -->
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-phone mr-1.5"></i>Numéro Mobile Money
                        </label>

                        <!-- Info opérateur détecté -->
                        <div id="operator-detected" class="mb-3 hidden animate-fade-in">
                            <div class="flex items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
                                <div class="w-12 h-12 rounded-xl bg-white p-1.5 shadow-sm mr-3 flex items-center justify-center flex-shrink-0">
                                    <img id="detected-logo" src="" alt="" class="w-full h-full object-contain">
                                </div>
                                <div class="min-w-0">
                                    <h6 id="detected-name" class="font-semibold text-green-800 dark:text-green-200 text-sm"></h6>
                                    <p class="text-xs text-green-600 dark:text-green-300 flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i>Opérateur détecté
                                    </p>
                                </div>
                                <span class="ml-auto text-xs text-green-500 bg-white dark:bg-green-900/50 px-2 py-0.5 rounded-full font-medium">
                                    ✓ Valide
                                </span>
                            </div>
                        </div>

                        <div class="flex">
                            <span class="inline-flex items-center px-4 py-3.5 bg-gradient-to-b from-green-600 to-green-700 text-white border border-r-0 border-green-600 rounded-l-xl font-medium text-sm shadow-sm">
                                +243
                            </span>
                            <input type="tel" 
                                   class="flex-1 px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-r-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-lg tracking-wider bg-white dark:bg-gray-900" 
                                   id="phone" 
                                   name="phone" 
                                   maxlength="13" 
                                   placeholder="0XX XX XX XXX" 
                                   required
                                   autocomplete="tel">
                        </div>
                        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1.5">
                            <i class="fas fa-info-circle"></i>
                            L'opérateur est détecté automatiquement selon votre numéro
                        </p>
                        <input type="hidden" id="operator" name="operator">
                    </div>

                    <!-- Montant -->
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-money-bill-wave mr-1.5"></i>Montant
                        </label>
                        <div class="flex">
                            <input type="number" 
                                   class="flex-1 px-4 py-3.5 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-lg font-bold bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white" 
                                   id="amount" 
                                   name="amount" 
                                   min="1" 
                                   step="0.01" 
                                   required 
                                   readonly
                                   value="{{ $displayTotal ?? $total ?? '' }}">
                            <span class="inline-flex items-center px-4 py-3.5 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-r-xl font-medium text-sm text-gray-700 dark:text-gray-200 shadow-sm">
                                {{ $displayCurrency ?? $currency ?? (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD') }}
                            </span>
                        </div>
                        <input type="hidden" name="currency" value="{{ $displayCurrency ?? $currency ?? (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD') }}">
                    </div>

                    <input type="hidden" id="description" name="description" value="Paiement VintApp">
                    <input type="hidden" id="buyer_id" name="buyer_id" value="{{ Auth::id() }}">
                    @if(isset($deliveryAddress))
                        <input type="hidden" name="delivery_address_id" value="{{ $deliveryAddress->id }}">
                    @endif

                    @if(isset($deliveryAddress))
                    <!-- Adresse de livraison -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-truck text-blue-600 dark:text-blue-400 text-sm"></i>
                            </div>
                            <div>
                                <h6 class="font-semibold text-blue-800 dark:text-blue-200 text-sm">Livraison à</h6>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-0.5">
                                    <strong>{{ $deliveryAddress->full_name }}</strong><br>
                                    {{ $deliveryAddress->address }}, {{ $deliveryAddress->commune }}<br>
                                    {{ $deliveryAddress->city }}<br>
                                    <i class="fas fa-phone mr-1"></i>{{ $deliveryAddress->phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Bouton de paiement -->
                    <button type="submit" 
                            id="pay-btn"
                            class="w-full bg-gradient-to-r from-green-600 via-green-500 to-emerald-600 text-white py-4 px-6 rounded-xl hover:from-green-700 hover:via-green-600 hover:to-emerald-700 transition-all font-bold text-base shadow-lg hover:shadow-xl active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Payer avec MaishaPay
                    </button>

                    <!-- Sécurité -->
                    <div class="mt-4 flex items-center justify-center gap-5 text-xs text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-shield-alt text-green-500"></i>SSL sécurisé
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-lock text-green-500"></i>Paiement crypté
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-clock text-green-500"></i>Instantanné
                        </span>
                    </div>
                </form>

                <!-- Status du paiement -->
                <div id="payment-status" class="mt-6 hidden"></div>
            </div>
        </div>

        <!-- Avantages MaishaPay -->
        <div class="mt-6 grid grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                    <span class="text-xl">⚡</span>
                </div>
                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Rapide</h6>
                <p class="text-[11px] text-gray-500 mt-0.5">Paiement instantané</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                    <span class="text-xl">🔒</span>
                </div>
                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Sécurisé</h6>
                <p class="text-[11px] text-gray-500 mt-0.5">Cryptage SSL</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                    <span class="text-xl">📱</span>
                </div>
                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Simple</h6>
                <p class="text-[11px] text-gray-500 mt-0.5">Tous opérateurs</p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
.operator-card {
    transition: transform 0.2s ease;
}
.operator-card:active {
    transform: scale(0.96);
}
#phone::placeholder {
    letter-spacing: normal;
}
input[type="tel"]:focus::placeholder {
    opacity: 0.5;
}
</style>

@push('scripts')
<script>
const buyerId = {{ Auth::id() ?? 'null' }};

const operators = {
    '81': { name: 'Vodacom M-Pesa', code: 'VODACOM', logo: '{{ asset("images/operators/mpesa.jpeg") }}' },
    '82': { name: 'Vodacom M-Pesa', code: 'VODACOM', logo: '{{ asset("images/operators/mpesa.jpeg") }}' },
    '83': { name: 'Vodacom M-Pesa', code: 'VODACOM', logo: '{{ asset("images/operators/mpesa.jpeg") }}' },
    '84': { name: 'Orange Money', code: 'ORANGE', logo: '{{ asset("images/operators/orange.jpeg") }}' },
    '85': { name: 'Orange Money', code: 'ORANGE', logo: '{{ asset("images/operators/orange.jpeg") }}' },
    '89': { name: 'Orange Money', code: 'ORANGE', logo: '{{ asset("images/operators/orange.jpeg") }}' },
    '97': { name: 'Airtel Money', code: 'AIRTEL', logo: '{{ asset("images/operators/airtel.jpeg") }}' },
    '98': { name: 'Airtel Money', code: 'AIRTEL', logo: '{{ asset("images/operators/airtel.jpeg") }}' },
    '99': { name: 'Airtel Money', code: 'AIRTEL', logo: '{{ asset("images/operators/airtel.jpeg") }}' },
    '90': { name: 'Africell Money', code: 'AFRICELL', logo: '{{ asset("images/operators/africell.jpeg") }}' },
    '91': { name: 'Africell Money', code: 'AFRICELL', logo: '{{ asset("images/operators/africell.jpeg") }}' },
};

function formatPhoneInput(value) {
    const digits = value.replace(/\D/g, '').substring(0, 10);
    if (digits.length > 7) {
        return digits.substring(0, 3) + ' ' + digits.substring(3, 5) + ' ' + digits.substring(5, 7) + ' ' + digits.substring(7);
    } else if (digits.length > 5) {
        return digits.substring(0, 3) + ' ' + digits.substring(3, 5) + ' ' + digits.substring(5);
    } else if (digits.length > 3) {
        return digits.substring(0, 3) + ' ' + digits.substring(3);
    }
    return digits;
}

document.getElementById('phone').addEventListener('input', function(e) {
    const raw = e.target.value.replace(/\D/g, '');
    const digits = raw.substring(0, 10);
    const formatted = formatPhoneInput(digits);
    e.target.value = formatted;

    const prefix = digits.length >= 3 ? digits.substring(1, 3) : '';
    const operatorDetected = document.getElementById('operator-detected');
    const operatorInput = document.getElementById('operator');

    if (prefix && operators[prefix]) {
        const op = operators[prefix];
        operatorDetected.classList.remove('hidden');
        document.getElementById('detected-logo').src = op.logo;
        document.getElementById('detected-name').textContent = op.name;
        operatorInput.value = op.code;

        if (digits.length === 10) {
            e.target.classList.add('border-green-500', 'ring-2', 'ring-green-200');
            e.target.classList.remove('border-gray-300', 'dark:border-gray-600');
        }
    } else {
        operatorDetected.classList.add('hidden');
        operatorInput.value = '';
        e.target.classList.remove('border-green-500', 'ring-2', 'ring-green-200');
        e.target.classList.add('border-gray-300', 'dark:border-gray-600');
    }
});

document.querySelectorAll('.operator-card').forEach(card => {
    card.addEventListener('click', function() {
        const code = this.dataset.operator;
        const phoneInput = document.getElementById('phone');
        phoneInput.focus();
    });
});

document.getElementById('maishapay-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const phone = document.getElementById('phone').value.replace(/\s/g, '');
    const amount = document.getElementById('amount').value;
    const operator = document.getElementById('operator').value;
    const currency = document.querySelector('input[name="currency"]').value;
    const statusDiv = document.getElementById('payment-status');
    const submitBtn = document.getElementById('pay-btn');

    if (!operator) {
        statusDiv.innerHTML = `
            <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 animate-fade-in">
                <div class="w-9 h-9 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-red-800 dark:text-red-200 text-sm">Numéro invalide</p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">Veuillez entrer un numéro Mobile Money valide (ex: 097 12 34 567)</p>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
        return;
    }

    if (!buyerId) {
        statusDiv.innerHTML = `
            <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 animate-fade-in">
                <div class="w-9 h-9 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-red-800 dark:text-red-200 text-sm">Non connecté</p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">Connectez-vous pour effectuer un paiement</p>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Traitement en cours...</span>';

    statusDiv.innerHTML = `
        <div class="flex items-start gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 animate-fade-in">
            <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-spinner fa-spin text-blue-600"></i>
            </div>
            <div>
                <p class="font-semibold text-blue-800 dark:text-blue-200 text-sm">Paiement en cours...</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Veuillez confirmer sur votre téléphone</p>
            </div>
        </div>
    `;
    statusDiv.classList.remove('hidden');

    try {
        const response = await fetch('{{ route("payments.maishapay.initiate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phone.replace(/^0/, ''),
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
                <div class="flex items-start gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 animate-fade-in">
                    <div class="w-9 h-9 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-green-800 dark:text-green-200 text-sm">Paiement initié avec succès !</p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">${data.message}</p>
                        <p class="text-[11px] text-green-500 dark:text-green-500 mt-1.5 font-mono bg-white dark:bg-green-900/30 inline-block px-2 py-0.5 rounded">
                            Réf: ${data.transaction_id}
                        </p>
                    </div>
                </div>
            `;

            submitBtn.innerHTML = '<i class="fas fa-hourglass-half"></i><span>En attente de confirmation...</span>';
            submitBtn.disabled = true;

            setTimeout(() => {
                window.location.href = '{{ route("payments.status", ":id") }}'.replace(':id', data.transaction_id);
            }, 2000);
        } else {
            throw new Error(data.message || 'Erreur de paiement');
        }
    } catch (error) {
        statusDiv.innerHTML = `
            <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 animate-fade-in">
                <div class="w-9 h-9 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times-circle text-red-600 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-red-800 dark:text-red-200 text-sm">Échec du paiement</p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">${error.message}</p>
                </div>
            </div>
        `;

        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-lock"></i><span>Payer avec MaishaPay</span>';
    }
});
</script>
@endpush
@endsection
