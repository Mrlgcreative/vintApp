@extends('app')

@section('title', 'Mon Portefeuille')

@section('content')
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- En-tête simple -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-vinted-primary-600 text-white shadow-sm shadow-vinted-primary/30">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Mon Portefeuille</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Solde total et opérations</p>
                </div>
            </div>
            <button class="inline-flex items-center gap-2 self-start rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 sm:self-auto" onclick="refreshBalances()" id="refreshMainBtn">
                <i class="fas fa-rotate-right text-xs" id="refreshMainIcon"></i>
                Actualiser
            </button>
        </div>

        <!-- Cartes des soldes -->
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Wallet USD -->
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <i class="fas fa-dollar-sign text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Dollar Américain</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">USD</p>
                    </div>
                </div>
                <p class="mb-5 text-3xl font-bold tabular-nums text-emerald-600 dark:text-emerald-400" id="usd-balance">
                    ${{ number_format($usdWallet->balance, 2, '.', ',') }}
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('wallet.index') }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-emerald-700">
                        <i class="fas fa-plus"></i>
                        Ajouter
                    </a>
                    <a href="{{ route('wallet.withdraw-funds', $usdWallet) }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-zinc-200 px-4 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        <i class="fas fa-minus text-red-500"></i>
                        Retirer
                    </a>
                </div>
            </div>

            <!-- Wallet CDF -->
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                        <i class="fas fa-coins text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Franc Congolais</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">CDF</p>
                    </div>
                </div>
                <p class="mb-5 text-3xl font-bold tabular-nums text-amber-600 dark:text-amber-400" id="cdf-balance">
                    {{ number_format($cdfWallet->balance, 2, ',', ' ') }} FC
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('wallet.index') }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-amber-600">
                        <i class="fas fa-plus"></i>
                        Ajouter
                    </a>
                    <a href="{{ route('wallet.withdraw-funds', $cdfWallet) }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-zinc-200 px-4 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        <i class="fas fa-minus text-red-500"></i>
                        Retirer
                    </a>
                </div>
            </div>
        </div>

        <!-- Conversion de devises -->
        <div class="mb-8 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="flex items-center gap-2.5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-vinted-primary-50 text-vinted-primary-500 dark:bg-vinted-primary-500/10">
                        <i class="fas fa-arrow-right-arrow-left text-sm"></i>
                    </div>
                    Convertir (USD ⇄ CDF)
                </h2>
                <div class="flex gap-2">
                    <button type="button" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-vinted-primary-600 transition-colors hover:bg-vinted-primary-50 dark:text-vinted-primary-300 dark:hover:bg-vinted-primary-500/10" onclick="quickConvert('USD', 'CDF')">USD → CDF</button>
                    <button type="button" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-amber-600 transition-colors hover:bg-amber-50 dark:text-amber-300 dark:hover:bg-amber-500/10" onclick="quickConvert('CDF', 'USD')">CDF → USD</button>
                </div>
            </div>

            <form id="conversionForm">
                @csrf
                <div class="flex flex-col items-stretch gap-4 md:flex-row md:items-end">
                    <!-- De -->
                    <div class="flex-1">
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">De</label>
                        <select class="mb-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 text-sm focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800" id="fromWallet" name="from_wallet_id" required>
                            <option value="{{ $usdWallet->id }}" data-currency="USD">USD - ${{ number_format($usdWallet->balance, 2) }}</option>
                            <option value="{{ $cdfWallet->id }}" data-currency="CDF">CDF - {{ number_format($cdfWallet->balance, 2) }} FC</option>
                        </select>
                        <div class="flex">
                            <input type="number" class="flex-1 rounded-l-lg border border-zinc-300 bg-white px-3 py-2.5 text-sm focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800" id="fromAmount" name="amount" placeholder="0.00" step="0.01" min="0.01" required>
                            <span class="inline-flex items-center rounded-r-lg border border-l-0 border-zinc-300 bg-zinc-50 px-3 font-medium text-zinc-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200" id="fromCurrency">USD</span>
                        </div>
                        <p class="mt-1 text-xs text-zinc-400" id="fromBalance">Solde : ${{ number_format($usdWallet->balance, 2) }}</p>
                    </div>

                    <!-- Swap -->
                    <div class="flex justify-center md:pb-1">
                        <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-vinted-primary-600 text-white shadow-sm transition-colors hover:bg-vinted-primary-700" id="swapBtn">
                            <i class="fas fa-arrows-rotate"></i>
                        </button>
                    </div>

                    <!-- Vers -->
                    <div class="flex-1">
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Vers</label>
                        <select class="mb-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 text-sm focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500 dark:border-zinc-600 dark:bg-zinc-800" id="toWallet" name="to_wallet_id" required>
                            <option value="{{ $cdfWallet->id }}" data-currency="CDF">CDF - {{ number_format($cdfWallet->balance, 2) }} FC</option>
                            <option value="{{ $usdWallet->id }}" data-currency="USD">USD - ${{ number_format($usdWallet->balance, 2) }}</option>
                        </select>
                        <div class="flex">
                            <input type="text" class="flex-1 rounded-l-lg border border-zinc-300 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-600 dark:bg-zinc-900" id="toAmount" placeholder="0.00" readonly>
                            <span class="inline-flex items-center rounded-r-lg border border-l-0 border-zinc-300 bg-zinc-50 px-3 font-medium text-zinc-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200" id="toCurrency">CDF</span>
                        </div>
                        <p class="mt-1 text-xs text-zinc-400" id="toBalance">Solde : {{ number_format($cdfWallet->balance, 2) }} FC</p>
                    </div>
                </div>

                <!-- Taux de change -->
                <div class="mt-4 rounded-lg border border-vinted-primary-200 bg-vinted-primary-50 px-4 py-3 dark:border-vinted-primary-500/30 dark:bg-vinted-primary-500/5" id="rateAlert">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                            <i class="fas fa-circle-info text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                            Taux : <strong id="exchangeRate">Chargement...</strong>
                        </span>
                        <div class="flex items-center gap-2">
                            <span id="rateSource" class="hidden rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400">Temps réel</span>
                            <span id="rateFallback" class="hidden rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-500/10 dark:text-amber-400">Taux de secours</span>
                            <span class="hidden text-xs text-zinc-500 md:inline" id="rateFwd"></span>
                            <button type="button" class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs text-vinted-primary-600 transition-colors hover:bg-vinted-primary-100 dark:text-vinted-primary-300 dark:hover:bg-vinted-primary-500/10" id="refreshRateBtn" onclick="refreshExchangeRate()">
                                <i class="fas fa-rotate-right text-[10px]" id="refreshIcon"></i>
                                Actualiser
                            </button>
                            <span class="hidden text-xs text-zinc-400 lg:inline">· <span id="lastUpdate">À l'instant</span></span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-vinted-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-vinted-primary-700" id="convertBtn">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                        Convertir maintenant
                    </button>
                    <div id="conversionMessage" class="flex-1 hidden"></div>
                </div>

                <!-- Exemples de conversion (subtils) -->
                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                    <span>$1 = <span id="example1" class="font-medium text-zinc-700 dark:text-zinc-200">-</span></span>
                    <span>$10 = <span id="example2" class="font-medium text-zinc-700 dark:text-zinc-200">-</span></span>
                    <span>10 000 FC = <span id="example3" class="font-medium text-zinc-700 dark:text-zinc-200">-</span></span>
                    <span>50 000 FC = <span id="example4" class="font-medium text-zinc-700 dark:text-zinc-200">-</span></span>
                </div>
            </form>
        </div>

        <!-- Transactions récentes -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 border-b border-zinc-200 px-6 py-4 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="flex items-center gap-2.5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-vinted-primary-50 text-vinted-primary-500 dark:bg-vinted-primary-500/10">
                        <i class="fas fa-clock-rotate-left text-sm"></i>
                    </div>
                    Transactions récentes
                </h2>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('wallet.transactions', $cdfWallet) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-vinted-primary-600 transition-colors hover:bg-vinted-primary-50 dark:text-vinted-primary-300 dark:hover:bg-vinted-primary-500/10">
                        Voir tout ({{ $recentTransactions->total() }})
                    </a>
                </div>
            </div>

            <div class="p-0">
                @if($recentTransactions->count() > 0)
                    <!-- Filtres -->
                    <div class="flex flex-wrap gap-1.5 border-b border-zinc-100 px-6 py-3 dark:border-zinc-800">
                        <button class="rounded-md bg-zinc-800 px-2.5 py-1 text-xs font-medium text-white dark:bg-zinc-100 dark:text-zinc-800" id="filterAll" onclick="filterTransactions('all')">Toutes</button>
                        <button class="rounded-md bg-green-50 px-2.5 py-1 text-xs text-green-700 transition-colors hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400" id="filterCredit" onclick="filterTransactions('credit')">Crédits</button>
                        <button class="rounded-md bg-red-50 px-2.5 py-1 text-xs text-red-700 transition-colors hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400" id="filterDebit" onclick="filterTransactions('debit')">Débits</button>
                        <button class="rounded-md bg-blue-50 px-2.5 py-1 text-xs text-blue-700 transition-colors hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400" id="filterUSD" onclick="filterTransactions('USD')">USD</button>
                        <button class="rounded-md bg-amber-50 px-2.5 py-1 text-xs text-amber-700 transition-colors hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400" id="filterCDF" onclick="filterTransactions('CDF')">CDF</button>
                    </div>

                    <!-- Version Desktop/Tablet -->
                    <div class="hidden overflow-x-auto sm:block">
                        <table class="w-full">
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @foreach($recentTransactions as $transaction)
                                    @php
                                        $typeConfig = [
                                            'credit' => ['class' => 'bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400', 'icon' => 'fas fa-plus'],
                                            'debit' => ['class' => 'bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400', 'icon' => 'fas fa-minus'],
                                        ];
                                        $config = $typeConfig[$transaction->type] ?? ['class' => 'bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200', 'icon' => 'fas fa-circle'];
                                    @endphp
                                    <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $config['class'] }}">
                                                <i class="{{ $config['icon'] }} text-[10px]"></i>{{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-zinc-900 dark:text-white">{{ $transaction->description }}</td>
                                        <td class="px-6 py-3 text-right text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} <span class="text-xs font-normal text-zinc-400">{{ $transaction->wallet->currency }}</span>
                                        </td>
                                        <td class="hidden px-6 py-3 text-right text-xs text-zinc-400 md:table-cell">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Version Mobile - Cards -->
                    <div class="block sm:hidden">
                        <div class="space-y-2 p-3">
                            @foreach($recentTransactions as $transaction)
                                @php
                                    $iconClass = 'fas fa-layer-group';
                                    $badgeClass = 'bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200';
                                    $src = 'Transaction';
                                    $d = strtolower($transaction->description);
                                    if (str_contains($d, 'commission')) { $iconClass='fas fa-star'; $badgeClass='bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400'; $src='Commission Vente'; }
                                    elseif (str_contains($d, 'vente')) { $iconClass='fas fa-bag-shopping'; $badgeClass='bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400'; $src='Vente Produit'; }
                                    elseif (str_contains($d, 'parrainage')) { $iconClass='fas fa-user-group'; $badgeClass='bg-vinted-primary-100 dark:bg-vinted-primary-500/10 text-vinted-primary-800 dark:text-vinted-primary-400'; $src='Parrainage'; }
                                    elseif (str_contains($d, 'conversion')) { $iconClass='fas fa-arrow-right-arrow-left'; $badgeClass='bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400'; $src='Conversion'; }
                                    elseif (str_contains($d, 'recharge')) { $iconClass='fas fa-mobile-screen'; $badgeClass='bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400'; $src='Recharge'; }
                                @endphp
                                <div class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900 transaction-card">
                                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg {{ $transaction->type === 'credit' ? 'bg-green-100 dark:bg-green-500/10' : 'bg-red-100 dark:bg-red-500/10' }}">
                                        <i class="{{ $iconClass }} {{ $transaction->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $src }}</div>
                                        <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $transaction->description }}</div>
                                        <span class="mt-0.5 inline-block rounded-full px-1.5 py-0.5 text-[10px] font-medium {{ $badgeClass }}">{{ $transaction->wallet->currency }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold {{ $transaction->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                        </div>
                                        <div class="text-[10px] text-zinc-400">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($recentTransactions->hasPages())
                        <div class="flex items-center justify-between border-t border-zinc-200 bg-zinc-50 px-6 py-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <small class="text-xs text-zinc-500 dark:text-zinc-400">Page {{ $recentTransactions->currentPage() }}/{{ $recentTransactions->lastPage() }}</small>
                            <div class="flex gap-2">{{ $recentTransactions->links() }}</div>
                        </div>
                    @endif
                @else
                    <div class="px-6 py-14 text-center">
                        <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                            <i class="fas fa-file-invoice text-2xl text-zinc-400 dark:text-zinc-500"></i>
                        </div>
                        <h3 class="mb-1 text-lg font-semibold text-zinc-900 dark:text-white">Aucune transaction</h3>
                        <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">Vos opérations apparaîtront ici.</p>
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
    const rateFwd = document.getElementById('rateFwd');
    const rateBwd = document.getElementById('rateBwd');
    if (rateFwd) rateFwd.textContent = `USD→CDF ×${currentRate.toLocaleString('fr-FR')}`;
    if (rateBwd) rateBwd.textContent = `CDF→USD ÷${currentRate.toLocaleString('fr-FR')}`;
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
        console.error('Erreur:', error);
        showMessage('error', 'Erreur de connexion au serveur');
        convertBtn.disabled = false;
        convertBtn.innerHTML = originalBtnText;
    }
}

// Afficher un message
function showMessage(type, message) {
    const messageDiv = document.getElementById('conversionMessage');
    if (!messageDiv) return;
    const alertClass = type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
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
    
    // Filtrer les lignes desktop
    desktopRows.forEach(row => {
        const shouldShow = shouldShowTransaction(row, type);
        if (shouldShow) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Filtrer les cartes mobile
    mobileCards.forEach(card => {
        const shouldShow = shouldShowTransaction(card, type);
        if (shouldShow) {
            card.style.display = '';
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
