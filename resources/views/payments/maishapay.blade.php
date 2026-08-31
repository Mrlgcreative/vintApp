@extends('app')

@section('title', 'Paiement MaishaPay')

@section('content')
@php
    $displayTotal = isset($total) ? $total : (isset($totalInPriority) ? $totalInPriority : 0);
    $displayCurrency = isset($currency) ? $currency : (isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD'));
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form id="maishapay-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Colonne gauche : sélection & saisie --}}
            <div class="lg:col-span-7 space-y-6">
                {{-- En-tête --}}
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <span class="bg-emerald-600 dark:bg-emerald-700 p-2.5 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-mobile-alt text-white"></i>
                        </span>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">Paiement Mobile Money</h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Payez en toute sécurité depuis votre téléphone</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-800 px-3 py-1.5 rounded-full text-xs font-medium text-emerald-700 dark:text-emerald-300">
                        <i class="fas fa-shield-alt"></i>Sécurisé
                    </span>
                </div>

                {{-- Opérateurs --}}
                <x-card>
                    <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
                        <h5 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                            Choisissez votre opérateur
                        </h5>
                    </div>
                    <div class="p-5 md:p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @php
                                $operators = [
                                    ['op'=>'VODACOM','img'=>'mpesa.jpeg','name'=>'M-Pesa','color'=>'text-emerald-600 dark:text-emerald-400','hover'=>'hover:border-emerald-500'],
                                    ['op'=>'ORANGE','img'=>'orange.jpeg','name'=>'Orange Money','color'=>'text-orange-600 dark:text-orange-400','hover'=>'hover:border-orange-500'],
                                    ['op'=>'AIRTEL','img'=>'airtel.jpeg','name'=>'Airtel Money','color'=>'text-red-600 dark:text-red-400','hover'=>'hover:border-red-500'],
                                    ['op'=>'AFRICELL','img'=>'africell.jpeg','name'=>'Africell Money','color'=>'text-blue-600 dark:text-blue-400','hover'=>'hover:border-blue-500'],
                                ];
                            @endphp
                            @foreach($operators as $op)
                            <div class="operator-card group cursor-pointer" data-operator="{{ $op['op'] }}">
                                <div class="relative p-4 bg-gray-50 dark:bg-gray-800/60 rounded-xl border-2 border-gray-100 dark:border-gray-700 hover:shadow-md {{ $op['hover'] }} transition-all duration-200 text-center hover:-translate-y-0.5">
                                    <div class="w-14 h-14 mx-auto mb-2.5 p-1.5 bg-white rounded-xl shadow-sm flex items-center justify-center">
                                        <img src="{{ asset('images/operators/' . $op['img']) }}" alt="{{ $op['name'] }}" class="w-full h-full object-contain">
                                    </div>
                                    <span class="block text-xs font-semibold {{ $op['color'] }}">{{ $op['name'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </x-card>

                {{-- Détails du paiement --}}
                <x-card>
                    <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
                        <h5 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                            Détails du paiement
                        </h5>
                    </div>
                    <div class="p-5 md:p-6 space-y-6">
                        {{-- Numéro de téléphone --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                                <i class="fas fa-phone text-emerald-500 mr-1"></i>Numéro Mobile Money
                            </label>

                            <div id="operator-detected" class="mb-3 hidden animate-fade-in">
                                <div class="flex items-center p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
                                    <div class="w-12 h-12 rounded-xl bg-white p-1.5 shadow-sm mr-3 flex items-center justify-center flex-shrink-0">
                                        <img id="detected-logo" src="" alt="" class="w-full h-full object-contain">
                                    </div>
                                    <div class="min-w-0">
                                        <h6 id="detected-name" class="font-semibold text-emerald-800 dark:text-emerald-200 text-sm"></h6>
                                        <p class="text-xs text-emerald-600 dark:text-emerald-300 flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i>Opérateur détecté
                                        </p>
                                    </div>
                                    <span class="ml-auto text-xs text-emerald-500 bg-white dark:bg-emerald-900/50 px-2 py-0.5 rounded-full font-medium">
                                        ✓ Valide
                                    </span>
                                </div>
                            </div>

                            <div class="flex shadow-sm">
                                <span class="inline-flex items-center px-4 py-3.5 bg-emerald-600 text-white border border-r-0 border-emerald-600 rounded-l-xl font-medium text-sm">
                                    +243
                                </span>
                                <input type="tel"
                                       class="flex-1 px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-r-xl focus:ring-2 focus:ring-emerald-500/40 focus:border-transparent transition-all text-lg tracking-wider bg-white dark:bg-gray-900 outline-none"
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

                        {{-- Montant --}}
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                                <i class="fas fa-money-bill-wave text-emerald-500 mr-1"></i>Montant
                            </label>
                            <div class="flex shadow-sm">
                                <input type="number"
                                       class="flex-1 px-4 py-3.5 border border-r-0 border-gray-300 dark:border-gray-700 rounded-l-xl focus:ring-2 focus:ring-emerald-500/40 focus:border-transparent transition-all text-lg font-bold bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none"
                                       id="amount"
                                       name="amount"
                                       min="1"
                                       step="0.01"
                                       required
                                       readonly
                                       value="{{ $displayTotal }}">
                                <span class="inline-flex items-center px-4 py-3.5 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 rounded-r-xl font-medium text-sm text-gray-700 dark:text-gray-200">
                                    {{ $displayCurrency }}
                                </span>
                            </div>
                            <input type="hidden" name="currency" value="{{ $displayCurrency }}">
                        </div>

                        <input type="hidden" id="description" name="description" value="Paiement VintApp">
                        <input type="hidden" id="buyer_id" name="buyer_id" value="{{ Auth::id() }}">
                        @if(isset($deliveryAddress))
                            <input type="hidden" name="delivery_address_id" value="{{ $deliveryAddress->id }}">
                        @endif

                        {{-- Statut du paiement --}}
                        <div id="payment-status" class="hidden"></div>
                    </div>
                </x-card>
            </div>

            {{-- Colonne droite : résumé sticky --}}
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-5 space-y-6">
                    {{-- Récapitulatif --}}
                    <x-card>
                        <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex items-center gap-3">
                            <span class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-emerald-600 dark:text-emerald-400 text-sm"></i>
                            </span>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Récapitulatif</h5>
                        </div>
                        <div class="p-5 md:p-6">
                            @if(isset($cart) && !empty($cart))
                                <div class="space-y-3 mb-5">
                                    @foreach($cart as $item)
                                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700/50 last:border-0">
                                            <div class="flex items-center gap-3 min-w-0">
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
                            @endif

                            <div class="flex items-center justify-between pt-2">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Total à payer</span>
                                <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($displayTotal, 2) }} {{ $displayCurrency }}
                                </span>
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
                    </x-card>

                    {{-- Adresse de livraison --}}
                    @if(isset($deliveryAddress))
                    <x-card>
                        <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                                <i class="fas fa-truck text-blue-600 dark:text-blue-400 text-sm"></i>
                            </span>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Livraison</h5>
                        </div>
                        <div class="p-5 md:p-6">
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <strong class="text-gray-900 dark:text-white">{{ $deliveryAddress->full_name }}</strong> · <i class="fas fa-phone text-gray-400 text-xs"></i> {{ $deliveryAddress->phone }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1.5">
                                {{ $deliveryAddress->address }}, {{ $deliveryAddress->commune }}<br>
                                {{ $deliveryAddress->city }}
                            </p>
                        </div>
                    </x-card>
                    @endif

                    {{-- Bouton de paiement (dans le formulaire) --}}
                    <button type="submit"
                            id="pay-btn"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 px-6 rounded-xl transition-all font-bold text-base shadow-lg hover:shadow-xl active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Payer {{ number_format($displayTotal, 2) }} {{ $displayCurrency }}
                    </button>

                    <div class="flex items-center justify-center gap-5 text-xs text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-shield-alt text-emerald-500"></i>SSL sécurisé
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-lock text-emerald-500"></i>Paiement crypté
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-clock text-emerald-500"></i>Instantanné
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
            e.target.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-200');
            e.target.classList.remove('border-gray-300', 'dark:border-gray-600');
        }
    } else {
        operatorDetected.classList.add('hidden');
        operatorInput.value = '';
        e.target.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-200');
        e.target.classList.add('border-gray-300', 'dark:border-gray-600');
    }
});

document.querySelectorAll('.operator-card').forEach(card => {
    card.addEventListener('click', function() {
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
        const errorMsg = data.message || 'Erreur de paiement';
        const txId = data.transaction_id || '';

        if (data.success) {
            statusDiv.innerHTML = `
                <div class="flex items-start gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 animate-fade-in">
                    <div class="w-9 h-9 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-green-800 dark:text-green-200 text-sm">Paiement initié avec succès !</p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">${data.message}</p>
                        <p class="text-[11px] text-green-500 dark:text-green-400 mt-1.5 font-mono bg-white dark:bg-green-900/30 inline-block px-2 py-0.5 rounded">
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
            window.location.href = '/payments/error?error=' + encodeURIComponent(errorMsg) + '&amount=' + encodeURIComponent(amount) + '&provider=MaishaPay' + (txId ? '&transaction_id=' + txId : '');
        }
    } catch (error) {
        window.location.href = '/payments/error?error=' + encodeURIComponent(error.message || 'Erreur de paiement') + '&amount=' + encodeURIComponent(amount) + '&provider=MaishaPay';
    }
});
</script>
@endpush
@endsection
