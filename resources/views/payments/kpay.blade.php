@extends('app')

@section('title', 'Paiement K-PAY')

@section('content')
@php
    $displayTotal = isset($totalCdf) ? (float) $totalCdf : (float) ($total ?? 0);
    $sourcedFromCdf = isset($totalCdf) && isset($total) && ((float) $total !== (float) $totalCdf);
    $displayCurrency = 'CDF';
    $sourceCurrency = isset($currency) ? $currency : 'CDF';
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Erreurs --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            @foreach($errors->all() as $error)
                <p class="text-sm text-red-700 dark:text-red-300 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>{{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-700 dark:text-red-300 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
            </p>
        </div>
    @endif

    @if(($kpayIsSandbox ?? false) === true)
        <div class="mb-6 rounded-xl border border-blue-200 dark:border-blue-500/30 bg-blue-50 dark:bg-blue-500/10 p-4 sm:p-5">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 inline-flex items-center gap-1.5 rounded-full bg-blue-600/10 border border-blue-200 dark:border-blue-500/40 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:text-blue-300 shrink-0">
                    <i class="fas fa-flask"></i> Mode test
                </span>
                <div class="text-sm">
                    <p class="font-medium text-blue-800 dark:text-blue-200 mb-1">
                        Seuls les numéros de démonstration sont acceptés en mode test.
                    </p>
                    <p class="text-blue-700/80 dark:text-blue-300/80 mb-2">
                        Utilisez l'un de ces numéros de test de la RD Congo (sinon le paiement sera refusé) :
                    </p>
                    <ul class="space-y-1 text-blue-700/90 dark:text-blue-300/90">
                        <li class="flex items-center gap-2">
                            <code class="font-mono text-xs bg-white/60 dark:bg-blue-900/40 px-1.5 py-0.5 rounded">0813456789</code>
                            <span class="text-xs">→ paiement réussi (Vodacom)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <code class="font-mono text-xs bg-white/60 dark:bg-blue-900/40 px-1.5 py-0.5 rounded">0973456789</code>
                            <span class="text-xs">→ paiement réussi (Airtel)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <code class="font-mono text-xs bg-white/60 dark:bg-blue-900/40 px-1.5 py-0.5 rounded">0813456129</code>
                            <span class="text-xs">→ paiement soumis/en attente</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <code class="font-mono text-xs bg-white/60 dark:bg-blue-900/40 px-1.5 py-0.5 rounded">0813456019</code>
                            <span class="text-xs">→ paiement échoué (limite atteinte)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form id="kpay-form" action="{{ route('payments.kpay.initiate') }}" method="POST" novalidate>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- Colonne gauche : formulaire --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700/50 shadow-sm overflow-hidden">

                    {{-- En-tête du panneau --}}
                    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700/50 flex items-center justify-between gap-3">
                        <div>
                            <h1 class="text-base font-semibold text-gray-900 dark:text-white">Paiement Mobile Money</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Toutes les transactions sont sécurisées et chiffrées</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 bg-vinted-success-50 dark:bg-vinted-success-500/10 border border-vinted-success-200 dark:border-vinted-success-800 px-2.5 py-1 rounded-md text-xs font-medium text-vinted-success-700 dark:text-vinted-success-300 shrink-0">
                            <i class="fas fa-shield-alt"></i>Sécurisé
                        </span>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">

                        {{-- Opérateur --}}
                        <div>
                            <label for="operator" class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">
                                Opérateur
                            </label>
                            <div class="relative">
                                <x-select id="operator" name="operator" class="appearance-none pr-10">
                                    <option value="">Sélectionnez votre opérateur</option>
                                    <option value="VODACOM">Vodacom M-Pesa</option>
                                    <option value="AIRTEL">Airtel Money</option>
                                    <option value="ORANGE">Orange Money</option>
                                </x-select>
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5" id="operator-hint">Choisissez l'opérateur Mobile Money utilisé pour le paiement.</p>
                            <p class="text-sm text-vinted-success-600 dark:text-vinted-success-300 font-medium mt-1.5 hidden" id="operator-detected">
                                <i class="fas fa-check-circle mr-1"></i><span id="operator-detected-name"></span>
                            </p>
                        </div>

                        {{-- Numéro Mobile Money --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">
                                Numéro Mobile Money
                            </label>
                            <div class="flex shadow-sm">
                                <span class="inline-flex items-center px-3.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l-lg text-sm font-medium">
                                    +243
                                </span>
                                <input type="tel"
                                       class="flex-1 w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors md:rounded-r-lg tracking-wide"
                                       id="phone"
                                       name="phone"
                                       maxlength="13"
                                       value="{{ old('phone', $deliveryAddress->phone ?? '') }}"
                                       placeholder="0XX XX XX XXX"
                                       autocomplete="tel">
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5">
                                L'opérateur est détecté automatiquement. Une confirmation sera envoyée sur ce numéro.
                            </p>
                        </div>

                        {{-- Séparateur --}}
                        <div class="border-t border-gray-100 dark:border-gray-700/50"></div>

                        {{-- Montant --}}
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">
                                Montant à payer
                            </label>
                            <div class="flex shadow-sm">
                                <input type="number"
                                       class="flex-1 w-full px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-l-lg text-sm font-semibold text-gray-900 dark:text-white focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors"
                                       id="amount"
                                       name="amount"
                                       min="1"
                                       step="0.01"
                                       required
                                       readonly
                                       value="{{ $displayTotal }}">
                                <span class="inline-flex items-center px-3.5 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-r-lg text-sm font-medium text-gray-600 dark:text-gray-300">
                                    {{ $displayCurrency }}
                                </span>
                            </div>
                            <input type="hidden" name="currency" value="{{ $displayCurrency }}">
                            @if(!empty($sourcedFromCdf) && $sourceCurrency !== 'CDF')
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5">
                                    Soit {{ number_format((float) $total, 2) }} {{ $sourceCurrency }}
                                    @if(!empty($exchangeRate))
                                        (taux {{ number_format((float) $exchangeRate, 2) }})
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne droite : résumé sticky --}}
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-5 space-y-6">

                    {{-- Récapitulatif --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700/50 shadow-sm overflow-hidden">
                        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700/50">
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Récapitulatif</h5>
                        </div>
                        <div class="p-5 sm:p-6">
                            @if(isset($cart) && !empty($cart))
                                <div class="space-y-3 mb-5">
                                    @foreach($cart as $item)
                                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700/50 last:border-0">
                                            <div class="flex items-center gap-3 min-w-0">
                                                @if(isset($item['image']) && $item['image'])
                                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                                         alt="{{ $item['name'] }}"
                                                         class="w-11 h-11 rounded-md object-cover flex-shrink-0 border border-gray-100 dark:border-gray-700">
                                                @else
                                                    <div class="w-11 h-11 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
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

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Total à payer</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($displayTotal, 2) }} {{ $displayCurrency }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Adresse de livraison --}}
                    @if(isset($deliveryAddress))
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700/50 shadow-sm overflow-hidden">
                        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700/50 flex items-center gap-3">
                            <span class="w-7 h-7 bg-vinted-primary-50 dark:bg-vinted-primary-500/10 rounded-md flex items-center justify-center">
                                <i class="fas fa-truck text-vinted-primary-600 dark:text-vinted-primary-300 text-xs"></i>
                            </span>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Livraison</h5>
                        </div>
                        <div class="p-5 sm:px-6">
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <strong class="text-gray-900 dark:text-white">{{ $deliveryAddress->full_name }}</strong> · <i class="fas fa-phone text-gray-400 text-xs"></i> {{ $deliveryAddress->phone }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1.5">
                                {{ $deliveryAddress->address }}, {{ $deliveryAddress->commune }}<br>
                                {{ $deliveryAddress->city }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Bouton de paiement --}}
                    <div class="space-y-4">
                        <x-button-primary id="pay-btn" type="submit" size="lg" class="w-full !px-6 !py-3.5 text-sm">
                            <i class="fas fa-lock mr-2"></i>
                            Payer {{ number_format($displayTotal, 2) }} {{ $displayCurrency }}
                        </x-button-primary>

                        <div class="flex items-center justify-center gap-5 text-xs text-gray-400">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-shield-alt text-vinted-success-500"></i>SSL sécurisé
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-lock text-vinted-success-500"></i>Paiement crypté
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-clock text-vinted-success-500"></i>Instantanné
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Avantages K-PAY --}}
    <div class="mt-8 grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700/50 text-center">
            <div class="w-10 h-10 bg-vinted-primary-50 dark:bg-vinted-primary-500/10 rounded-lg flex items-center justify-center mx-auto mb-2.5">
                <i class="fas fa-globe text-vinted-primary-600 dark:text-vinted-primary-300"></i>
            </div>
            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Panafricain</h6>
            <p class="text-xs text-gray-500 mt-0.5">Plusieurs opérateurs</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700/50 text-center">
            <div class="w-10 h-10 bg-vinted-success-50 dark:bg-vinted-success-500/10 rounded-lg flex items-center justify-center mx-auto mb-2.5">
                <i class="fas fa-shield-alt text-vinted-success-600 dark:text-vinted-success-300"></i>
            </div>
            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Sécurisé</h6>
            <p class="text-xs text-gray-500 mt-0.5">Confirmation USSD</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700/50 text-center">
            <div class="w-10 h-10 bg-vinted-accent-50 dark:bg-vinted-accent-500/10 rounded-lg flex items-center justify-center mx-auto mb-2.5">
                <i class="fas fa-mobile-alt text-vinted-accent-500 dark:text-vinted-accent-300"></i>
            </div>
            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Simple</h6>
            <p class="text-xs text-gray-500 mt-0.5">Paiement depuis le mobile</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const phoneInput = document.getElementById('phone');
    const operatorSelect = document.getElementById('operator');
    const predictUrl = window.location.pathname.includes('payments/kpay')
        ? '/payments/kpay/predict-provider'
        : '{{ route("payments.kpay.predict-provider") }}';
    let timer = null;

    if (!phoneInput || !operatorSelect) return;

    function normalize(raw) {
        return raw.replace(/[^0-9]/g, '').replace(/^243/, '');
    }

    function predict() {
        const digits = normalize(phoneInput.value);
        if (digits.length < 9) return;

        fetch(predictUrl + '?phone=' + encodeURIComponent(digits), {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            const hint = document.getElementById('operator-hint');
            const detected = document.getElementById('operator-detected');
            const detectedName = document.getElementById('operator-detected-name');

            if (data.success && data.provider && operatorSelect.querySelector('option[value="' + data.provider + '"]')) {
                operatorSelect.value = data.provider;
                if (detected && detectedName && operatorSelect.selectedOptions[0]) {
                    detectedName.textContent = 'Opérateur détecté : ' + operatorSelect.selectedOptions[0].textContent;
                    detected.classList.remove('hidden');
                    if (hint) hint.textContent = 'Vous pouvez modifier l\'opérateur détecté si besoin.';
                }
            } else if (detected) {
                detected.classList.add('hidden');
                if (hint) hint.textContent = 'Choisissez l\'opérateur Mobile Money utilisé pour le paiement.';
            }
        })
        .catch(err => {
            if (window.console && console.error) console.error('K-PAY predict error:', err);
        });
    }

    phoneInput.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(predict, 500);
    });

    if (phoneInput.value) predict();
})();
</script>
@endpush
@endsection