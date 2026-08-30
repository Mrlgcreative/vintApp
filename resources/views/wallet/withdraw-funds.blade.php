@extends('app')

@section('title', 'Retirer des fonds - ' . $wallet->currency)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto">
        <!-- Carte principale -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700/50">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <h1 class="text-xl font-bold text-white">Retirer des fonds</h1>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 bg-white/20 text-white text-sm font-medium rounded-full">
                            {{ $wallet->currency }}
                        </span>
                        <span class="px-3 py-1 bg-green-400 text-white text-xs font-medium rounded-full flex items-center space-x-1 animate-pulse" title="Traitement automatique">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                            <span>Auto</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Messages flash -->
                @if(session('success'))
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-green-800 dark:text-green-400">Succès</p>
                            <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-800 dark:text-red-400">Erreur</p>
                            <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-amber-800 dark:text-amber-400">Attention</p>
                            <p class="text-sm text-amber-700 dark:text-amber-300">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Solde disponible -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl p-5">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Solde disponible</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
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
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-amber-800 dark:text-amber-300 mb-2">Solde insuffisant</h3>
                        <p class="text-amber-600 dark:text-amber-400 mb-4">Vous n'avez pas de fonds disponibles pour effectuer un retrait.</p>
                        <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter des fonds
                        </a>
                    </div>
                @else
                    <!-- Formulaire de retrait -->
                    <form action="{{ route('wallet.store-withdraw-funds', $wallet) }}" method="POST" id="withdrawFundsForm" class="space-y-6">
                        @csrf

                        <!-- Montant -->
                        <div>
                            <label for="amount" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Montant à retirer</span>
                                    <span class="text-gray-400 dark:text-gray-500 font-normal text-xs">
                                        (en {{ $wallet->currency === 'CDF' ? 'Francs Congolais' : 'Dollars US' }})
                                    </span>
                                </span>
                            </label>
                            <div class="relative">
                                @if($wallet->currency === 'USD')
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 font-medium">$</span>
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
                                       class="w-full {{ $wallet->currency === 'USD' ? 'pl-8' : 'pl-4' }} pr-16 py-3.5 text-lg font-medium border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 @error('amount') border-red-500 ring-2 ring-red-200 @enderror">
                                @if($wallet->currency === 'CDF')
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 font-medium">FC</span>
                                    </div>
                                @endif
                            </div>
                            @error('amount')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Maximum : {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}</span>
                            </p>
                        </div>

                        <!-- Numéro de téléphone -->
                        <div>
                            <label for="phone_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Numéro Mobile Money</span>
                                    <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="tel" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number') }}"
                                   placeholder="Ex: 0812345678 ou +243812345678"
                                   pattern="^(\+?243|0)?[0-9]{9}$"
                                   required
                                    class="w-full px-4 py-3.5 text-lg border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 @error('phone_number') border-red-500 ring-2 ring-red-200 @enderror">
                            @error('phone_number')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Numéro de réception (format: 0812345678 ou +243812345678)</span>
                            </p>
                        </div>

                        <!-- Méthode de retrait -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <span>Méthode de retrait</span>
                                </span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- MaishaPay -->
                                <button type="button" id="method-maishapay" data-method="maishapay"
                                        class="method-card relative rounded-xl border-2 border-green-500 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 p-4 text-left transition-all duration-200">
                                    <span class="method-badge absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-green-500 bg-green-500 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">MaishaPay</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Orange, M-Pesa, Airtel, Africell</p>
                                        </div>
                                    </div>
                                </button>
                                <!-- CinetPay -->
                                <button type="button" id="method-cinetpay" data-method="cinetpay"
                                        class="method-card relative rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-4 text-left transition-all duration-200">
                                    <span class="method-badge absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-gray-300 dark:border-gray-500"></span>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">CinetPay</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Orange Money, MTN, M-Pesa…</p>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" value="maishapay">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>MaishaPay détecte automatiquement l'opérateur · CinetPay envoie vers votre compte mobile money</span>
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                    <span>Description</span>
                                    <span class="text-gray-400 dark:text-gray-500 font-normal text-xs">(optionnel)</span>
                                </span>
                            </label>
                            <input type="text" 
                                   id="description" 
                                   name="description" 
                                   value="{{ old('description') }}"
                                   maxlength="255"
                                   placeholder="Ex: Retrait pour achat..."
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                        </div>

                        <!-- Aperçu du nouveau solde -->
                        <div id="preview" class="hidden bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 space-y-3 border border-gray-200 dark:border-gray-700 transform transition-all duration-300">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Aperçu</span>
                            </h4>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Solde actuel</span>
                                <span class="font-semibold text-gray-700 dark:text-gray-200" id="currentBalance">
                                    {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Montant à retirer</span>
                                <span class="font-semibold text-red-600 dark:text-red-400" id="withdrawAmount">-0.00</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-700 dark:text-gray-200">Nouveau solde</span>
                                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400" id="newBalance">
                                        {{ $wallet->currency === 'CDF' ? number_format($wallet->balance, 2, ',', ' ') . ' FC' : '$' . number_format($wallet->balance, 2, '.', ',') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Info traitement -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl p-4">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200">⚡ Traitement <span id="withdrawInfoMethod">MaishaPay</span></h4>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1" id="withdrawInfoText">Retrait automatique. Fonds envoyés vers votre mobile en 2-10 min selon l'opérateur.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Avertissement -->
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-200">⚠️ Attention</h4>
                                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-1"><strong>Wallet débité immédiatement.</strong> Remboursement automatique en cas d'échec du transfert.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="space-y-3 pt-2">
                            <button type="submit" 
                                    id="confirmBtn" 
                                    disabled
                                    class="w-full py-4 px-6 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:transform-none transition-all duration-300 flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                                <span>Confirmer le retrait</span>
                            </button>
                            <a href="{{ route('wallet.index') }}" class="w-full py-3 px-6 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-200 font-medium rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                <span>Retour au portefeuille</span>
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Conseils de sécurité -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700/50 p-5">
            <h3 class="text-sm font-bold text-red-600 dark:text-red-400 mb-4 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Important à retenir</span>
            </h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-start space-x-3">
                    <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span class="text-gray-600 dark:text-gray-300"><strong class="text-gray-800 dark:text-gray-100">Délai :</strong> 2 à 10 minutes selon l'opérateur</span>
                </li>
                <li class="flex items-start space-x-3">
                    <span class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <span class="text-gray-600 dark:text-gray-300"><strong class="text-gray-800 dark:text-gray-100">Numéro :</strong> Vérifiez qu'il correspond à l'opérateur</span>
                </li>
                <li class="flex items-start space-x-3">
                    <span class="w-6 h-6 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                    <span class="text-gray-600 dark:text-gray-300"><strong class="text-gray-800 dark:text-gray-100">Débit :</strong> Fonds bloqués pendant le traitement</span>
                </li>
                <li class="flex items-start space-x-3">
                    <span class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </span>
                    <span class="text-gray-600 dark:text-gray-300"><strong class="text-gray-800 dark:text-gray-100">Remboursement :</strong> Automatique en cas d'échec</span>
                </li>
            </ul>
        </div>

        <!-- Opérateurs supportés -->
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700/50 p-5">
            <h3 class="text-sm font-bold text-green-600 dark:text-green-400 mb-4 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                <span>Opérateurs supportés</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl p-3 text-center hover:scale-105 transition-transform duration-200 cursor-default">
                    <div class="text-2xl mb-1">🟠</div>
                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Orange Money</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">084/085/089</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl p-3 text-center hover:scale-105 transition-transform duration-200 cursor-default">
                    <div class="text-2xl mb-1">🟢</div>
                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">M-Pesa</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">081/082/083</p>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 rounded-xl p-3 text-center hover:scale-105 transition-transform duration-200 cursor-default">
                    <div class="text-2xl mb-1">🔴</div>
                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Airtel Money</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">097/098/099</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl p-3 text-center hover:scale-105 transition-transform duration-200 cursor-default">
                    <div class="text-2xl mb-1">🔵</div>
                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Africell</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">090/091/092</p>
                </div>
                <div class="bg-gradient-to-br from-cyan-50 to-sky-100 dark:from-cyan-900/30 dark:to-sky-800/30 rounded-xl p-3 text-center hover:scale-105 transition-transform duration-200 cursor-default">
                    <div class="text-2xl mb-1">⚡</div>
                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">CinetPay</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Orange, MTN, M-Pesa…</p>
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
                    <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Traitement en cours...</span>
                `;
            }
        });
    }

    // Sélection de la méthode de retrait (MaishaPay / CinetPay)
    const methodMeta = {
        maishapay: {
            active: 'border-green-500 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30',
            badge: 'border-green-500 bg-green-500',
            label: 'MaishaPay',
            text: "Retrait automatique. Fonds envoyés vers votre mobile en 2-10 min selon l'opérateur."
        },
        cinetpay: {
            active: 'border-blue-500 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30',
            badge: 'border-blue-500 bg-blue-500',
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
            card.className = base + (isActive ? methodMeta[m].active : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800');
            const badge = card.querySelector('.method-badge');
            badge.className = 'method-badge absolute top-2 right-2 w-4 h-4 rounded-full border-2 flex items-center justify-center ' +
                (isActive ? methodMeta[m].badge : 'border-gray-300 dark:border-gray-500');
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
