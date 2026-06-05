@extends('app')

@section('title', 'Ajouter des fonds - ' . $wallet->currency)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <a href="{{ route('wallet.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-4">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au portefeuille
            </a>
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                    <i class="fas fa-plus text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ajouter des fonds</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $wallet->currency === 'USD' ? 'Dollar Américain' : 'Franc Congolais' }}</p>
                </div>
            </div>
        </div>

        <!-- Solde actuel -->
        <div class="bg-gradient-to-r {{ $wallet->currency === 'USD' ? 'from-emerald-600 to-emerald-500' : 'from-amber-600 to-amber-500' }} rounded-2xl shadow-lg p-6 text-white mb-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
                <div>
                    <p class="text-white/80 text-sm mb-1">Solde actuel</p>
                    <p class="text-2xl font-bold">
                        @if($wallet->currency === 'CDF')
                            {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                        @else
                            ${{ number_format($wallet->balance, 2, '.', ',') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte formulaire -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            <div class="p-6 sm:p-8">
                <form action="{{ route('wallet.store-add-funds', $wallet) }}" method="POST" id="addFundsForm">
                    @csrf

                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-coins mr-1"></i>
                            Montant à ajouter
                            <small class="font-normal text-gray-500 dark:text-gray-400">
                                (en {{ $wallet->currency === 'CDF' ? 'Francs Congolais' : 'Dollars US' }})
                            </small>
                        </label>
                        <div class="relative">
                            <input type="number"
                                   class="w-full p-4 border-2 {{ $wallet->currency === 'USD' ? 'border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500/20' : 'border-amber-200 focus:border-amber-500 focus:ring-amber-500/20' }} rounded-xl focus:ring-4 outline-none transition-all duration-300 text-lg @error('amount') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                                   id="amount"
                                   name="amount"
                                   value="{{ old('amount') }}"
                                   step="0.01"
                                   min="0.01"
                                   max="999999.99"
                                   placeholder="0.00"
                                   required>
                            @if($wallet->currency === 'CDF')
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-amber-600 dark:text-amber-400 font-bold">FC</span>
                            @else
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600 dark:text-emerald-400 font-bold">$</span>
                            @endif
                        </div>
                        @error('amount')
                            <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Montant minimum : {{ $wallet->currency === 'CDF' ? '0,01 FC' : '$0.01' }}
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-comment mr-1"></i>
                            Description <small class="font-normal text-gray-500">(optionnel)</small>
                        </label>
                        <input type="text"
                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all duration-300 @error('description') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                               id="description"
                               name="description"
                               value="{{ old('description') }}"
                               maxlength="255"
                               placeholder="Ex: Rechargement de compte, Dépôt initial...">
                        @error('description')
                            <p class="text-red-500 text-sm mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Aperçu du nouveau solde -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 mb-6 hidden" id="preview">
                        <h6 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-4">
                            <i class="fas fa-calculator mr-1"></i>
                            Aperçu du nouveau solde
                        </h6>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Solde actuel :</span>
                                <span class="font-semibold text-gray-900 dark:text-white" id="currentBalance">
                                    @if($wallet->currency === 'CDF')
                                        {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                                    @else
                                        ${{ number_format($wallet->balance, 2, '.', ',') }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Montant à ajouter :</span>
                                <span class="text-green-600 dark:text-green-400 font-semibold" id="addAmount">+0.00</span>
                            </div>
                            <hr class="border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-900 dark:text-white">Nouveau solde :</span>
                                <span class="font-bold text-lg {{ $wallet->currency === 'USD' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}" id="newBalance">
                                    @if($wallet->currency === 'CDF')
                                        {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                                    @else
                                        ${{ number_format($wallet->balance, 2, '.', ',') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r {{ $wallet->currency === 'USD' ? 'from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600' : 'from-amber-600 to-amber-500 hover:from-amber-700 hover:to-amber-600' }} text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $wallet->currency === 'USD' ? 'focus:ring-emerald-500' : 'focus:ring-amber-500' }} transition-all duration-300">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter les fonds
                        </button>
                        <a href="{{ route('wallet.index') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour au portefeuille
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Conseils de sécurité -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6 mt-6">
            <h6 class="text-sm font-semibold text-blue-600 dark:text-blue-400 mb-4">
                <i class="fas fa-shield-alt mr-2"></i>
                Conseils de sécurité
            </h6>
            <ul class="space-y-3">
                <li class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-check text-green-500 mr-3 mt-0.5"></i>
                    Vérifiez toujours le montant avant de confirmer
                </li>
                <li class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-check text-green-500 mr-3 mt-0.5"></i>
                    Gardez un historique de vos transactions
                </li>
                <li class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-check text-green-500 mr-3 mt-0.5"></i>
                    N'ajoutez que des montants que vous pouvez vous permettre
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const preview = document.getElementById('preview');
    const addAmountSpan = document.getElementById('addAmount');
    const newBalanceSpan = document.getElementById('newBalance');
    const currentBalance = {{ $wallet->balance }};
    const currency = '{{ $wallet->currency }}';

    amountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value) || 0;

        if (amount > 0) {
            preview.classList.remove('hidden');

            if (currency === 'CDF') {
                addAmountSpan.textContent = '+' + amount.toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' FC';

                newBalanceSpan.textContent = (currentBalance + amount).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' FC';
            } else {
                addAmountSpan.textContent = '+$' + amount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                newBalanceSpan.textContent = '$' + (currentBalance + amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        } else {
            preview.classList.add('hidden');
        }
    });

    const form = document.getElementById('addFundsForm');
    form.style.opacity = '0';
    form.style.transform = 'translateY(20px)';

    setTimeout(() => {
        form.style.transition = 'all 0.5s ease';
        form.style.opacity = '1';
        form.style.transform = 'translateY(0)';
    }, 200);
});
</script>
@endpush
@endsection
