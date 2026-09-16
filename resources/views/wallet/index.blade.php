@extends('app')

@section('title', 'Mon Portefeuille')

@section('content')
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 py-8">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

        <!-- En-tête -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">Portefeuille</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Solde total et historique des opérations</p>
            </div>
            <button id="refreshMainBtn" onclick="refreshBalances()" class="inline-flex h-9 items-center gap-2 self-start rounded-md border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 sm:self-auto">
                <i class="fas fa-rotate-right text-xs" id="refreshMainIcon"></i>
                Actualiser
            </button>
        </div>

        <!-- Soldes -->
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Wallet USD -->
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="p-6">
                    <div class="mb-5 flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <i class="fas fa-dollar-sign text-base"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Dollar Américain</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Solde disponible</p>
                            </div>
                        </div>
                        <span class="inline-flex h-6 items-center rounded-md bg-zinc-100 px-2 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">USD</span>
                    </div>
                    <p class="text-3xl font-semibold tracking-tight tabular-nums text-zinc-900 dark:text-white" id="usd-balance">
                        ${{ number_format($usdWallet->balance, 2, '.', ',') }}
                    </p>
                </div>
                <div class="flex gap-2 border-t border-zinc-100 p-4 dark:border-zinc-800">
                    <a href="{{ route('wallet.index') }}" class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-md bg-zinc-900 px-4 text-sm font-medium text-zinc-50 transition-colors hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                        <i class="fas fa-plus text-xs"></i>
                        Ajouter
                    </a>
                    <a href="{{ route('wallet.withdraw-funds', $usdWallet) }}" class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-md border border-zinc-200 bg-white px-4 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        <i class="fas fa-minus text-xs text-red-500"></i>
                        Retirer
                    </a>
                </div>
            </div>

            <!-- Wallet CDF -->
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="p-6">
                    <div class="mb-5 flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                <i class="fas fa-coins text-base"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Franc Congolais</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Solde disponible</p>
                            </div>
                        </div>
                        <span class="inline-flex h-6 items-center rounded-md bg-zinc-100 px-2 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">CDF</span>
                    </div>
                    <p class="text-3xl font-semibold tracking-tight tabular-nums text-zinc-900 dark:text-white" id="cdf-balance">
                        {{ number_format($cdfWallet->balance, 2, ',', ' ') }} FC
                    </p>
                </div>
                <div class="flex gap-2 border-t border-zinc-100 p-4 dark:border-zinc-800">
                    <a href="{{ route('wallet.index') }}" class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-md bg-zinc-900 px-4 text-sm font-medium text-zinc-50 transition-colors hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                        <i class="fas fa-plus text-xs"></i>
                        Ajouter
                    </a>
                    <a href="{{ route('wallet.withdraw-funds', $cdfWallet) }}" class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-md border border-zinc-200 bg-white px-4 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        <i class="fas fa-minus text-xs text-red-500"></i>
                        Retirer
                    </a>
                </div>
            </div>
        </div>

        <!-- Conversion -->
        <div class="mb-8 rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-100 p-6 dark:border-zinc-800">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="flex items-center gap-2.5 text-base font-semibold tracking-tight text-zinc-900 dark:text-white">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            <i class="fas fa-arrow-right-arrow-left text-sm"></i>
                        </div>
                        Conversion
                    </h2>
                    <div class="flex gap-1 rounded-md bg-zinc-100 p-1 dark:bg-zinc-800">
                        <button type="button" class="rounded-sm px-2.5 py-1 text-xs font-medium text-zinc-600 transition-colors hover:bg-white hover:text-zinc-900 hover:shadow-sm dark:text-zinc-300 dark:hover:bg-zinc-700 dark:hover:text-white" onclick="quickConvert('USD', 'CDF')">USD → CDF</button>
                        <button type="button" class="rounded-sm px-2.5 py-1 text-xs font-medium text-zinc-600 transition-colors hover:bg-white hover:text-zinc-900 hover:shadow-sm dark:text-zinc-300 dark:hover:bg-zinc-700 dark:hover:text-white" onclick="quickConvert('CDF', 'USD')">CDF → USD</button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form id="conversionForm">
                    @csrf
                    <div class="flex flex-col gap-4 md:flex-row md:items-end">
                        <!-- De -->
                        <div class="flex-1 space-y-1.5">
                            <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400" for="fromWallet">De</label>
                            <div class="flex items-center gap-2">
                                <select class="h-9 w-full rounded-md border border-zinc-200 bg-white px-3 text-sm text-zinc-900 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-500 dark:focus:ring-zinc-700" id="fromWallet" name="from_wallet_id" required>
                                    <option value="{{ $usdWallet->id }}" data-currency="USD">USD - ${{ number_format($usdWallet->balance, 2) }}</option>
                                    <option value="{{ $cdfWallet->id }}" data-currency="CDF">CDF - {{ number_format($cdfWallet->balance, 2) }} FC</option>
                                </select>
                            </div>
                            <div class="flex">
                                <input type="number" class="h-9 w-full rounded-l-md border border-zinc-200 bg-white px-3 text-sm text-zinc-900 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-500 dark:focus:ring-zinc-700" id="fromAmount" name="amount" placeholder="0.00" step="0.01" min="0.01" required>
                                <span class="inline-flex h-9 items-center rounded-r-md border border-l-0 border-zinc-200 bg-zinc-50 px-3 text-sm font-medium text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300" id="fromCurrency">USD</span>
                            </div>
                            <p class="text-xs text-zinc-400" id="fromBalance">Solde : ${{ number_format($usdWallet->balance, 2) }}</p>
                        </div>

                        <!-- Swap -->
                        <div class="flex justify-center pb-7">
                            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white" id="swapBtn">
                                <i class="fas fa-arrows-rotate text-sm"></i>
                            </button>
                        </div>

                        <!-- Vers -->
                        <div class="flex-1 space-y-1.5">
                            <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400" for="toWallet">Vers</label>
                            <div class="flex items-center gap-2">
                                <select class="h-9 w-full rounded-md border border-zinc-200 bg-white px-3 text-sm text-zinc-900 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-zinc-500 dark:focus:ring-zinc-700" id="toWallet" name="to_wallet_id" required>
                                    <option value="{{ $cdfWallet->id }}" data-currency="CDF">CDF - {{ number_format($cdfWallet->balance, 2) }} FC</option>
                                    <option value="{{ $usdWallet->id }}" data-currency="USD">USD - ${{ number_format($usdWallet->balance, 2) }}</option>
                                </select>
                            </div>
                            <div class="flex">
                                <input type="text" class="h-9 w-full rounded-l-md border border-zinc-200 bg-zinc-50 px-3 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300" id="toAmount" placeholder="0.00" readonly>
                                <span class="inline-flex h-9 items-center rounded-r-md border border-l-0 border-zinc-200 bg-zinc-50 px-3 text-sm font-medium text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300" id="toCurrency">CDF</span>
                            </div>
                            <p class="text-xs text-zinc-400" id="toBalance">Solde : {{ number_format($cdfWallet->balance, 2) }} FC</p>
                        </div>
                    </div>

                    <!-- Taux -->
                    <div class="mt-6 rounded-md border border-zinc-200 bg-zinc-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-800/40" id="rateAlert">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-circle-info text-sm text-zinc-400"></i>
                                <span class="text-sm text-zinc-600 dark:text-zinc-300">Taux : <strong class="font-semibold text-zinc-900 dark:text-white" id="exchangeRate">Chargement...</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="rateSource" class="hidden rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Temps réel</span>
                                <span id="rateFallback" class="hidden rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Taux de secours</span>
                                <span class="hidden text-xs text-zinc-500 dark:text-zinc-400 md:inline" id="rateFwd"></span>
                                <button type="button" class="inline-flex h-7 items-center gap-1.5 rounded-md px-2 text-xs font-medium text-zinc-600 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white" id="refreshRateBtn" onclick="refreshExchangeRate()">
                                    <i class="fas fa-rotate-right text-[10px]" id="refreshIcon"></i>
                                    Actualiser
                                </button>
                                <span class="hidden text-xs text-zinc-400 lg:inline">· <span id="lastUpdate">À l'instant</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-md bg-zinc-900 px-4 text-sm font-medium text-zinc-50 transition-colors hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200" id="convertBtn">
                            <i class="fas fa-arrow-right-arrow-left text-xs"></i>
                            Convertir
                        </button>
                        <div id="conversionMessage" class="flex-1 hidden"></div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                        <span>$1 = <span id="example1" class="font-medium text-zinc-700 dark:text-zinc-200">-</span></span>
                        <span>$10 = <span id="example2" class="font-medium text-zinc-700 dark:text-zinc-200">-</span></span>
                        <span>10 000 FC = <span id="example3" class="font-medium text-zinc-700 dark:text-zinc-200">-</span></span>
                        <span>50 000 FC = <span id="example4" class="font-medium text-zinc-700 dark:text-zinc-200">-</span></span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 border-b border-zinc-100 px-6 py-4 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="flex items-center gap-2.5 text-base font-semibold tracking-tight text-zinc-900 dark:text-white">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        <i class="fas fa-clock-rotate-left text-sm"></i>
                    </div>
                    Transactions
                </h2>
                <a href="{{ route('wallet.transactions', $cdfWallet) }}" class="inline-flex h-9 items-center gap-1.5 rounded-md border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Voir tout
                    <span class="rounded-full bg-zinc-100 px-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $recentTransactions->total() }}</span>
                </a>
            </div>

            @if($recentTransactions->count() > 0)
                <!-- Filtres -->
                <div class="flex flex-wrap gap-1.5 border-b border-zinc-100 px-6 py-3 dark:border-zinc-800">
                    <button class="rounded-md bg-zinc-900 px-2.5 py-1 text-xs font-medium text-zinc-50 dark:bg-zinc-100 dark:text-zinc-900" id="filterAll" onclick="filterTransactions('all')">Toutes</button>
                    <button class="rounded-md bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 transition-colors hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700" id="filterCredit" onclick="filterTransactions('credit')">Crédits</button>
                    <button class="rounded-md bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 transition-colors hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700" id="filterDebit" onclick="filterTransactions('debit')">Débits</button>
                    <button class="rounded-md bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 transition-colors hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700" id="filterUSD" onclick="filterTransactions('USD')">USD</button>
                    <button class="rounded-md bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 transition-colors hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700" id="filterCDF" onclick="filterTransactions('CDF')">CDF</button>
                </div>

                <!-- Desktop -->
                <div class="hidden overflow-x-auto sm:block">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($recentTransactions as $transaction)
                                @php
                                    $isCredit = $transaction->type === 'credit';
                                    $txTypeLabel = $isCredit ? 'Crédit' : 'Débit';
                                @endphp
                                <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50" data-type="{{ $transaction->type }}" data-currency="{{ $transaction->wallet->currency }}">
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium {{ $isCredit ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400' }}">
                                            <i class="{{ $isCredit ? 'fas fa-plus' : 'fas fa-minus' }} text-[10px]"></i>{{ $txTypeLabel }}
                                        </span>
                                    </td>
                                    <td class="max-w-[280px] truncate px-6 py-3 text-sm text-zinc-700 dark:text-zinc-300">{{ $transaction->description }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="font-semibold tabular-nums {{ $isCredit ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ $isCredit ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}</span>
                                        <span class="ml-1 text-xs text-zinc-400">{{ $transaction->wallet->currency }}</span>
                                    </td>
                                    <td class="hidden px-6 py-3 text-right text-xs text-zinc-400 md:table-cell">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile -->
                <div class="block sm:hidden">
                    <div class="space-y-2 p-3">
                        @foreach($recentTransactions as $transaction)
                            @php
                                $isCredit = $transaction->type === 'credit';
                                $d = strtolower($transaction->description);
                                $iconClass = 'fas fa-layer-group';
                                $badgeClass = 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300';
                                $src = 'Transaction';
                                if (str_contains($d, 'commission')) { $iconClass='fas fa-star'; $badgeClass='bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'; $src='Commission Vente'; }
                                elseif (str_contains($d, 'vente')) { $iconClass='fas fa-bag-shopping'; $badgeClass='bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400'; $src='Vente Produit'; }
                                elseif (str_contains($d, 'parrainage')) { $iconClass='fas fa-user-group'; $badgeClass='bg-vinted-primary-100 text-vinted-primary-700 dark:bg-vinted-primary-500/10 dark:text-vinted-primary-400'; $src='Parrainage'; }
                                elseif (str_contains($d, 'conversion')) { $iconClass='fas fa-arrow-right-arrow-left'; $badgeClass='bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400'; $src='Conversion'; }
                                elseif (str_contains($d, 'recharge')) { $iconClass='fas fa-mobile-screen'; $badgeClass='bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400'; $src='Recharge'; }
                            @endphp
                            <div class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900 transaction-card" data-type="{{ $transaction->type }}" data-currency="{{ $transaction->wallet->currency }}">
                                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg {{ $isCredit ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' }}">
                                    <i class="{{ $iconClass }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $src }}</div>
                                    <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->description }}</div>
                                    <span class="mt-0.5 inline-block rounded-md px-1.5 py-0.5 text-[10px] font-medium {{ $badgeClass }}">{{ $transaction->wallet->currency }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold tabular-nums {{ $isCredit ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $isCredit ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                    </div>
                                    <div class="text-[10px] text-zinc-400">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if($recentTransactions->hasPages())
                    <div class="flex items-center justify-between border-t border-zinc-100 bg-zinc-50 px-6 py-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                        <small class="text-xs text-zinc-500 dark:text-zinc-400">Page {{ $recentTransactions->currentPage() }}/{{ $recentTransactions->lastPage() }}</small>
                        <div class="flex gap-2">{{ $recentTransactions->links() }}</div>
                    </div>
                @endif
            @else
                <div class="px-6 py-14 text-center">
                    <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <i class="fas fa-file-invoice text-xl text-zinc-400 dark:text-zinc-500"></i>
                    </div>
                    <h3 class="mb-1 text-base font-semibold text-zinc-900 dark:text-white">Aucune transaction</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Vos opérations apparaîtront ici.</p>
                </div>
            @endif
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
        }
    } catch (error) {
        showRateError();
    }
}

// Rafraîchir le taux de change manuellement
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

    if (isFallbackRate) {
        rateSource.classList.add('hidden');
        rateFallback.classList.remove('hidden');
        rateAlert.classList.remove('border-zinc-200', 'bg-zinc-50/60');
        rateAlert.classList.add('border-amber-200', 'bg-amber-50/60');
    } else {
        rateSource.classList.remove('hidden');
        rateFallback.classList.add('hidden');
        rateAlert.classList.remove('border-amber-200', 'bg-amber-50/60');
        rateAlert.classList.add('border-zinc-200', 'bg-zinc-50/60');
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

    const rateFwd = document.getElementById('rateFwd');
    const rateBwd = document.getElementById('rateBwd');
    if (rateFwd) rateFwd.textContent = `USD→CDF ×${currentRate.toLocaleString('fr-FR')}`;
    if (rateBwd) rateBwd.textContent = `CDF→USD ÷${currentRate.toLocaleString('fr-FR')}`;
}

// Afficher une erreur de chargement du taux
function showRateError() {
    const rateAlert = document.getElementById('rateAlert');
    rateAlert.classList.remove('border-zinc-200', 'bg-zinc-50/60');
    rateAlert.classList.add('border-red-200', 'bg-red-50/60');
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

    const exchangeRate = document.getElementById('exchangeRate');
    if (exchangeRate) exchangeRate.textContent = rateText;
}

// Afficher une notification
function showNotification(type, message) {
    const alertClass = type === 'success'
        ? 'bg-zinc-900 text-zinc-50 border-zinc-900'
        : 'bg-red-50 text-red-800 border-red-200';
    const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-triangle-exclamation';

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full ${alertClass} border rounded-lg p-4 shadow-lg transform transition-all duration-300`;
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

    const fromBalance = 'Solde : ' + fromWallet.selectedOptions[0].text.split(' - ')[1];
    const toBalance = 'Solde : ' + toWallet.selectedOptions[0].text.split(' - ')[1];

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
            showMessage('success', `<strong>Conversion réussie !</strong> ${data.amount} ${data.from_currency} → ${data.converted_amount} ${data.to_currency} <small>(taux: ${data.rate.toLocaleString('fr-FR')})</small>`);

            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showMessage('error', data.message || 'Erreur lors de la conversion');
            convertBtn.disabled = false;
            convertBtn.innerHTML = originalBtnText;
        }
    } catch (error) {
        showMessage('error', 'Erreur de connexion au serveur');
        convertBtn.disabled = false;
        convertBtn.innerHTML = originalBtnText;
    }
}

// Afficher un message
function showMessage(type, message) {
    const messageDiv = document.getElementById('conversionMessage');
    if (!messageDiv) return;
    const alertClass = type === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200';
    const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-triangle-exclamation';

    messageDiv.innerHTML = `
        <div class="rounded-lg border p-2.5 text-sm ${alertClass}">
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
    const refreshIcon = document.getElementById('refreshMainIcon');

    const spinner = '<i class="fas fa-spinner fa-spin text-lg mx-auto"></i>';

    usdBalance.innerHTML = spinner;
    cdfBalance.innerHTML = spinner;
    if (refreshIcon) refreshIcon.classList.add('animate-spin');

    setTimeout(() => {
        location.reload();
    }, 500);
}

// Filtres des transactions (data attributes)
function filterTransactions(type) {
    const items = document.querySelectorAll('[data-type]');
    const buttons = document.querySelectorAll('[id^="filter"]');

    buttons.forEach(btn => {
        btn.classList.remove('bg-zinc-900', 'text-zinc-50', 'dark:bg-zinc-100', 'dark:text-zinc-900');
        btn.classList.add('bg-zinc-100', 'text-zinc-600', 'hover:bg-zinc-200', 'dark:bg-zinc-800', 'dark:text-zinc-300', 'dark:hover:bg-zinc-700');
    });

    const activeId = 'filter' + type.charAt(0).toUpperCase() + type.slice(1);
    const activeButton = document.getElementById(activeId);
    if (activeButton) {
        activeButton.classList.remove('bg-zinc-100', 'text-zinc-600', 'hover:bg-zinc-200', 'dark:bg-zinc-800', 'dark:text-zinc-300', 'dark:hover:bg-zinc-700');
        activeButton.classList.add('bg-zinc-900', 'text-zinc-50', 'dark:bg-zinc-100', 'dark:text-zinc-900');
    }

    items.forEach(item => {
        let show = type === 'all' ||
            (type === 'credit' && item.dataset.type === 'credit') ||
            (type === 'debit' && item.dataset.type === 'debit') ||
            item.dataset.currency === type;
        item.style.display = show ? '' : 'none';
    });
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