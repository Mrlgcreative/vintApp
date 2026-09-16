@extends('app')

@section('title', 'Retirer des fonds - ' . $wallet->currency)

@section('content')
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-xl">

        <!-- Bouton retour -->
        <a href="{{ route('wallet.index') }}" class="mb-6 inline-flex h-8 items-center gap-1.5 text-sm font-medium text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
            <i class="fas fa-arrow-left text-xs"></i>
            Retour au portefeuille
        </a>

        <!-- Carte principale -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <!-- Header -->
            <div class="flex items-center justify-between gap-4 border-b border-zinc-100 p-6 dark:border-zinc-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        <i class="fas fa-minus text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">Retirer des fonds</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Débiter votre wallet vers Mobile Money</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-6 items-center rounded-md bg-zinc-100 px-2 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $wallet->currency }}</span>
                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400" title="Traitement automatique">
                        <i class="fas fa-bolt text-[10px]"></i>
                        Auto
                    </span>
                </div>
            </div>

            <div class="space-y-6 p-6">
                <!-- Messages flash -->
                @if(session('success'))
                    <div class="flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                        <i class="fas fa-circle-check mt-0.5 text-sm text-emerald-600 dark:text-emerald-400"></i>
                        <div>
                            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-500/30 dark:bg-red-500/10">
                        <i class="fas fa-circle-exclamation mt-0.5 text-sm text-red-600 dark:text-red-400"></i>
                        <div>
                            <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
                        <i class="fas fa-triangle-exclamation mt-0.5 text-sm text-amber-600 dark:text-amber-400"></i>
                        <div>
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-300">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Solde disponible -->
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Solde disponible</p>
                    <p class="mt-1 text-3xl font-semibold tracking-tight tabular-nums text-zinc-900 dark:text-white">
                        @if($wallet->currency === 'CDF')
                            {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                        @else
                            ${{ number_format($wallet->balance, 2, '.', ',') }}
                        @endif
                    </p>
                </div>

                @if($wallet->balance <= 0)
                    <!-- Solde insuffisant -->
                    <div class="rounded-lg border border-dashed border-zinc-300 p-6 text-center dark:border-zinc-700">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <i class="fas fa-triangle-exclamation text-lg text-zinc-400"></i>
                        </div>
                        <h3 class="mb-1 text-sm font-semibold text-zinc-900 dark:text-white">Solde insuffisant</h3>
                        <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">Vous n'avez pas de fonds disponibles pour effectuer un retrait.</p>
                        <a href="{{ route('wallet.index') }}" class="inline-flex h-9 items-center gap-2 rounded-md bg-zinc-900 px-4 text-sm font-medium text-zinc-50 transition-colors hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                            <i class="fas fa-plus text-xs"></i>
                            Ajouter des fonds
                        </a>
                    </div>
                @else
                    <!-- Formulaire de retrait -->
                    <form action="{{ route('wallet.store-withdraw-funds', $wallet) }}" method="POST" id="withdrawFundsForm" class="space-y-6">
                        @csrf

                        <!-- Montant -->
                        <div class="space-y-1.5">
                            <label for="amount" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Montant à retirer
                                <span class="font-normal text-zinc-400 dark:text-zinc-500">({{ $wallet->currency === 'CDF' ? 'FC' : 'USD' }})</span>
                            </label>
                            <div class="relative">
                                @if($wallet->currency === 'USD')
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-zinc-400">$</span>
                                @endif
                                <input type="number"
                                       id="amount"
                                       name="amount"
                                       value="{{ old('amount') }}"
                                       step="0.01"
                                       min="0.01"
                                       max="{{ $wallet->balance }}"
                                       placeholder="0.00"
                                       required
                                       class="h-10 w-full rounded-md border border-zinc-200 bg-white pl-7 pr-3 text-sm font-medium tabular-nums text-zinc-900 transition-colors placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-500 dark:focus:ring-zinc-700 @error('amount') border-red-300 ring-2 ring-red-100 dark:border-red-500/50 dark:ring-red-500/20 @enderror">
                                @if($wallet->currency === 'CDF')
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-sm font-medium text-zinc-400">FC</span>
                                @endif
                            </div>
                            @error('amount')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-zinc-400">Maximum : {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}</p>
                        </div>

                        <!-- Numéro de téléphone -->
                        <div class="space-y-1.5">
                            <label for="phone_number" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Numéro Mobile Money <span class="text-red-500">*</span>
                            </label>
                            <input type="tel"
                                   id="phone_number"
                                   name="phone_number"
                                   value="{{ old('phone_number') }}"
                                   placeholder="Ex: 0812345678 ou +243812345678"
                                   pattern="^(\+?243|0)?[0-9]{9}$"
                                   required
                                   class="h-10 w-full rounded-md border border-zinc-200 bg-white px-3 text-sm text-zinc-900 transition-colors placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-500 dark:focus:ring-zinc-700 @error('phone_number') border-red-300 ring-2 ring-red-100 dark:border-red-500/50 dark:ring-red-500/20 @enderror">
                            @error('phone_number')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-zinc-400">Format : 0812345678 ou +243812345678</p>
                        </div>

                        <!-- Méthode de retrait -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Méthode de retrait</label>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <!-- K-PAY -->
                                <button type="button" id="method-kpay" data-method="kpay"
                                        class="method-card relative rounded-lg border-2 border-zinc-900 bg-zinc-50 p-4 text-left transition-all duration-200">
                                    <span class="method-badge absolute right-2 top-2 flex h-4 w-4 items-center justify-center rounded-full border-2 border-zinc-900 bg-zinc-900">
                                        <svg class="h-2.5 w-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <i class="fas fa-bolt text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">K-PAY</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Mobile Money RDC</p>
                                        </div>
                                    </div>
                                </button>
                                <!-- MaishaPay -->
                                <button type="button" id="method-maishapay" data-method="maishapay"
                                        class="method-card relative rounded-lg border-2 border-zinc-200 bg-white p-4 text-left transition-all duration-200 dark:border-zinc-700 dark:bg-zinc-800">
                                    <span class="method-badge absolute right-2 top-2 h-4 w-4 rounded-full border-2 border-zinc-200 dark:border-zinc-600"></span>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <i class="fas fa-bolt text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">MaishaPay</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Orange, M-Pesa, Airtel, Africell</p>
                                        </div>
                                    </div>
                                </button>
                                <!-- CinetPay -->
                                <button type="button" id="method-cinetpay" data-method="cinetpay"
                                        class="method-card relative rounded-lg border-2 border-zinc-200 bg-white p-4 text-left transition-all duration-200 dark:border-zinc-700 dark:bg-zinc-800">
                                    <span class="method-badge absolute right-2 top-2 h-4 w-4 rounded-full border-2 border-zinc-200 dark:border-zinc-600"></span>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300">
                                            <i class="fas fa-bolt text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">CinetPay</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Orange Money, MTN, M-Pesa…</p>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" value="kpay">
                            <p class="text-xs text-zinc-400">K-PAY détecte automatiquement l'opérateur · MaishaPay unifié RDC · CinetPay vers votre compte mobile money</p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-1.5">
                            <label for="description" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Description
                                <span class="font-normal text-zinc-400 dark:text-zinc-500">(optionnel)</span>
                            </label>
                            <input type="text"
                                   id="description"
                                   name="description"
                                   value="{{ old('description') }}"
                                   maxlength="255"
                                   placeholder="Ex: Retrait pour achat..."
                                   class="h-10 w-full rounded-md border border-zinc-200 bg-white px-3 text-sm text-zinc-900 transition-colors placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                        </div>

                        <!-- Aperçu du nouveau solde -->
                        <div id="preview" class="hidden space-y-3 rounded-lg border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-800/50">
                            <h4 class="flex items-center gap-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                <i class="fas fa-eye text-[10px]"></i>
                                <span>Aperçu</span>
                            </h4>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-zinc-500 dark:text-zinc-400">Solde actuel</span>
                                <span class="font-medium tabular-nums text-zinc-700 dark:text-zinc-200" id="currentBalance">
                                    {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-zinc-500 dark:text-zinc-400">Montant à retirer</span>
                                <span class="font-medium tabular-nums text-red-600 dark:text-red-400" id="withdrawAmount">-0.00</span>
                            </div>
                            <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Nouveau solde</span>
                                    <span class="text-lg font-semibold tabular-nums text-zinc-900 dark:text-white" id="newBalance">
                                        {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Info traitement -->
                        <div class="rounded-lg border border-zinc-200 bg-zinc-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                            <div class="flex gap-3">
                                <i class="fas fa-bolt mt-0.5 text-sm text-emerald-600 dark:text-emerald-400"></i>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Traitement <span id="withdrawInfoMethod">K-PAY</span></h4>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400" id="withdrawInfoText">Retrait automatique. Fonds envoyés vers votre mobile en 2-10 min.</p>
                                    <p class="text-xs text-amber-600 dark:text-amber-400"><i class="fas fa-triangle-exclamation mr-1"></i>Wallet débité immédiatement · remboursement automatique en cas d'échec.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="space-y-3 pt-1">
                            <button type="submit"
                                    id="confirmBtn"
                                    disabled
                                    class="flex h-11 w-full items-center justify-center gap-2 rounded-md bg-zinc-900 px-6 text-sm font-medium text-zinc-50 shadow-sm transition-colors hover:bg-zinc-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-zinc-900 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200 dark:disabled:hover:bg-zinc-50">
                                <i class="fas fa-minus text-xs"></i>
                                <span>Confirmer le retrait</span>
                            </button>
                            <a href="{{ route('wallet.index') }}" class="flex h-10 w-full items-center justify-center gap-2 rounded-md border border-zinc-200 bg-white px-6 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <span>Retour au portefeuille</span>
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Conseils de sécurité -->
        <div class="mt-6 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-zinc-900 dark:text-white">
                <i class="fas fa-shield-halved text-sm text-zinc-400"></i>
                <span>Important à retenir</span>
            </h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-start gap-3">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-md bg-zinc-100 text-zinc-500 dark:bg-zinc-800">
                        <i class="fas fa-clock text-xs"></i>
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-300"><strong class="font-medium text-zinc-800 dark:text-zinc-100">Délai :</strong> 2 à 10 minutes selon l'opérateur</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-md bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10">
                        <i class="fas fa-mobile-screen text-xs"></i>
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-300"><strong class="font-medium text-zinc-800 dark:text-zinc-100">Numéro :</strong> Vérifiez qu'il correspond à l'opérateur</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-md bg-amber-50 text-amber-600 dark:bg-amber-500/10">
                        <i class="fas fa-triangle-exclamation text-xs"></i>
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-300"><strong class="font-medium text-zinc-800 dark:text-zinc-100">Débit :</strong> Fonds bloqués pendant le traitement</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-md bg-vinted-primary-50 text-vinted-primary-600 dark:bg-vinted-primary-500/10">
                        <i class="fas fa-rotate-left text-xs"></i>
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-300"><strong class="font-medium text-zinc-800 dark:text-zinc-100">Remboursement :</strong> Automatique en cas d'échec</span>
                </li>
            </ul>
        </div>

        <!-- Opérateurs supportés -->
        <div class="mt-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-zinc-900 dark:text-white">
                <i class="fas fa-bolt text-sm text-emerald-600 dark:text-emerald-400"></i>
                <span>Opérateurs supportés</span>
            </h3>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                <div class="rounded-lg border border-zinc-200 p-3 text-center dark:border-zinc-700">
                    <div class="mb-1.5 flex items-center justify-center text-xl"><i class="fas fa-mobile-screen text-orange-500"></i></div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Orange Money</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">084/085/089</p>
                </div>
                <div class="rounded-lg border border-zinc-200 p-3 text-center dark:border-zinc-700">
                    <div class="mb-1.5 flex items-center justify-center text-xl"><i class="fas fa-mobile-screen text-emerald-600"></i></div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">M-Pesa</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">081/082/083</p>
                </div>
                <div class="rounded-lg border border-zinc-200 p-3 text-center dark:border-zinc-700">
                    <div class="mb-1.5 flex items-center justify-center text-xl"><i class="fas fa-mobile-screen text-red-500"></i></div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Airtel Money</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">097/098/099</p>
                </div>
                <div class="rounded-lg border border-zinc-200 p-3 text-center dark:border-zinc-700">
                    <div class="mb-1.5 flex items-center justify-center text-xl"><i class="fas fa-mobile-screen text-blue-600"></i></div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Africell</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">090/091/092</p>
                </div>
                <div class="col-span-2 rounded-lg border border-zinc-200 p-3 text-center dark:border-zinc-700 sm:col-span-1">
                    <div class="mb-1.5 flex items-center justify-center text-xl"><i class="fas fa-bolt text-zinc-400"></i></div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">CinetPay</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Orange, MTN…</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const phoneInput = document.getElementById('phone_number');
    const confirmBtn = document.getElementById('confirmBtn');
    const preview = document.getElementById('preview');
    const withdrawAmountSpan = document.getElementById('withdrawAmount');
    const newBalanceSpan = document.getElementById('newBalance');
    const currentBalance = {{ $wallet->balance }};
    const currency = '{{ $wallet->currency }}';

    function validateForm() {
        const amount = parseFloat(amountInput?.value) || 0;
        const phone = phoneInput?.value.trim() || '';

        const isAmountValid = amount > 0 && amount <= currentBalance;
        const isPhoneValid = phone.length >= 9 && /^(\+?243|0)?[0-9]{9}$/.test(phone);

        confirmBtn.disabled = !(isAmountValid && isPhoneValid);

        return { isValid: isAmountValid && isPhoneValid, amount };
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            const phoneRegex = /^(\+?243|0)?[0-9]{9}$/;

            this.classList.remove('border-red-300', 'ring-red-100', 'border-emerald-300', 'ring-emerald-100');
            this.classList.remove('border-red-500', 'ring-red-200', 'border-green-500', 'ring-green-200');

            if (phone.length > 0) {
                if (phoneRegex.test(phone)) {
                    this.classList.add('border-emerald-300', 'ring-2', 'ring-emerald-100');
                } else {
                    this.classList.add('border-red-300', 'ring-2', 'ring-red-100');
                }
            }

            validateForm();
        });
    }

    if (amountInput) {
        amountInput.addEventListener('input', function() {
            validateForm();
            const amount = parseFloat(this.value) || 0;

            if (amount > 0) {
                preview.classList.remove('hidden');

                if (currency === 'CDF') {
                    withdrawAmountSpan.textContent = '-' + amount.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' FC';

                    newBalanceSpan.textContent = (currentBalance - amount).toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' FC';
                } else {
                    withdrawAmountSpan.textContent = '-$' + amount.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    newBalanceSpan.textContent = '$' + (currentBalance - amount).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                newBalanceSpan.classList.remove('text-red-600', 'text-zinc-900');
                newBalanceSpan.classList.add(amount > currentBalance ? 'text-red-600' : 'text-zinc-900');
            } else {
                preview.classList.add('hidden');
            }
        });
    }

    const form = document.getElementById('withdrawFundsForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const phone = phoneInput.value.trim();
            const amount = parseFloat(amountInput.value);
            const paymentMethod = document.getElementById('payment_method').value;
            const methodLabel = { kpay: 'K-PAY', maishapay: 'MAISHAPAY', cinetpay: 'CINETPAY' }[paymentMethod] || 'MAISHAPAY';

            const confirmMessage = `RETRAIT VIA ${methodLabel}\n\n` +
                `Montant : ${currency === 'CDF' ? amount.toLocaleString('fr-FR') + ' FC' : '$' + amount.toLocaleString('en-US')}\n` +
                `Vers : ${phone}\n\n` +
                `Transfert automatique en 2-10 minutes.\n` +
                `Wallet débité immédiatement.\n` +
                `Remboursement auto en cas d'échec.\n\n` +
                `Confirmer ?`;

            if (!confirm(confirmMessage)) {
                e.preventDefault();
            } else {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    <span>Traitement en cours...</span>
                `;
            }
        });
    }

    const methodMeta = {
        kpay: {
            active: 'border-zinc-900 bg-zinc-50 dark:border-zinc-100 dark:bg-zinc-800',
            badge: 'border-zinc-900 bg-zinc-900',
            label: 'K-PAY',
            text: 'Retrait automatique. Fonds envoyés vers votre mobile en 2-10 min.'
        },
        maishapay: {
            active: 'border-zinc-900 bg-zinc-50 dark:border-zinc-100 dark:bg-zinc-800',
            badge: 'border-zinc-900 bg-zinc-900',
            label: 'MaishaPay',
            text: "Retrait automatique. Fonds envoyés vers votre mobile en 2-10 min."
        },
        cinetpay: {
            active: 'border-zinc-900 bg-zinc-50 dark:border-zinc-100 dark:bg-zinc-800',
            badge: 'border-zinc-900 bg-zinc-900',
            label: 'CinetPay',
            text: 'Transfert CinetPay. Fonds envoyés vers votre compte mobile money (Orange Money, MTN, M-Pesa).'
        }
    };
    const base = 'method-card relative rounded-lg border-2 p-4 text-left transition-all duration-200 ';
    const inactive = 'border-zinc-200 bg-white hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-zinc-600';
    const checkSvg = `<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>`;

    function selectWithdrawMethod(method) {
        document.getElementById('payment_method').value = method;
        const meta = methodMeta[method];

        document.querySelectorAll('.method-card').forEach(function(card) {
            const m = card.dataset.method;
            const isActive = m === method;
            card.className = base + (isActive ? methodMeta[m].active : inactive);
            const badge = card.querySelector('.method-badge');
            badge.className = 'method-badge absolute top-2 right-2 w-4 h-4 rounded-full border-2 flex items-center justify-center ' +
                (isActive ? methodMeta[m].badge : 'border-zinc-200 dark:border-zinc-600');
            badge.innerHTML = isActive ? checkSvg : '';
        });

        document.getElementById('withdrawInfoMethod').textContent = meta.label;
        document.getElementById('withdrawInfoText').textContent = meta.text;
    }

    document.querySelectorAll('.method-card').forEach(function(card) {
        card.addEventListener('click', function() {
            selectWithdrawMethod(this.dataset.method);
        });
    });

    selectWithdrawMethod('kpay');
});
</script>
@endpush
@endsection