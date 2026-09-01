@extends('app')

@section('title', 'Mon Portefeuille')

@section('content')
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- En-tête violet -->
        <div class="mb-8">
            <div class="relative overflow-hidden rounded-2xl border border-vinted-primary-400 bg-vinted-primary-600 shadow-lg shadow-vinted-primary/30">
                <div class="absolute -top-10 -right-10 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-16 -left-6 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 text-xl text-white">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Mon Portefeuille</h1>
                            <p class="mt-1 text-sm text-vinted-primary-100">Gérez vos finances en toute simplicité</p>
                        </div>
                    </div>
                    <button class="inline-flex items-center gap-2 self-start rounded-lg bg-white/15 px-4 py-2.5 text-sm font-medium text-white backdrop-blur-sm transition-colors hover:bg-white/25 sm:self-auto" onclick="refreshBalances()" id="refreshMainBtn">
                        <i class="fas fa-rotate-right" id="refreshMainIcon"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Cartes des soldes -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
            <!-- Wallet USD -->
            <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition-shadow dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 to-emerald-600"></div>
                <div class="p-6">
                    <div class="mb-6 flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <i class="fas fa-dollar-sign text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">Dollar Américain</h2>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">USD</p>
                            </div>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-zinc-100 text-zinc-500 transition-colors hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 z-50 mt-2 w-56 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-800">
                                <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-700/60">
                                    <i class="fas fa-plus w-4 text-emerald-500"></i>
                                    Ajouter des fonds
                                </a>
                                <a href="{{ route('wallet.withdraw-funds', $usdWallet) }}" class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-700/60">
                                    <i class="fas fa-minus w-4 text-red-500"></i>
                                    Retirer des fonds
                                </a>
                                <div class="border-t border-zinc-100 dark:border-zinc-700"></div>
                                <a href="{{ route('wallet.transactions', $usdWallet) }}" class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-700/60">
                                    <i class="fas fa-clock-rotate-left w-4 text-vinted-primary-500"></i>
                                    Voir l'historique
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="py-5 text-center">
                        <p class="mb-1 text-4xl font-bold tabular-nums text-emerald-600 dark:text-emerald-400" id="usd-balance">
                            ${{ number_format($usdWallet->balance, 2, '.', ',') }}
                        </p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Solde disponible</p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('wallet.index') }}"
                           class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 font-medium text-white shadow-sm transition-all duration-200 hover:bg-emerald-700 active:scale-[0.98]">
                            <i class="fas fa-plus"></i>
                            Ajouter
                        </a>
                        <a href="{{ route('wallet.withdraw-funds', $usdWallet) }}"
                           class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-zinc-200 px-4 py-3 font-medium text-zinc-700 transition-all duration-200 hover:bg-zinc-50 active:scale-[0.98] dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                            <i class="fas fa-minus text-red-500"></i>
                            Retirer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Wallet CDF -->
            <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition-shadow dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-400 to-yellow-500"></div>
                <div class="p-6">
                    <div class="mb-6 flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                <i class="fas fa-coins text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">Franc Congolais</h2>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">CDF</p>
                            </div>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-zinc-100 text-zinc-500 transition-colors hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 z-50 mt-2 w-56 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-800">
                                <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-700/60">
                                    <i class="fas fa-plus w-4 text-emerald-500"></i>
                                    Ajouter des fonds
                                </a>
                                <a href="{{ route('wallet.withdraw-funds', $cdfWallet) }}" class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-700/60">
                                    <i class="fas fa-minus w-4 text-red-500"></i>
                                    Retirer des fonds
                                </a>
                                <div class="border-t border-zinc-100 dark:border-zinc-700"></div>
                                <a href="{{ route('wallet.transactions', $cdfWallet) }}" class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-700/60">
                                    <i class="fas fa-clock-rotate-left w-4 text-vinted-primary-500"></i>
                                    Voir l'historique
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="py-5 text-center">
                        <p class="mb-1 text-4xl font-bold tabular-nums text-amber-600 dark:text-amber-400" id="cdf-balance">
                            {{ number_format($cdfWallet->balance, 2, ',', ' ') }} FC
                        </p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Solde disponible</p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('wallet.index') }}"
                           class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-3 font-medium text-white shadow-sm transition-all duration-200 hover:bg-amber-600 active:scale-[0.98]">
                            <i class="fas fa-plus"></i>
                            Ajouter
                        </a>
                        <a href="{{ route('wallet.withdraw-funds', $cdfWallet) }}"
                           class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-zinc-200 px-4 py-3 font-medium text-zinc-700 transition-all duration-200 hover:bg-zinc-50 active:scale-[0.98] dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                            <i class="fas fa-minus text-red-500"></i>
                            Retirer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte de conversion de devises -->
        <div class="mb-8 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="bg-vinted-primary-600 px-6 py-5 text-white">
                <h2 class="flex items-center gap-2.5 text-xl font-bold tracking-tight">
                    <i class="fas fa-arrow-right-arrow-left"></i>
                    Convertir entre devises (USD ⇄ CDF)
                </h2>
                <p class="mt-1 text-sm text-vinted-primary-100">Convertissez facilement vos dollars en francs congolais et vice-versa</p>
            </div>
            <div class="p-6">
                <!-- Options de conversion rapide -->
                <div class="mb-6 flex gap-3">
                    <button type="button"
                            class="inline-flex items-center gap-2 rounded-lg bg-vinted-primary-50 px-4 py-2 text-sm font-medium text-vinted-primary-700 transition-colors hover:bg-vinted-primary-100 dark:bg-vinted-primary-500/10 dark:text-vinted-primary-300"
                            onclick="quickConvert('USD', 'CDF')">
                        <i class="fas fa-arrow-right-long"></i>
                        USD → CDF
                    </button>
                    <button type="button"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 transition-colors hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-300"
                            onclick="quickConvert('CDF', 'USD')">
                        <i class="fas fa-arrow-right-long"></i>
                        CDF → USD
                    </button>
                </div>

                <form id="conversionForm">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                        <!-- Source -->
                        <div class="lg:col-span-5">
                            <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-200">De</label>
                            <select class="mb-3 w-full rounded-lg border border-zinc-300 bg-white p-3 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800"
                                    id="fromWallet" name="from_wallet_id" required>
                                <option value="{{ $usdWallet->id }}" data-currency="USD">
                                    USD - ${{ number_format($usdWallet->balance, 2) }}
                                </option>
                                <option value="{{ $cdfWallet->id }}" data-currency="CDF">
                                    CDF - {{ number_format($cdfWallet->balance, 2) }} FC
                                </option>
                            </select>
                            <div class="flex">
                                <input type="number"
                                       class="flex-1 rounded-l-lg border border-zinc-300 bg-white p-3 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800"
                                       id="fromAmount"
                                       name="amount"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0.01"
                                       required>
                                <span class="inline-flex items-center rounded-r-lg border border-l-0 border-zinc-300 bg-zinc-50 px-4 font-medium text-zinc-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200"
                                      id="fromCurrency">USD</span>
                            </div>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                Solde disponible: <span id="fromBalance">${{ number_format($usdWallet->balance, 2) }}</span>
                            </p>
                        </div>

                        <!-- Bouton d'échange -->
                        <div class="flex items-center justify-center lg:col-span-2">
                            <button type="button"
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-vinted-primary-600 text-white shadow-sm transition-all duration-300 hover:scale-110 hover:bg-vinted-primary-700 active:scale-95"
                                    id="swapBtn">
                                <i class="fas fa-arrows-rotate"></i>
                            </button>
                        </div>

                        <!-- Destination -->
                        <div class="lg:col-span-5">
                            <label class="mb-2 block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Vers</label>
                            <select class="mb-3 w-full rounded-lg border border-zinc-300 bg-white p-3 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800"
                                    id="toWallet" name="to_wallet_id" required>
                                <option value="{{ $cdfWallet->id }}" data-currency="CDF">
                                    CDF - {{ number_format($cdfWallet->balance, 2) }} FC
                                </option>
                                <option value="{{ $usdWallet->id }}" data-currency="USD">
                                    USD - ${{ number_format($usdWallet->balance, 2) }}
                                </option>
                            </select>
                            <div class="flex">
                                <input type="text"
                                       class="flex-1 rounded-l-lg border border-zinc-300 bg-zinc-50 p-3 dark:border-zinc-600 dark:bg-zinc-900"
                                       id="toAmount"
                                       placeholder="0.00"
                                       readonly>
                                <span class="inline-flex items-center rounded-r-lg border border-l-0 border-zinc-300 bg-zinc-50 px-4 font-medium text-zinc-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200"
                                      id="toCurrency">CDF</span>
                            </div>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                Solde actuel: <span id="toBalance">{{ number_format($cdfWallet->balance, 2) }} FC</span>
                            </p>
                        </div>
                    </div>

                    <!-- Taux de change -->
                    <div class="mt-6">
                        <div class="rounded-xl border border-vinted-primary-200 bg-vinted-primary-50 p-4 dark:border-vinted-primary-500/30 dark:bg-vinted-primary-500/5" id="rateAlert">
                            <div class="flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-2">
                                        <i class="fas fa-circle-info text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                                        <span class="text-zinc-700 dark:text-zinc-200">Taux actuel: <strong id="exchangeRate">Chargement...</strong></span>
                                        <span id="rateSource" class="hidden rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            Temps réel
                                        </span>
                                        <span id="rateFallback" class="hidden rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-500/10 dark:text-amber-400">
                                            Taux de secours
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-4">
                                        <small class="flex items-center gap-1 text-zinc-600 dark:text-zinc-300">
                                            <i class="fas fa-arrow-right-long text-xs"></i>
                                            <strong>USD → CDF:</strong> × <span id="rateFwd">-</span>
                                        </small>
                                        <small class="flex items-center gap-1 text-zinc-600 dark:text-zinc-300">
                                            <i class="fas fa-arrow-left-long text-xs"></i>
                                            <strong>CDF → USD:</strong> ÷ <span id="rateBwd">-</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button"
                                            class="inline-flex items-center gap-2 rounded-lg bg-vinted-primary-100 px-3 py-1.5 text-sm text-vinted-primary-700 transition-colors hover:bg-vinted-primary-200 dark:bg-vinted-primary-500/10 dark:text-vinted-primary-300"
                                            id="refreshRateBtn" onclick="refreshExchangeRate()">
                                        <i class="fas fa-rotate-right text-xs" id="refreshIcon"></i>
                                        Actualiser
                                    </button>
                                    <small class="hidden text-zinc-500 dark:text-zinc-400 lg:block">
                                        Mis à jour: <span id="lastUpdate">À l'instant</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton de conversion -->
                    <div class="mt-6">
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-vinted-primary-600 px-6 py-4 text-lg font-semibold text-white shadow-md transition-all duration-200 hover:bg-vinted-primary-700 active:scale-[0.99]"
                                id="convertBtn">
                            <i class="fas fa-arrow-right-arrow-left"></i>
                            Convertir maintenant
                        </button>
                    </div>

                    <!-- Exemples de conversion -->
                    <div class="mt-6">
                        <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800/50">
                            <p class="mb-3 flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                                <i class="fas fa-calculator text-vinted-primary-500"></i>
                                <strong>Exemples de conversion :</strong>
                            </p>
                            <div class="grid grid-cols-2 gap-4 text-center lg:grid-cols-4">
                                <div class="rounded-lg border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                                    <p class="mb-1 text-xs text-zinc-500 dark:text-zinc-400">$1 USD =</p>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white" id="example1">2,650 FC</p>
                                </div>
                                <div class="rounded-lg border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                                    <p class="mb-1 text-xs text-zinc-500 dark:text-zinc-400">$10 USD =</p>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white" id="example2">26,500 FC</p>
                                </div>
                                <div class="rounded-lg border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                                    <p class="mb-1 text-xs text-zinc-500 dark:text-zinc-400">10,000 FC =</p>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white" id="example3">$3.77</p>
                                </div>
                                <div class="rounded-lg border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                                    <p class="mb-1 text-xs text-zinc-500 dark:text-zinc-400">50,000 FC =</p>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white" id="example4">$18.87</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div id="conversionMessage" class="mt-6 hidden"></div>
                </form>
            </div>
        </div>

        <!-- Transactions récentes -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 p-6 dark:border-zinc-800">
                <div class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <h2 class="flex items-center gap-2.5 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-vinted-primary-50 text-vinted-primary-500 dark:bg-vinted-primary-500/10">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>
                        Transactions récentes
                    </h2>
                    <div class="flex items-center gap-3">
                        <small class="text-zinc-500 dark:text-zinc-400">{{ $recentTransactions->total() }} transaction(s)</small>
                        <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            Page {{ $recentTransactions->currentPage() }}/{{ $recentTransactions->lastPage() }}
                        </span>
                    </div>
                </div>

                <!-- Filtres rapides -->
                <div class="flex flex-wrap gap-2">
                    <button class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-800 px-3 py-1.5 text-sm font-medium text-white transition-colors duration-200 dark:bg-zinc-100 dark:text-zinc-800"
                            id="filterAll" onclick="filterTransactions('all')">
                        <i class="fas fa-bars-staggered text-xs"></i>
                        Toutes
                    </button>
                    <button class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-sm text-green-700 transition-colors duration-200 hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400"
                            id="filterCredit" onclick="filterTransactions('credit')">
                        <i class="fas fa-plus text-xs"></i>
                        Crédits
                    </button>
                    <button class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-sm text-red-700 transition-colors duration-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                            id="filterDebit" onclick="filterTransactions('debit')">
                        <i class="fas fa-minus text-xs"></i>
                        Débits
                    </button>
                    <button class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-sm text-blue-700 transition-colors duration-200 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                            id="filterUSD" onclick="filterTransactions('USD')">
                        <i class="fas fa-dollar-sign text-xs"></i>
                        USD
                    </button>
                    <button class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 text-sm text-amber-700 transition-colors duration-200 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400"
                            id="filterCDF" onclick="filterTransactions('CDF')">
                        <i class="fas fa-coins text-xs"></i>
                        CDF
                    </button>
                </div>
            </div>

            <div class="p-0">
                @if($recentTransactions->count() > 0)
                    <!-- Version Desktop/Tablet - Tableau -->
                    <div class="hidden overflow-x-auto sm:block">
                        <table class="w-full">
                            <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Type</th>
                                    <th class="w-1/2 px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Description</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Montant</th>
                                    <th class="hidden px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 lg:table-cell">Devise</th>
                                    <th class="hidden px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 xl:table-cell">Solde après</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach($recentTransactions as $transaction)
                                    <tr class="transition-colors duration-200 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                                {{ $transaction->created_at->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $transaction->created_at->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $typeConfig = [
                                                    'credit' => ['class' => 'bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400', 'icon' => 'fas fa-plus'],
                                                    'debit' => ['class' => 'bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400', 'icon' => 'fas fa-minus'],
                                                ];
                                                $config = $typeConfig[$transaction->type] ?? ['class' => 'bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200', 'icon' => 'fas fa-circle'];
                                            @endphp
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium {{ $config['class'] }}">
                                                <i class="{{ $config['icon'] }} text-[10px]"></i>
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-zinc-900 dark:text-white">{{ $transaction->description }}</div>
                                            @if($transaction->reference)
                                                <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Réf: {{ $transaction->reference }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                                {{ $transaction->wallet->currency }}
                                            </div>
                                        </td>
                                        <td class="hidden px-6 py-4 text-center lg:table-cell">
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $transaction->wallet->currency === 'USD' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $transaction->wallet->currency }}
                                            </span>
                                        </td>
                                        <td class="hidden px-6 py-4 text-right xl:table-cell">
                                            <div class="text-sm text-zinc-900 dark:text-white">
                                                {{ number_format($transaction->balance_after, 2) }} {{ $transaction->wallet->currency }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Version Mobile - Cards -->
                    <div class="block sm:hidden">
                        <div class="space-y-4 p-4">
                            @foreach($recentTransactions as $transaction)
                                @php
                                    $source = '';
                                    $iconClass = 'fas fa-layer-group';
                                    $badgeClass = 'bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200';

                                    if (str_contains(strtolower($transaction->description), 'vente')) {
                                        if (str_contains($transaction->description, 'commission')) {
                                            $source = 'Commission Vente';
                                            $iconClass = 'fas fa-star';
                                            $badgeClass = 'bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400';
                                        } else {
                                            $source = 'Vente Produit';
                                            $iconClass = 'fas fa-bag-shopping';
                                            $badgeClass = 'bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400';
                                        }
                                    } elseif (str_contains(strtolower($transaction->description), 'parrainage')) {
                                        $source = 'Argent Parrainage';
                                        $iconClass = 'fas fa-user-group';
                                        $badgeClass = 'bg-vinted-primary-100 dark:bg-vinted-primary-500/10 text-vinted-primary-800 dark:text-vinted-primary-400';
                                    } elseif (str_contains(strtolower($transaction->description), 'conversion')) {
                                        $source = 'Conversion de Devise';
                                        $iconClass = 'fas fa-arrow-right-arrow-left';
                                        $badgeClass = 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400';
                                    } elseif (str_contains(strtolower($transaction->description), 'recharge')) {
                                        $source = 'Recharge Mobile Money';
                                        $iconClass = 'fas fa-mobile-screen';
                                        $badgeClass = 'bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400';
                                    } else {
                                        $source = 'Transaction Générale';
                                        $badgeClass = 'bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200';
                                    }
                                @endphp

                                <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900 transaction-card">
                                    <div class="mb-3 flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg {{ $transaction->type === 'credit' ? 'bg-green-100 dark:bg-green-500/10' : 'bg-red-100 dark:bg-red-500/10' }}">
                                                <i class="{{ $iconClass }} {{ $transaction->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $source }}</h6>
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                                    {{ $transaction->wallet->currency }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold tabular-nums {{ $transaction->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                            </div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-2 text-sm text-zinc-700 dark:text-zinc-300">{{ $transaction->description }}</p>
                                        @if($transaction->reference)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Réf: {{ $transaction->reference }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($recentTransactions->hasPages())
                        <div class="flex flex-col items-center justify-between border-t border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-800 dark:bg-zinc-800/50 sm:flex-row">
                            <div class="mb-4 flex items-center sm:mb-0">
                                <small class="text-zinc-600 dark:text-zinc-300">
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
                    <!-- État vide -->
                    <div class="px-4 py-16 text-center">
                        <div class="mb-6">
                            <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                                <i class="fas fa-file-invoice text-3xl text-zinc-400 dark:text-zinc-500"></i>
                            </div>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-zinc-900 dark:text-white">Aucune transaction</h3>
                        <p class="mx-auto mb-8 max-w-md text-zinc-500 dark:text-zinc-400">Vos transactions apparaîtront ici une fois que vous aurez effectué des opérations.</p>

                        <!-- Sources d'argent possibles -->
                        <div class="mb-8">
                            <h4 class="mb-6 text-lg font-medium text-zinc-700 dark:text-zinc-200">💡 Comment recevoir de l'argent dans votre portefeuille :</h4>
                            <div class="mx-auto grid max-w-4xl grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                <div class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 dark:border-emerald-500/30 dark:from-emerald-500/10 dark:to-emerald-500/5">
                                    <div class="text-center">
                                        <div class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-200/60 dark:bg-emerald-500/20">
                                            <i class="fas fa-bag-shopping text-emerald-600 dark:text-emerald-400"></i>
                                        </div>
                                        <h5 class="font-semibold text-emerald-800 dark:text-emerald-400">Vendre des articles</h5>
                                        <p class="text-sm text-emerald-700 dark:text-emerald-500">Recevez l'argent de vos ventes</p>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-vinted-primary-200 bg-gradient-to-br from-vinted-primary-50 to-vinted-primary-100 p-4 dark:border-vinted-primary-500/30 dark:from-vinted-primary-500/10 dark:to-vinted-primary-500/5">
                                    <div class="text-center">
                                        <div class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-vinted-primary-200/60 dark:bg-vinted-primary-500/20">
                                            <i class="fas fa-star text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                                        </div>
                                        <h5 class="font-semibold text-vinted-primary-800 dark:text-vinted-primary-400">Commissions</h5>
                                        <p class="text-sm text-vinted-primary-700 dark:text-vinted-primary-500">Gagnez des commissions sur les ventes</p>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-vinted-primary-200 bg-gradient-to-br from-vinted-primary-50 to-vinted-primary-100 p-4 dark:border-vinted-primary-500/30 dark:from-vinted-primary-500/10 dark:to-vinted-primary-500/5">
                                    <div class="text-center">
                                        <div class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-vinted-primary-200/60 dark:bg-vinted-primary-500/20">
                                            <i class="fas fa-user-group text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                                        </div>
                                        <h5 class="font-semibold text-vinted-primary-800 dark:text-vinted-primary-400">Parrainage</h5>
                                        <p class="text-sm text-vinted-primary-700 dark:text-vinted-primary-500">Bonus de parrainage d'amis</p>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 dark:border-yellow-500/30 dark:from-yellow-500/10 dark:to-yellow-500/5">
                                    <div class="text-center">
                                        <div class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-200/60 dark:bg-yellow-500/20">
                                            <i class="fas fa-gift text-yellow-600 dark:text-yellow-400"></i>
                                        </div>
                                        <h5 class="font-semibold text-yellow-800 dark:text-yellow-400">Bonus</h5>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-500">Récompenses et bonus spéciaux</p>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 dark:border-emerald-500/30 dark:from-emerald-500/10 dark:to-emerald-500/5">
                                    <div class="text-center">
                                        <div class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-200/60 dark:bg-emerald-500/20">
                                            <i class="fas fa-mobile-screen text-emerald-600 dark:text-emerald-400"></i>
                                        </div>
                                        <h5 class="font-semibold text-emerald-800 dark:text-emerald-400">Recharge Mobile</h5>
                                        <p class="text-sm text-emerald-700 dark:text-emerald-500">Ajout de fonds via Mobile Money</p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="flex flex-col justify-center gap-3 sm:flex-row">
                            <a href="{{ route('wallet.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 font-medium text-white transition-all duration-200 hover:bg-emerald-700 active:scale-[0.98]">
                                <i class="fas fa-plus"></i>
                                Ajouter des fonds USD
                            </a>
                            <a href="{{ route('wallet.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-6 py-3 font-medium text-white transition-all duration-200 hover:bg-amber-600 active:scale-[0.98]">
                                <i class="fas fa-plus"></i>
                                Ajouter des fonds CDF
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Taux de change global
let currentRate = 2650;
let rateLastUpdate = null;
let isFallbackRate = false;

// Récupérer le taux de change actuel depuis l'API
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
            
            console.log(`Taux récupéré: ${currentRate} (${isFallbackRate ? 'secours' : 'réel'})`);
        }
    } catch (error) {
        console.error('Erreur lors de la récupération du taux:', error);
        showRateError();
    }
}

// Rafraîchir le taux de change manuellement
async function refreshExchangeRate() {
    const refreshBtn = document.getElementById('refreshRateBtn');
    const refreshIcon = document.getElementById('refreshIcon');
    
    // Animation de rotation
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

// Mettre à jour les indicateurs visuels du taux
function updateRateIndicators() {
    const rateSource = document.getElementById('rateSource');
    const rateFallback = document.getElementById('rateFallback');
    const lastUpdate = document.getElementById('lastUpdate');
    const rateAlert = document.getElementById('rateAlert');
    const rateFwd = document.getElementById('rateFwd');
    const rateBwd = document.getElementById('rateBwd');
    
    // Afficher le type de taux
    if (isFallbackRate) {
        rateSource.classList.add('hidden');
        rateFallback.classList.remove('hidden');
        rateAlert.classList.remove('bg-blue-50', 'border-blue-200');
        rateAlert.classList.add('bg-yellow-50', 'border-yellow-200');
    } else {
        rateSource.classList.remove('hidden');
        rateFallback.classList.add('hidden');
        rateAlert.classList.remove('bg-yellow-50', 'border-yellow-200');
        rateAlert.classList.add('bg-blue-50', 'border-blue-200');
    }
    
    // Mettre à jour le temps écoulé
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
    
    // Mettre à jour les flèches de conversion
    if (rateFwd) rateFwd.textContent = currentRate.toLocaleString('fr-FR');
    if (rateBwd) rateBwd.textContent = currentRate.toLocaleString('fr-FR');
}

// Afficher une erreur de chargement du taux
function showRateError() {
    const rateAlert = document.getElementById('rateAlert');
    rateAlert.classList.remove('bg-blue-50', 'border-blue-200');
    rateAlert.classList.add('bg-red-50', 'border-red-200');
    document.getElementById('exchangeRate').textContent = 'Erreur de chargement';
}

// Mettre à jour les exemples de conversion
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

// Mettre à jour l'affichage du taux
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

// Afficher une notification
function showNotification(type, message) {
    const alertClass = type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
    const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-triangle-exclamation';
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full ${alertClass} border rounded-xl p-4 shadow-lg transform transition-all duration-300`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="${iconClass} flex-shrink-0"></i>
            <span class="text-sm">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto p-1 hover:opacity-70 transition-opacity">
                <i class="fas fa-xmark"></i>
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

// Calculer la conversion
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

// Échanger les wallets source et destination
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

// Conversion rapide
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

// Mettre à jour les labels de devise
function updateCurrencyLabels() {
    const fromCurrency = document.getElementById('fromWallet').selectedOptions[0].dataset.currency;
    const toCurrency = document.getElementById('toWallet').selectedOptions[0].dataset.currency;
    
    document.getElementById('fromCurrency').textContent = fromCurrency;
    document.getElementById('toCurrency').textContent = toCurrency;
    
    updateRateDisplay();
}

// Mettre à jour les labels de solde
function updateBalanceLabels() {
    const fromWallet = document.getElementById('fromWallet');
    const toWallet = document.getElementById('toWallet');
    
    const fromBalance = fromWallet.selectedOptions[0].text.split(' - ')[1];
    const toBalance = toWallet.selectedOptions[0].text.split(' - ')[1];
    
    document.getElementById('fromBalance').textContent = fromBalance;
    document.getElementById('toBalance').textContent = toBalance;
}

// Soumettre le formulaire de conversion
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
    convertBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Conversion en cours...';
    
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

// Afficher un message
function showMessage(type, message) {
    const messageDiv = document.getElementById('conversionMessage');
    const alertClass = type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
    const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-triangle-exclamation';
    
    messageDiv.innerHTML = `
        <div class="border rounded-xl p-4 ${alertClass}">
            <div class="flex items-center gap-2">
                <i class="${iconClass}"></i>
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
    
    const spinner = '<i class="fas fa-spinner fa-spin text-3xl mx-auto"></i>';
    
    usdBalance.innerHTML = spinner;
    cdfBalance.innerHTML = spinner;
    if (refreshIcon) refreshIcon.classList.add('animate-spin');
    
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Fonctions de filtrage des transactions
function filterTransactions(type) {
    const desktopRows = document.querySelectorAll('tbody tr');
    const mobileCards = document.querySelectorAll('.transaction-card');
    const buttons = document.querySelectorAll('[id^="filter"]');
    
    // Reset all buttons
    buttons.forEach(btn => {
        btn.classList.remove('bg-gray-800', 'text-white');
        btn.classList.add('bg-gray-50 dark:bg-gray-900', 'text-gray-700 dark:text-gray-200');
    });
    
    // Set active button
    const activeButton = document.getElementById('filter' + type.charAt(0).toUpperCase() + type.slice(1));
    if (activeButton) {
        activeButton.classList.remove('bg-gray-50', 'dark:bg-gray-900', 'text-gray-700', 'dark:text-gray-200');
        activeButton.classList.add('bg-gray-800', 'text-white');
    }
    
    let visibleCount = 0;
    
    // Filtrer les lignes desktop
    desktopRows.forEach(row => {
        const shouldShow = shouldShowTransaction(row, type);
        if (shouldShow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Filtrer les cartes mobile
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
        const currencyBadges = element.querySelectorAll('[class*="bg-blue-100"], [class*="bg-yellow-100"]');
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

// Initialisation
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
