@extends('app')

@section('title', 'Mon wallet')

@section('content')
@php
    $usdBalance = $usdWallet?->balance ?? 0;
    $usdId = $usdWallet?->id;
    $cdfBalance = $cdfWallet?->balance ?? 0;
    $cdfId = $cdfWallet?->id;
@endphp
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <!-- En-tête avec gradient -->
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 rounded-2xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
                        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 110-6h.008A2.25 2.25 0 0021 6.008V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v.008c0 1.243 1.007 2.25 2.25 2.25H15a3 3 0 010 6H5.25A2.25 2.25 0 003 16.5v1.245c0 1.243 1.007 2.25 2.25 2.248h13.5A2.25 2.25 0 0021 17.745V12z"/></svg>
                                    </div>
                                    Mon Portefeuille
                                </h1>
                                <p class="text-emerald-100 mt-2 text-sm sm:text-base">Gérez vos finances en toute simplicité</p>
                            </div>
                            <button class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-white/25 active:scale-95 transition-all duration-200" onclick="refreshBalances()" id="refreshMainBtn">
                                <svg class="w-4 h-4" id="refreshMainIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.181-3.182"/></svg>
                                Actualiser
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cartes des soldes -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700/50 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-400 to-emerald-500"></div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <h2 class="text-gray-900 dark:text-white text-lg font-semibold">Dollar Américain</h2>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">USD</p>
                                    </div>
                                </div>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-50 overflow-hidden">
                                        <a href="{{ $usdWallet ? route('wallet.index') : '#' }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            Ajouter des fonds
                                        </a>
                                        <a href="{{ $usdWallet ? route('wallet.withdraw-funds', $usdWallet) : '#' }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                            Retirer des fonds
                                        </a>
                                        <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                        <a href="{{ $usdWallet ? route('wallet.transactions', $usdWallet) : '#' }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Voir l'historique
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center py-6">
                                <p class="text-4xl font-bold text-green-600 dark:text-green-400 mb-1 tabular-nums" id="usd-balance">
                                    ${{ number_format($usdBalance, 2, '.', ',') }}
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Solde disponible</p>
                            </div>

                            <div class="flex gap-3">
                                <a href="{{ $usdWallet ? route('wallet.index') : '#' }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-xl font-medium transition-all duration-200 active:scale-[0.98] shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    Ajouter
                                </a>
                                <a href="{{ $usdWallet ? route('wallet.withdraw-funds', $usdWallet) : '#' }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 py-3 px-4 rounded-xl font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                    Retirer
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700/50 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 to-yellow-500"></div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
                                    </div>
                                    <div>
                                        <h2 class="text-gray-900 dark:text-white text-lg font-semibold">Franc Congolais</h2>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">CDF</p>
                                    </div>
                                </div>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-50 overflow-hidden">
                                        <a href="{{ $cdfWallet ? route('wallet.index') : '#' }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            Ajouter des fonds
                                        </a>
                                        <a href="{{ $cdfWallet ? route('wallet.withdraw-funds', $cdfWallet) : '#' }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                            Retirer des fonds
                                        </a>
                                        <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                        <a href="{{ $cdfWallet ? route('wallet.transactions', $cdfWallet) : '#' }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Voir l'historique
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center py-6">
                                <p class="text-4xl font-bold text-amber-600 dark:text-amber-400 mb-1 tabular-nums" id="cdf-balance">
                                    {{ number_format($cdfBalance, 2, ',', ' ') }} FC
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Solde disponible</p>
                            </div>

                            <div class="flex gap-3">
                                <a href="{{ $cdfWallet ? route('wallet.index') : '#' }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white py-3 px-4 rounded-xl font-medium transition-all duration-200 active:scale-[0.98] shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    Ajouter
                                </a>
                                <a href="{{ $cdfWallet ? route('wallet.withdraw-funds', $cdfWallet) : '#' }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 py-3 px-4 rounded-xl font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                    Retirer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte de conversion de devises -->
                <div class="mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white">
                            <h2 class="text-xl font-bold flex items-center gap-2.5 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                Convertir entre devises (USD ⇄ CDF)
                            </h2>
                            <p class="text-emerald-100 text-sm">Convertissez facilement vos dollars en francs congolais et vice-versa</p>
                        </div>
                        <div class="p-6">
                            <div class="flex gap-3 mb-6">
                                <button type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200 text-sm font-medium"
                                        onclick="quickConvert('USD', 'CDF')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    USD → CDF
                                </button>
                                <button type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors duration-200 text-sm font-medium"
                                        onclick="quickConvert('CDF', 'USD')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    CDF → USD
                                </button>
                            </div>

                            <form id="conversionForm">
                                @csrf
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                    <div class="lg:col-span-5">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">De</label>
                                        <select class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary mb-3"
                                                id="fromWallet" name="from_wallet_id" required>
                                            <option value="{{ $usdId }}" data-currency="USD">
                                                USD - ${{ number_format($usdBalance, 2) }}
                                            </option>
                                            <option value="{{ $cdfId }}" data-currency="CDF">
                                                CDF - {{ number_format($cdfBalance, 2) }} FC
                                            </option>
                                        </select>
                                        <div class="flex">
                                            <input type="number"
                                                   class="flex-1 p-3 border border-gray-300 dark:border-gray-600 rounded-l-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                   id="fromAmount"
                                                   name="amount"
                                                   placeholder="0.00"
                                                   step="0.01"
                                                   min="0.01"
                                                   required>
                                            <span class="inline-flex items-center px-4 bg-gray-50 dark:bg-gray-900 border border-l-0 border-gray-300 rounded-r-lg text-gray-700 dark:text-gray-200 font-medium"
                                                  id="fromCurrency">USD</span>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            Solde disponible: <span id="fromBalance">${{ number_format($usdBalance, 2) }}</span>
                                        </p>
                                    </div>

                                    <div class="lg:col-span-2 flex items-center justify-center">
                                        <button type="button"
                                                class="w-12 h-12 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 transform hover:scale-110 active:scale-95 shadow-sm"
                                                id="swapBtn">
                                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                        </button>
                                    </div>

                                    <div class="lg:col-span-5">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Vers</label>
                                        <select class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary mb-3"
                                                id="toWallet" name="to_wallet_id" required>
                                            <option value="{{ $cdfId }}" data-currency="CDF">
                                                CDF - {{ number_format($cdfBalance, 2) }} FC
                                            </option>
                                            <option value="{{ $usdId }}" data-currency="USD">
                                                USD - ${{ number_format($usdBalance, 2) }}
                                            </option>
                                        </select>
                                        <div class="flex">
                                            <input type="text"
                                                   class="flex-1 p-3 border border-gray-300 rounded-l-lg bg-gray-50 dark:bg-gray-900"
                                                   id="toAmount"
                                                   placeholder="0.00"
                                                   readonly>
                                            <span class="inline-flex items-center px-4 bg-gray-50 dark:bg-gray-900 border border-l-0 border-gray-300 rounded-r-lg text-gray-700 dark:text-gray-200 font-medium"
                                                  id="toCurrency">CDF</span>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            Solde actuel: <span id="toBalance">{{ number_format($cdfBalance, 2) }} FC</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-xl p-4" id="rateAlert">
                                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                                                    <span class="text-gray-700 dark:text-gray-200">Taux actuel: <strong id="exchangeRate">Chargement...</strong></span>
                                                    <span id="rateSource" class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 px-2 py-0.5 rounded-full text-xs font-medium hidden">Temps réel</span>
                                                    <span id="rateFallback" class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 px-2 py-0.5 rounded-full text-xs font-medium hidden">Taux de secours</span>
                                                </div>
                                                <div class="flex flex-wrap gap-4">
                                                    <small class="text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                                        <strong>USD → CDF:</strong> × <span id="rateFwd">-</span>
                                                    </small>
                                                    <small class="text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                                                        <strong>CDF → USD:</strong> ÷ <span id="rateBwd">-</span>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <button type="button"
                                                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-800/30 text-emerald-700 dark:text-emerald-400 rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-800/50 transition-colors duration-200 text-sm"
                                                        id="refreshRateBtn" onclick="refreshExchangeRate()">
                                                    <svg class="w-3.5 h-3.5" id="refreshIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.181-3.182"/></svg>
                                                    Actualiser
                                                </button>
                                                <small class="text-gray-500 dark:text-gray-400 hidden lg:block">Mis à jour: <span id="lastUpdate">À l'instant</span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-emerald-700 hover:to-teal-700 transform hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 shadow-lg hover:shadow-xl"
                                            id="convertBtn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                        Convertir maintenant
                                    </button>
                                </div>

                                <div class="mt-6">
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z"/></svg>
                                            <strong>Exemples de conversion :</strong>
                                        </p>
                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-center">
                                            <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">$1 USD =</p>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" id="example1">2,650 FC</p>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">$10 USD =</p>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" id="example2">26,500 FC</p>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">10,000 FC =</p>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" id="example3">$3.77</p>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">50,000 FC =</p>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" id="example4">$18.87</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="conversionMessage" class="mt-6 hidden"></div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Transactions récentes -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                            <h2 class="text-xl font-bold flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-gray-900 dark:text-white">Transactions récentes</span>
                            </h2>
                            <div class="flex items-center gap-3">
                                <small class="text-gray-500 dark:text-gray-400">{{ $recentTransactions->total() }} transaction(s)</small>
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2.5 py-1 rounded-full text-xs font-medium">
                                    Page {{ $recentTransactions->currentPage() }}/{{ $recentTransactions->lastPage() }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 dark:bg-gray-100 text-white dark:text-gray-800 rounded-lg text-sm font-medium transition-colors duration-200"
                                    id="filterAll" onclick="filterTransactions('all')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                Toutes
                            </button>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200"
                                    id="filterCredit" onclick="filterTransactions('credit')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Crédits
                            </button>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-sm hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors duration-200"
                                    id="filterDebit" onclick="filterTransactions('debit')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                Débits
                            </button>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors duration-200"
                                    id="filterUSD" onclick="filterTransactions('USD')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                USD
                            </button>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-lg text-sm hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors duration-200"
                                    id="filterCDF" onclick="filterTransactions('CDF')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
                                CDF
                            </button>
                        </div>
                    </div>

                    <div class="p-0">
                        @if($recentTransactions->count() > 0)
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 sticky top-0">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-1/2">Description</th>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Montant</th>
                                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">Devise</th>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hidden xl:table-cell">Solde après</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($recentTransactions as $transaction)
                                            <tr class="hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('H:i') }}</div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    @php
                                                        $typeConfig = [
                                                            'credit' => ['class' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400', 'svgPath' => 'M12 4.5v15m7.5-7.5h-15'],
                                                            'debit' => ['class' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400', 'svgPath' => 'M19.5 12h-15'],
                                                        ];
                                                        $config = $typeConfig[$transaction->type] ?? ['class' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200', 'svgPath' => 'M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z'];
                                                    @endphp
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $config['svgPath'] }}"/></svg>
                                                        {{ ucfirst($transaction->type) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-gray-900 dark:text-white">{{ $transaction->description }}</div>
                                                    @if($transaction->reference)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Réf: {{ $transaction->reference }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                                        {{ $transaction->wallet->currency }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center hidden lg:table-cell">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->wallet->currency === 'USD' ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $transaction->wallet->currency }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right hidden xl:table-cell">
                                                    <div class="text-sm text-gray-900 dark:text-white">
                                                        {{ number_format($transaction->balance_after, 2) }} {{ $transaction->wallet->currency }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="block sm:hidden">
                                <div class="p-4 space-y-4">
                                    @foreach($recentTransactions as $transaction)
                                        @php
                                            $source = '';
                                            $svgPath = 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z';
                                            $badgeClass = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';

                                            if (str_contains(strtolower($transaction->description), 'vente')) {
                                                if (str_contains($transaction->description, 'commission')) {
                                                    $source = '💰 Commission Vente';
                                                    $svgPath = 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z';
                                                    $badgeClass = 'bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-400';
                                                } else {
                                                    $source = '🛍️ Vente Produit';
                                                    $svgPath = 'M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z';
                                                    $badgeClass = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400';
                                                }
                                            } elseif (str_contains(strtolower($transaction->description), 'parrainage')) {
                                                $source = '🤝 Argent Parrainage';
                                                $svgPath = 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z';
                                                $badgeClass = 'bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-400';
                                            } elseif (str_contains(strtolower($transaction->description), 'conversion')) {
                                                $source = '🔄 Conversion de Devise';
                                                $svgPath = 'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5';
                                                $badgeClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400';
                                            } elseif (str_contains(strtolower($transaction->description), 'recharge')) {
                                                $source = '📱 Recharge Mobile Money';
                                                $svgPath = 'M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3';
                                                $badgeClass = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400';
                                            } else {
                                                $source = '💳 Transaction Générale';
                                                $badgeClass = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
                                            }
                                        @endphp

                                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow duration-200 transaction-card">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl {{ $transaction->type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-5 h-5 {{ $transaction->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $svgPath }}"/></svg>
                                                    </div>
                                                    <div>
                                                        <h6 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $source }}</h6>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $transaction->wallet->currency }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-lg font-bold tabular-nums {{ $transaction->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                        {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ $transaction->description }}</p>
                                                @if($transaction->reference)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Réf: {{ $transaction->reference }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if($recentTransactions->hasPages())
                                <div class="flex flex-col sm:flex-row justify-between items-center p-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center mb-4 sm:mb-0">
                                        <small class="text-gray-600 dark:text-gray-300">
                                            Affichage de {{ $recentTransactions->firstItem() }} à {{ $recentTransactions->lastItem() }}
                                            sur {{ $recentTransactions->total() }} transactions
                                        </small>
                                    </div>
                                    <div class="flex gap-2">
                                        {{ $recentTransactions->links() }}
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-16 px-4">
                                <div class="mb-6">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                                        <svg class="w-9 h-9 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Aucune transaction</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">Vos transactions apparaîtront ici une fois que vous aurez effectué des opérations.</p>

                                <div class="mb-8">
                                    <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-6">💡 Comment recevoir de l'argent dans votre portefeuille :</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-w-4xl mx-auto">
                                        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/10 p-4 rounded-xl border border-green-200 dark:border-green-800/30">
                                            <div class="text-center">
                                                <div class="inline-flex items-center justify-center w-10 h-10 bg-green-200/60 dark:bg-green-800/30 rounded-xl mb-2">
                                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                                </div>
                                                <h5 class="font-semibold text-green-800 dark:text-green-400">Vendre des articles</h5>
                                                <p class="text-sm text-green-700 dark:text-green-500">Recevez l'argent de vos ventes</p>
                                            </div>
                                        </div>
                                        <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-900/10 p-4 rounded-xl border border-primary-200 dark:border-primary-800/30">
                                            <div class="text-center">
                                                <div class="inline-flex items-center justify-center w-10 h-10 bg-primary-200/60 dark:bg-primary-800/30 rounded-xl mb-2">
                                                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                                </div>
                                                <h5 class="font-semibold text-primary-800 dark:text-primary-400">Commissions</h5>
                                                <p class="text-sm text-primary-700 dark:text-primary-500">Gagnez des commissions sur les ventes</p>
                                            </div>
                                        </div>
                                        <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-900/10 p-4 rounded-xl border border-primary-200 dark:border-primary-800/30">
                                            <div class="text-center">
                                                <div class="inline-flex items-center justify-center w-10 h-10 bg-primary-200/60 dark:bg-primary-800/30 rounded-xl mb-2">
                                                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                                </div>
                                                <h5 class="font-semibold text-primary-800 dark:text-primary-400">Parrainage</h5>
                                                <p class="text-sm text-primary-700 dark:text-primary-500">Bonus de parrainage d'amis</p>
                                            </div>
                                        </div>
                                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-900/10 p-4 rounded-xl border border-yellow-200 dark:border-yellow-800/30">
                                            <div class="text-center">
                                                <div class="inline-flex items-center justify-center w-10 h-10 bg-yellow-200/60 dark:bg-yellow-800/30 rounded-xl mb-2">
                                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                                </div>
                                                <h5 class="font-semibold text-yellow-800 dark:text-yellow-400">Bonus</h5>
                                                <p class="text-sm text-yellow-700 dark:text-yellow-500">Récompenses et bonus spéciaux</p>
                                            </div>
                                        </div>
                                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/10 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800/30">
                                            <div class="text-center">
                                                <div class="inline-flex items-center justify-center w-10 h-10 bg-emerald-200/60 dark:bg-emerald-800/30 rounded-xl mb-2">
                                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                                                </div>
                                                <h5 class="font-semibold text-emerald-800 dark:text-emerald-400">Recharge Mobile</h5>
                                                <p class="text-sm text-emerald-700 dark:text-emerald-500">Ajout de fonds via Mobile Money</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                    <a href="{{ $usdWallet ? route('wallet.index') : '#' }}"
                                       class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 active:scale-[0.98] shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Ajouter des fonds USD
                                    </a>
                                    <a href="{{ $cdfWallet ? route('wallet.index') : '#' }}"
                                       class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 active:scale-[0.98] shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Ajouter des fonds CDF
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
let currentRate = 2650;
let rateLastUpdate = null;
let isFallbackRate = false;

async function fetchExchangeRate() {
    try {
        const response = await fetch('{{ route("exchange.rate") }}');
        const data = await response.json();

        if (data.status === 'success') {
            currentRate = data.rate;
            rateLastUpdate = new Date(data.updated_at);
            isFallbackRate = data.fallback || false;

            updateRateDisplay();
            updateExamples();
            updateRateIndicators();
        }
    } catch (error) {
        console.error('Erreur lors de la récupération du taux:', error);
        showRateError();
    }
}

async function refreshExchangeRate() {
    const refreshBtn = document.getElementById('refreshRateBtn');
    const refreshIcon = document.getElementById('refreshIcon');

    refreshIcon.classList.add('animate-spin');
    refreshBtn.disabled = true;

    try {
        const response = await fetch('{{ route("exchange.rate") }}', {
            method: 'GET',
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        });

        const data = await response.json();

        if (data.status === 'success') {
            currentRate = data.rate;
            rateLastUpdate = new Date();
            isFallbackRate = data.fallback || false;

            updateRateDisplay();
            updateExamples();
            updateRateIndicators();

            showNotification('success', `Taux de change mis à jour: 1 USD = ${currentRate.toLocaleString('fr-FR')} CDF`);
        }
    } catch (error) {
        console.error('Erreur lors du rafraîchissement:', error);
        showNotification('error', 'Impossible de rafraîchir le taux de change');
    } finally {
        refreshIcon.classList.remove('animate-spin');
        refreshBtn.disabled = false;
    }
}

function updateRateIndicators() {
    const rateSource = document.getElementById('rateSource');
    const rateFallback = document.getElementById('rateFallback');
    const lastUpdate = document.getElementById('lastUpdate');
    const rateAlert = document.getElementById('rateAlert');
    const rateFwd = document.getElementById('rateFwd');
    const rateBwd = document.getElementById('rateBwd');

    if (isFallbackRate) {
        rateSource.classList.add('hidden');
        rateFallback.classList.remove('hidden');
        rateAlert.classList.remove('bg-emerald-50', 'border-emerald-200');
        rateAlert.classList.add('bg-yellow-50', 'border-yellow-200');
    } else {
        rateSource.classList.remove('hidden');
        rateFallback.classList.add('hidden');
        rateAlert.classList.remove('bg-yellow-50', 'border-yellow-200');
        rateAlert.classList.add('bg-emerald-50', 'border-emerald-200');
    }

    if (rateLastUpdate) {
        const now = new Date();
        const diff = Math.floor((now - rateLastUpdate) / 1000);

        if (diff < 60) {
            lastUpdate.textContent = `Il y a ${diff}s`;
        } else if (diff < 3600) {
            lastUpdate.textContent = `Il y a ${Math.floor(diff / 60)}min`;
        } else {
            lastUpdate.textContent = `Il y a ${Math.floor(diff / 3600)}h`;
        }
    } else {
        lastUpdate.textContent = 'À l\'instant';
    }

    if (rateFwd) rateFwd.textContent = currentRate.toLocaleString('fr-FR');
    if (rateBwd) rateBwd.textContent = currentRate.toLocaleString('fr-FR');
}

function showRateError() {
    const rateAlert = document.getElementById('rateAlert');
    rateAlert.classList.remove('bg-emerald-50', 'border-emerald-200');
    rateAlert.classList.add('bg-red-50', 'border-red-200');
    document.getElementById('exchangeRate').textContent = 'Erreur de chargement';
}

function updateExamples() {
    const example1 = document.getElementById('example1');
    const example2 = document.getElementById('example2');
    const example3 = document.getElementById('example3');
    const example4 = document.getElementById('example4');

    if (example1) example1.textContent = (1 * currentRate).toLocaleString('fr-FR') + ' FC';
    if (example2) example2.textContent = (10 * currentRate).toLocaleString('fr-FR') + ' FC';
    if (example3) example3.textContent = '$' + (10000 / currentRate).toFixed(2);
    if (example4) example4.textContent = '$' + (50000 / currentRate).toFixed(2);
}

function updateRateDisplay() {
    const fromWallet = document.getElementById('fromWallet');
    const toWallet = document.getElementById('toWallet');

    if (!fromWallet || !toWallet) return;

    const fromCurrency = fromWallet.selectedOptions[0].dataset.currency;
    const toCurrency = toWallet.selectedOptions[0].dataset.currency;

    let rateText;
    if (fromCurrency === 'USD' && toCurrency === 'CDF') {
        rateText = `1 USD = ${currentRate.toLocaleString('fr-FR')} CDF`;
    } else if (fromCurrency === 'CDF' && toCurrency === 'USD') {
        rateText = `${currentRate.toLocaleString('fr-FR')} CDF = 1 USD`;
    } else {
        rateText = '1:1 (même devise)';
    }

    document.getElementById('exchangeRate').textContent = rateText;
}

function showNotification(type, message) {
    const alertClass = type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
    const svgIcon = type === 'success'
        ? '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        : '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>';

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full ${alertClass} border rounded-xl p-4 shadow-lg transform transition-all duration-300`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            ${svgIcon}
            <span class="text-sm">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto p-1 hover:opacity-70 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function calculateConversion() {
    const fromAmount = parseFloat(document.getElementById('fromAmount').value) || 0;
    const fromCurrency = document.getElementById('fromWallet').selectedOptions[0].dataset.currency;
    const toCurrency = document.getElementById('toWallet').selectedOptions[0].dataset.currency;

    if (fromAmount <= 0) {
        document.getElementById('toAmount').value = '';
        return;
    }

    let convertedAmount;
    if (fromCurrency === 'USD' && toCurrency === 'CDF') {
        convertedAmount = fromAmount * currentRate;
    } else if (fromCurrency === 'CDF' && toCurrency === 'USD') {
        convertedAmount = fromAmount / currentRate;
    } else {
        convertedAmount = fromAmount;
    }

    document.getElementById('toAmount').value = convertedAmount.toFixed(2);
}

function swapWallets() {
    const fromWallet = document.getElementById('fromWallet');
    const toWallet = document.getElementById('toWallet');

    const tempValue = fromWallet.value;
    const tempIndex = fromWallet.selectedIndex;

    fromWallet.value = toWallet.value;
    fromWallet.selectedIndex = toWallet.selectedIndex;

    toWallet.value = tempValue;
    toWallet.selectedIndex = tempIndex;

    updateCurrencyLabels();
    updateBalanceLabels();
    calculateConversion();
}

function quickConvert(fromCurrency, toCurrency) {
    const fromWallet = document.getElementById('fromWallet');
    const toWallet = document.getElementById('toWallet');

    const fromOptions = Array.from(fromWallet.options);
    const toOptions = Array.from(toWallet.options);

    const fromOption = fromOptions.find(opt => opt.dataset.currency === fromCurrency);
    const toOption = toOptions.find(opt => opt.dataset.currency === toCurrency);

    if (fromOption && toOption) {
        fromWallet.value = fromOption.value;
        toWallet.value = toOption.value;

        updateCurrencyLabels();
        updateBalanceLabels();
        calculateConversion();

        document.getElementById('fromAmount').focus();
    }
}

function updateCurrencyLabels() {
    const fromCurrency = document.getElementById('fromWallet').selectedOptions[0].dataset.currency;
    const toCurrency = document.getElementById('toWallet').selectedOptions[0].dataset.currency;

    document.getElementById('fromCurrency').textContent = fromCurrency;
    document.getElementById('toCurrency').textContent = toCurrency;

    updateRateDisplay();
}

function updateBalanceLabels() {
    const fromWallet = document.getElementById('fromWallet');
    const toWallet = document.getElementById('toWallet');

    const fromBalance = fromWallet.selectedOptions[0].text.split(' - ')[1];
    const toBalance = toWallet.selectedOptions[0].text.split(' - ')[1];

    document.getElementById('fromBalance').textContent = fromBalance;
    document.getElementById('toBalance').textContent = toBalance;
}

async function submitConversion(event) {
    event.preventDefault();

    const convertBtn = document.getElementById('convertBtn');
    const messageDiv = document.getElementById('conversionMessage');
    const originalBtnText = convertBtn.innerHTML;

    const fromAmount = parseFloat(document.getElementById('fromAmount').value);
    if (!fromAmount || fromAmount <= 0) {
        showMessage('error', 'Veuillez entrer un montant valide');
        return;
    }

    convertBtn.disabled = true;
    convertBtn.innerHTML = '<svg class="w-5 h-5 animate-spin inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Conversion en cours...';

    try {
        const formData = new FormData(event.target);

        const response = await fetch('{{ route("wallet.convert") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                from_wallet_id: formData.get('from_wallet_id'),
                to_wallet_id: formData.get('to_wallet_id'),
                amount: fromAmount
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            showMessage('success', `
                <strong>Conversion réussie !</strong><br>
                ${data.amount} ${data.from_currency} → ${data.converted_amount} ${data.to_currency}<br>
                <small>Taux appliqué: ${data.rate.toLocaleString('fr-FR')}</small>
            `);

            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showMessage('error', data.message || 'Erreur lors de la conversion');
            convertBtn.disabled = false;
            convertBtn.innerHTML = originalBtnText;
        }
    } catch (error) {
        console.error('Erreur:', error);
        showMessage('error', 'Erreur de connexion au serveur');
        convertBtn.disabled = false;
        convertBtn.innerHTML = originalBtnText;
    }
}

function showMessage(type, message) {
    const messageDiv = document.getElementById('conversionMessage');
    const alertClass = type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
    const svgIcon = type === 'success'
        ? '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        : '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>';

    messageDiv.innerHTML = `
        <div class="border rounded-xl p-4 ${alertClass}">
            <div class="flex items-center gap-2">
                ${svgIcon}
                <span>${message}</span>
            </div>
        </div>
    `;
    messageDiv.classList.remove('hidden');
}

function refreshBalances() {
    const usdBalance = document.getElementById('usd-balance');
    const cdfBalance = document.getElementById('cdf-balance');
    const refreshBtn = document.getElementById('refreshMainBtn');
    const refreshIcon = document.getElementById('refreshMainIcon');

    const spinner = '<svg class="w-6 h-6 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    usdBalance.innerHTML = spinner;
    cdfBalance.innerHTML = spinner;
    if (refreshIcon) refreshIcon.classList.add('animate-spin');

    setTimeout(() => {
        location.reload();
    }, 500);
}

function filterTransactions(type) {
    const desktopRows = document.querySelectorAll('tbody tr');
    const mobileCards = document.querySelectorAll('.transaction-card');
    const buttons = document.querySelectorAll('[id^="filter"]');

    buttons.forEach(btn => {
        btn.classList.remove('bg-gray-800', 'text-white');
        btn.classList.add('bg-gray-50 dark:bg-gray-900', 'text-gray-700 dark:text-gray-200');
    });

    const activeButton = document.getElementById('filter' + type.charAt(0).toUpperCase() + type.slice(1));
    if (activeButton) {
        activeButton.classList.remove('bg-gray-50', 'dark:bg-gray-900', 'text-gray-700', 'dark:text-gray-200');
        activeButton.classList.add('bg-gray-800', 'text-white');
    }

    let visibleCount = 0;

    desktopRows.forEach(row => {
        const shouldShow = shouldShowTransaction(row, type);
        if (shouldShow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    mobileCards.forEach(card => {
        const shouldShow = shouldShowTransaction(card, type);
        if (shouldShow) {
            card.style.display = '';
            if (desktopRows.length === 0) visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
}

function shouldShowTransaction(element, type) {
    if (type === 'all') {
        return true;
    }

    if (type === 'credit' || type === 'debit') {
        const badge = element.querySelector('.bg-green-100, .bg-red-100');
        if (!badge) return false;
        const isCredit = badge.classList.contains('bg-green-100');
        return (type === 'credit' && isCredit) || (type === 'debit' && !isCredit);
    }

    if (type === 'USD' || type === 'CDF') {
        const currencyBadges = element.querySelectorAll('[class*="bg-emerald-100"], [class*="bg-yellow-100"]');
        let hasCurrency = false;
        currencyBadges.forEach(badge => {
            if (badge.textContent.trim() === type) {
                hasCurrency = true;
            }
        });
        return hasCurrency;
    }

    return false;
}

document.addEventListener('DOMContentLoaded', function() {
    fetchExchangeRate();

    const fromAmountInput = document.getElementById('fromAmount');
    const fromWalletSelect = document.getElementById('fromWallet');
    const toWalletSelect = document.getElementById('toWallet');
    const swapBtn = document.getElementById('swapBtn');
    const conversionForm = document.getElementById('conversionForm');

    if (fromAmountInput) fromAmountInput.addEventListener('input', calculateConversion);
    if (fromWalletSelect) fromWalletSelect.addEventListener('change', function() {
        updateCurrencyLabels();
        updateBalanceLabels();
        calculateConversion();
    });
    if (toWalletSelect) toWalletSelect.addEventListener('change', function() {
        updateCurrencyLabels();
        updateBalanceLabels();
        calculateConversion();
    });
    if (swapBtn) swapBtn.addEventListener('click', swapWallets);
    if (conversionForm) conversionForm.addEventListener('submit', submitConversion);
});
</script>
@endsection
