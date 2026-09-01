@extends('app')

@section('title', 'Retirer des fonds - ' . $wallet->currency)

@section('content')
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-xl">
        <!-- Carte principale -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <!-- Header -->
            <div class="bg-vinted-primary-600 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 text-white">
                            <i class="fas fa-minus"></i>
                        </div>
                        <h1 class="text-xl font-bold tracking-tight text-white">Retirer des fonds</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-white/15 px-3 py-1 text-sm font-medium text-white">
                            {{ $wallet->currency }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-400 px-3 py-1 text-xs font-medium text-white animate-pulse" title="Traitement automatique">
                            <i class="fas fa-bolt"></i>
                            <span>Auto</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="space-y-6 p-6">
                <!-- Messages flash -->
                @if(session('success'))
                    <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                        <i class="fas fa-circle-check mt-0.5 text-emerald-500"></i>
                        <div>
                            <p class="font-semibold text-emerald-800 dark:text-emerald-400">Succès</p>
                            <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-500/30 dark:bg-red-500/10">
                        <i class="fas fa-circle-exclamation mt-0.5 text-red-500"></i>
                        <div>
                            <p class="font-semibold text-red-800 dark:text-red-400">Erreur</p>
                            <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
                        <i class="fas fa-triangle-exclamation mt-0.5 text-amber-500"></i>
                        <div>
                            <p class="font-semibold text-amber-800 dark:text-amber-400">Attention</p>
                            <p class="text-sm text-amber-700 dark:text-amber-300">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Solde disponible -->
                <div class="rounded-xl border border-vinted-primary-200 bg-vinted-primary-50 p-5 dark:border-vinted-primary-500/30 dark:bg-vinted-primary-500/5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-vinted-primary-600 text-white">
                            <i class="fas fa-wallet text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Solde disponible</p>
                            <p class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                                @if($wallet->currency === 'CDF')
                                    {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                                @else
                                    ${{ number_format($wallet->balance, 2, '.', ',') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($wallet->balance <= 0)
                    <!-- Solde insuffisant -->
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-center dark:border-amber-500/30 dark:bg-amber-500/10">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-500/20">
                            <i class="fas fa-triangle-exclamation text-2xl text-amber-500"></i>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-amber-800 dark:text-amber-300">Solde insuffisant</h3>
                        <p class="mb-4 text-amber-600 dark:text-amber-400">Vous n'avez pas de fonds disponibles pour effectuer un retrait.</p>
                        <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 font-medium text-white transition-colors duration-200 hover:bg-emerald-700">
                            <i class="fas fa-plus"></i>
                            Ajouter des fonds
                        </a>
                    </div>
                @else
                    <!-- Formulaire de retrait -->
                    <form action="{{ route('wallet.store-withdraw-funds', $wallet) }}" method="POST" id="withdrawFundsForm" class="space-y-6">
                        @csrf

                        <!-- Montant -->
                        <div>
                            <label for="amount" class="mb-2 flex items-center gap-1 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                <i class="fas fa-money-bill-wave text-zinc-400 dark:text-zinc-500"></i>
                                <span>Montant à retirer</span>
                                <span class="text-xs font-normal text-zinc-400 dark:text-zinc-500">
                                    (en {{ $wallet->currency === 'CDF' ? 'Francs Congolais' : 'Dollars US' }})
                                </span>
                            </label>
                            <div class="relative">
                                @if($wallet->currency === 'USD')
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <span class="font-medium text-zinc-500 dark:text-zinc-400">$</span>
                                    </div>
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
                                       class="w-full rounded-lg border border-zinc-300 bg-white py-3.5 pr-16 text-lg font-medium transition-all duration-200 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800 {{ $wallet->currency === 'USD' ? 'pl-8' : 'pl-4' }} @error('amount') border-red-500 ring-2 ring-red-200 @enderror">
                                @if($wallet->currency === 'CDF')
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                        <span class="font-medium text-zinc-500 dark:text-zinc-400">FC</span>
                                    </div>
                                @endif
                            </div>
                            @error('amount')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                                <i class="fas fa-circle-info"></i>
                                <span>Maximum : {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}</span>
                            </p>
                        </div>

                        <!-- Numéro de téléphone -->
                        <div>
                            <label for="phone_number" class="mb-2 flex items-center gap-1 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                <i class="fas fa-mobile-screen text-zinc-400 dark:text-zinc-500"></i>
                                <span>Numéro Mobile Money</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="tel"
                                   id="phone_number"
                                   name="phone_number"
                                   value="{{ old('phone_number') }}"
                                   placeholder="Ex: 0812345678 ou +243812345678"
                                   pattern="^(\+?243|0)?[0-9]{9}$"
                                   required
                                    class="w-full rounded-lg border border-zinc-300 bg-white px-4 py-3.5 text-lg transition-all duration-200 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800 @error('phone_number') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('phone_number')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                                <i class="fas fa-circle-info"></i>
                                <span>Numéro de réception (format: 0812345678 ou +243812345678)</span>
                            </p>
                        </div>

                        <!-- Méthode de retrait -->
                        <div>
                            <label class="mb-2 flex items-center gap-1 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                <i class="fas fa-credit-card text-zinc-400 dark:text-zinc-500"></i>
                                <span>Méthode de retrait</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- MaishaPay -->
                                <button type="button" id="method-maishapay" data-method="maishapay"
                                        class="method-card relative rounded-xl border-2 border-emerald-500 bg-gradient-to-r from-emerald-50 to-teal-50 p-4 text-left transition-all duration-200 dark:from-emerald-500/10 dark:to-teal-500/10">
                                    <span class="method-badge absolute right-2 top-2 flex h-4 w-4 items-center justify-center rounded-full border-2 border-emerald-500 bg-emerald-500">
                                        <svg class="h-2.5 w-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white">
                                            <i class="fas fa-bolt"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-zinc-900 dark:text-white">MaishaPay</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Orange, M-Pesa, Airtel, Africell</p>
                                        </div>
                                    </div>
                                </button>
                                <!-- CinetPay -->
                                <button type="button" id="method-cinetpay" data-method="cinetpay"
                                        class="method-card relative rounded-xl border-2 border-zinc-200 bg-white p-4 text-left transition-all duration-200 dark:border-zinc-600 dark:bg-zinc-800">
                                    <span class="method-badge absolute right-2 top-2 h-4 w-4 rounded-full border-2 border-zinc-300 dark:border-zinc-500"></span>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-vinted-primary-600 text-white">
                                            <i class="fas fa-bolt"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-zinc-900 dark:text-white">CinetPay</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Orange Money, MTN, M-Pesa…</p>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" value="maishapay">
                            <p class="mt-2 flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                                <i class="fas fa-circle-info"></i>
                                <span>MaishaPay détecte automatiquement l'opérateur · CinetPay envoie vers votre compte mobile money</span>
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="mb-2 flex items-center gap-1 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                <i class="fas fa-note-sticky text-zinc-400 dark:text-zinc-500"></i>
                                <span>Description</span>
                                <span class="text-xs font-normal text-zinc-400 dark:text-zinc-500">(optionnel)</span>
                            </label>
                            <input type="text"
                                   id="description"
                                   name="description"
                                   value="{{ old('description') }}"
                                   maxlength="255"
                                   placeholder="Ex: Retrait pour achat..."
                                    class="w-full rounded-lg border border-zinc-300 bg-white px-4 py-3 transition-all duration-200 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800">
                        </div>

                        <!-- Aperçu du nouveau solde -->
                        <div id="preview" class="hidden space-y-3 rounded-xl border border-zinc-200 bg-zinc-50 p-5 transition-all duration-300 dark:border-zinc-700 dark:bg-zinc-800/50">
                            <h4 class="flex items-center gap-2 text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                                <i class="fas fa-eye"></i>
                                <span>Aperçu</span>
                            </h4>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-zinc-500 dark:text-zinc-400">Solde actuel</span>
                                <span class="font-semibold text-zinc-700 dark:text-zinc-200" id="currentBalance">
                                    {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-zinc-500 dark:text-zinc-400">Montant à retirer</span>
                                <span class="font-semibold text-red-600 dark:text-red-400" id="withdrawAmount">-0.00</span>
                            </div>
                            <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-200">Nouveau solde</span>
                                    <span class="text-xl font-bold text-vinted-primary-600 dark:text-vinted-primary-400" id="newBalance">
                                        {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Info traitement -->
                        <div class="rounded-xl border border-vinted-primary-200 bg-vinted-primary-50 p-4 dark:border-vinted-primary-500/30 dark:bg-vinted-primary-500/5">
                            <div class="flex gap-3">
                                <i class="fas fa-bolt mt-0.5 text-vinted-primary-500"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-vinted-primary-800 dark:text-vinted-primary-200">⚡ Traitement <span id="withdrawInfoMethod">MaishaPay</span></h4>
                                    <p class="mt-1 text-xs text-vinted-primary-600 dark:text-vinted-primary-300" id="withdrawInfoText">Retrait automatique. Fonds envoyés vers votre mobile en 2-10 min selon l'opérateur.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Avertissement -->
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
                            <div class="flex gap-3">
                                <i class="fas fa-triangle-exclamation mt-0.5 text-amber-500"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-200">⚠️ Attention</h4>
                                    <p class="mt-1 text-xs text-amber-700 dark:text-amber-300"><strong>Wallet débité immédiatement.</strong> Remboursement automatique en cas d'échec du transfert.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="space-y-3 pt-2">
                            <button type="submit"
                                    id="confirmBtn"
                                    disabled
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-vinted-primary-600 px-6 py-4 font-semibold text-white shadow-lg transition-all duration-300 hover:bg-vinted-primary-700 disabled:cursor-not-allowed disabled:bg-zinc-300 dark:disabled:bg-zinc-700">
                                <i class="fas fa-minus"></i>
                                <span>Confirmer le retrait</span>
                            </button>
                            <a href="{{ route('wallet.index') }}" class="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-zinc-200 bg-white px-6 py-3 font-medium text-zinc-700 transition-all duration-200 hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-zinc-500 dark:hover:bg-zinc-700/50">
                                <i class="fas fa-arrow-left"></i>
                                <span>Retour au portefeuille</span>
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Conseils de sécurité -->
        <div class="mt-6 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-vinted-primary-600 dark:text-vinted-primary-400">
                <i class="fas fa-shield-halved"></i>
                <span>Important à retenir</span>
            </h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-start gap-3">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-500/20">
                        <i class="fas fa-clock text-xs text-blue-600 dark:text-blue-400"></i>
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-300"><strong class="text-zinc-800 dark:text-zinc-100">Délai :</strong> 2 à 10 minutes selon l'opérateur</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20">
                        <i class="fas fa-mobile-screen text-xs text-emerald-600 dark:text-emerald-400"></i>
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-300"><strong class="text-zinc-800 dark:text-zinc-100">Numéro :</strong> Vérifiez qu'il correspond à l'opérateur</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-500/20">
                        <i class="fas fa-triangle-exclamation text-xs text-amber-600 dark:text-amber-400"></i>
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-300"><strong class="text-zinc-800 dark:text-zinc-100">Débit :</strong> Fonds bloqués pendant le traitement</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-vinted-primary-100 dark:bg-vinted-primary-500/20">
                        <i class="fas fa-rotate-left text-xs text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-300"><strong class="text-zinc-800 dark:text-zinc-100">Remboursement :</strong> Automatique en cas d'échec</span>
                </li>
            </ul>
        </div>

        <!-- Opérateurs supportés -->
        <div class="mt-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-emerald-600 dark:text-emerald-400">
                <i class="fas fa-bolt"></i>
                <span>Opérateurs supportés</span>
            </h3>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                <div class="cursor-default rounded-xl border border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100 p-3 text-center transition-transform duration-200 hover:scale-105 dark:border-orange-500/30 dark:from-orange-500/10 dark:to-orange-500/5">
                    <div class="mb-1 text-2xl">🟠</div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Orange Money</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">084/085/089</p>
                </div>
                <div class="cursor-default rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-3 text-center transition-transform duration-200 hover:scale-105 dark:border-emerald-500/30 dark:from-emerald-500/10 dark:to-emerald-500/5">
                    <div class="mb-1 text-2xl">🟢</div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">M-Pesa</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">081/082/083</p>
                </div>
                <div class="cursor-default rounded-xl border border-red-200 bg-gradient-to-br from-red-50 to-red-100 p-3 text-center transition-transform duration-200 hover:scale-105 dark:border-red-500/30 dark:from-red-500/10 dark:to-red-500/5">
                    <div class="mb-1 text-2xl">🔴</div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Airtel Money</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">097/098/099</p>
                </div>
                <div class="cursor-default rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-3 text-center transition-transform duration-200 hover:scale-105 dark:border-blue-500/30 dark:from-blue-500/10 dark:to-blue-500/5">
                    <div class="mb-1 text-2xl">🔵</div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Africell</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">090/091/092</p>
                </div>
                <div class="cursor-default rounded-xl border border-cyan-200 bg-gradient-to-br from-cyan-50 to-sky-100 p-3 text-center transition-transform duration-200 hover:scale-105 dark:border-cyan-500/30 dark:from-cyan-500/10 dark:to-sky-500/5">
                    <div class="mb-1 text-2xl">⚡</div>
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">CinetPay</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Orange, MTN, M-Pesa…</p>
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
        const amount = parseFloat(amountInput.value) || 0;
        const phone = phoneInput.value.trim();
        
        const isAmountValid = amount > 0 && amount <= currentBalance;
        const isPhoneValid = phone.length >= 9 && /^(\+?243|0)?[0-9]{9}$/.test(phone);
        
        confirmBtn.disabled = !(isAmountValid && isPhoneValid);
        
        return { isValid: isAmountValid && isPhoneValid, amount };
    }
    
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            const phoneRegex = /^(\+?243|0)?[0-9]{9}$/;
            
            this.classList.remove('border-red-500', 'ring-2', 'ring-red-200', 'border-green-500', 'ring-green-200');
            
            if (phone.length > 0) {
                if (phoneRegex.test(phone)) {
                    this.classList.add('border-green-500', 'ring-2', 'ring-green-200');
                } else {
                    this.classList.add('border-red-500', 'ring-2', 'ring-red-200');
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
                
                newBalanceSpan.classList.remove('text-blue-600', 'text-red-600');
                newBalanceSpan.classList.add(amount > currentBalance ? 'text-red-600' : 'text-blue-600');
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
            const methodLabel = paymentMethod === 'cinetpay' ? 'CINETPAY' : 'MAISHAPAY';

            const confirmMessage = `🔄 RETRAIT VIA ${methodLabel}\n\n` +
                `Montant : ${currency === 'CDF' ? amount.toLocaleString('fr-FR') + ' FC' : '$' + amount.toLocaleString('en-US')}\n` +
                `Vers : ${phone}\n\n` +
                `⚡ Transfert automatique en 2-10 minutes.\n` +
                `💰 Wallet débité immédiatement.\n` +
                `🔄 Remboursement auto en cas d'échec.\n\n` +
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

    // Sélection de la méthode de retrait (MaishaPay / CinetPay)
    const methodMeta = {
        maishapay: {
            active: 'border-emerald-500 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-500/10 dark:to-teal-500/10',
            badge: 'border-emerald-500 bg-emerald-500',
            label: 'MaishaPay',
            text: "Retrait automatique. Fonds envoyés vers votre mobile en 2-10 min selon l'opérateur."
        },
        cinetpay: {
            active: 'border-vinted-primary-500 bg-gradient-to-r from-vinted-primary-50 to-indigo-50 dark:from-vinted-primary-500/10 dark:to-indigo-500/10',
            badge: 'border-vinted-primary-500 bg-vinted-primary-500',
            label: 'CinetPay',
            text: 'Transfert CinetPay. Fonds envoyés vers votre compte mobile money (Orange Money, MTN, M-Pesa).'
        }
    };
    const checkSvg = `<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>`;

    function selectWithdrawMethod(method) {
        document.getElementById('payment_method').value = method;
        const meta = methodMeta[method];
        const base = 'method-card relative rounded-xl border-2 p-4 text-left transition-all duration-200 ';

        document.querySelectorAll('.method-card').forEach(function(card) {
            const m = card.dataset.method;
            const isActive = m === method;
            card.className = base + (isActive ? methodMeta[m].active : 'border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800');
            const badge = card.querySelector('.method-badge');
            badge.className = 'method-badge absolute top-2 right-2 w-4 h-4 rounded-full border-2 flex items-center justify-center ' +
                (isActive ? methodMeta[m].badge : 'border-zinc-300 dark:border-zinc-500');
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
});
</script>
@endpush
@endsection
