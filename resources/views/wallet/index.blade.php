@extends('app')

@section('title', 'Mon Portefeuille')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête avec gradient vert -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold flex items-center gap-3">
                            <i class="fas fa-wallet text-emerald-200"></i>
                            Mon Portefeuille
                        </h1>
                        <p class="text-emerald-100 mt-2">Gérez vos finances en toute simplicité</p>
                    </div>
                    <button class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2 hover:bg-white/30 transition-all duration-200" onclick="refreshBalances()">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Cartes des soldes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Wallet USD -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-green-600 text-lg font-semibold flex items-center gap-2">
                                <i class="fas fa-dollar-sign"></i>
                                Dollar Américain
                            </h2>
                            <p class="text-gray-500 text-sm">USD</p>
                        </div>
                        <div class="relative">
                            <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors duration-200" 
                                    onclick="toggleDropdown('usd-dropdown')">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="usd-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                <a href="{{ route('wallet.add-funds', $usdWallet) }}" 
                                   class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 first:rounded-t-lg">
                                    <i class="fas fa-plus text-green-500"></i>
                                    Ajouter des fonds
                                </a>
                                <a href="{{ route('wallet.withdraw-funds', $usdWallet) }}" 
                                   class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-minus text-red-500"></i>
                                    Retirer des fonds
                                </a>
                                <hr class="border-gray-200">
                                <a href="{{ route('wallet.transactions', $usdWallet) }}" 
                                   class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 last:rounded-b-lg">
                                    <i class="fas fa-history text-blue-500"></i>
                                    Voir l'historique
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center py-6">
                        <p class="text-4xl font-bold text-green-600 mb-2" id="usd-balance">
                            ${{ number_format($usdWallet->balance, 2, '.', ',') }}
                        </p>
                        <p class="text-gray-500">Solde disponible</p>
                    </div>
                    
                    <div class="flex gap-3">
                        <a href="{{ route('wallet.add-funds', $usdWallet) }}" 
                           class="flex-1 bg-green-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-600 transition-colors duration-200 text-center">
                            <i class="fas fa-plus mr-2"></i>Ajouter
                        </a>
                        <a href="{{ route('wallet.withdraw-funds', $usdWallet) }}" 
                           class="flex-1 border border-red-300 text-red-600 py-3 px-4 rounded-lg font-medium hover:bg-red-50 transition-colors duration-200 text-center">
                            <i class="fas fa-minus mr-2"></i>Retirer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Wallet CDF -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-yellow-600 text-lg font-semibold flex items-center gap-2">
                                <i class="fas fa-coins"></i>
                                Franc Congolais
                            </h2>
                            <p class="text-gray-500 text-sm">CDF</p>
                        </div>
                        <div class="relative">
                            <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors duration-200" 
                                    onclick="toggleDropdown('cdf-dropdown')">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="cdf-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                <a href="{{ route('wallet.add-funds', $cdfWallet) }}" 
                                   class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 first:rounded-t-lg">
                                    <i class="fas fa-plus text-green-500"></i>
                                    Ajouter des fonds
                                </a>
                                <a href="{{ route('wallet.withdraw-funds', $cdfWallet) }}" 
                                   class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-minus text-red-500"></i>
                                    Retirer des fonds
                                </a>
                                <hr class="border-gray-200">
                                <a href="{{ route('wallet.transactions', $cdfWallet) }}" 
                                   class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 last:rounded-b-lg">
                                    <i class="fas fa-history text-blue-500"></i>
                                    Voir l'historique
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center py-6">
                        <p class="text-4xl font-bold text-yellow-600 mb-2" id="cdf-balance">
                            {{ number_format($cdfWallet->balance, 2, ',', ' ') }} FC
                        </p>
                        <p class="text-gray-500">Solde disponible</p>
                    </div>
                    
                    <div class="flex gap-3">
                        <a href="{{ route('wallet.add-funds', $cdfWallet) }}" 
                           class="flex-1 bg-yellow-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-yellow-600 transition-colors duration-200 text-center">
                            <i class="fas fa-plus mr-2"></i>Ajouter
                        </a>
                        <a href="{{ route('wallet.withdraw-funds', $cdfWallet) }}" 
                           class="flex-1 border border-red-300 text-red-600 py-3 px-4 rounded-lg font-medium hover:bg-red-50 transition-colors duration-200 text-center">
                            <i class="fas fa-minus mr-2"></i>Retirer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte de conversion de devises -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-primary-600 p-6 text-white">
                    <h2 class="text-xl font-bold flex items-center gap-2 mb-2">
                        <i class="fas fa-exchange-alt"></i>
                        Convertir entre devises (USD ⇄ CDF)
                    </h2>
                    <p class="text-blue-100">Convertissez facilement vos dollars en francs congolais et vice-versa</p>
                </div>
                <div class="p-6">
                    <!-- Options de conversion rapide -->
                    <div class="flex gap-3 mb-6">
                        <button type="button" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200" 
                                onclick="quickConvert('USD', 'CDF')">
                            <i class="fas fa-arrow-right"></i>
                            USD → CDF
                        </button>
                        <button type="button" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors duration-200" 
                                onclick="quickConvert('CDF', 'USD')">
                            <i class="fas fa-arrow-right"></i>
                            CDF → USD
                        </button>
                    </div>

                    <form id="conversionForm">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <!-- Source -->
                            <div class="lg:col-span-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">De</label>
                                <select class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3" 
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
                                           class="flex-1 p-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           id="fromAmount" 
                                           name="amount" 
                                           placeholder="0.00" 
                                           step="0.01" 
                                           min="0.01" 
                                           required>
                                    <span class="inline-flex items-center px-4 bg-gray-50 border border-l-0 border-gray-300 rounded-r-lg text-gray-700 font-medium" 
                                          id="fromCurrency">USD</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">
                                    Solde disponible: <span id="fromBalance">${{ number_format($usdWallet->balance, 2) }}</span>
                                </p>
                            </div>

                            <!-- Bouton d'échange -->
                            <div class="lg:col-span-2 flex items-center justify-center">
                                <button type="button" 
                                        class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-all duration-300 transform hover:scale-110" 
                                        id="swapBtn">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </div>

                            <!-- Destination -->
                            <div class="lg:col-span-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Vers</label>
                                <select class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3" 
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
                                           class="flex-1 p-3 border border-gray-300 rounded-l-lg bg-gray-50" 
                                           id="toAmount" 
                                           placeholder="0.00" 
                                           readonly>
                                    <span class="inline-flex items-center px-4 bg-gray-50 border border-l-0 border-gray-300 rounded-r-lg text-gray-700 font-medium" 
                                          id="toCurrency">CDF</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">
                                    Solde actuel: <span id="toBalance">{{ number_format($cdfWallet->balance, 2) }} FC</span>
                                </p>
                            </div>
                        </div>

                        <!-- Taux de change -->
                        <div class="mt-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4" id="rateAlert">
                                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <i class="fas fa-info-circle text-blue-600"></i>
                                            <span class="text-gray-700">Taux actuel: <strong id="exchangeRate">Chargement...</strong></span>
                                            <span id="rateSource" class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium hidden">
                                                Temps réel
                                            </span>
                                            <span id="rateFallback" class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium hidden">
                                                Taux de secours
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-4">
                                            <small class="text-gray-600 flex items-center gap-1">
                                                <i class="fas fa-arrow-right"></i>
                                                <strong>USD → CDF:</strong> × <span id="rateFwd">-</span>
                                            </small>
                                            <small class="text-gray-600 flex items-center gap-1">
                                                <i class="fas fa-arrow-left"></i>
                                                <strong>CDF → USD:</strong> ÷ <span id="rateBwd">-</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" 
                                                class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200" 
                                                id="refreshRateBtn" onclick="refreshExchangeRate()">
                                            <i class="fas fa-sync-alt" id="refreshIcon"></i>
                                            Actualiser
                                        </button>
                                        <small class="text-gray-500 hidden lg:block">
                                            Mis à jour: <span id="lastUpdate">À l'instant</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton de conversion -->
                        <div class="mt-6">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-primary-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-blue-700 hover:to-primary-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg" 
                                    id="convertBtn">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Convertir maintenant
                            </button>
                        </div>

                        <!-- Exemples de conversion -->
                        <div class="mt-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-3 flex items-center gap-2">
                                    <i class="fas fa-calculator"></i>
                                    <strong>Exemples de conversion :</strong>
                                </p>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-center">
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">$1 USD =</p>
                                        <p class="text-sm font-semibold text-gray-900" id="example1">2,650 FC</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">$10 USD =</p>
                                        <p class="text-sm font-semibold text-gray-900" id="example2">26,500 FC</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">10,000 FC =</p>
                                        <p class="text-sm font-semibold text-gray-900" id="example3">$3.77</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">50,000 FC =</p>
                                        <p class="text-sm font-semibold text-gray-900" id="example4">$18.87</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div id="conversionMessage" class="mt-6 hidden"></div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transactions récentes -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-history text-blue-500"></i>
                        Transactions récentes
                    </h2>
                    <div class="flex items-center gap-3">
                        <small class="text-gray-500">{{ $recentTransactions->total() }} transaction(s)</small>
                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs">
                            Page {{ $recentTransactions->currentPage() }}/{{ $recentTransactions->lastPage() }}
                        </span>
                    </div>
                </div>
                
                <!-- Filtres rapides -->
                <div class="flex flex-wrap gap-2">
                    <button class="inline-flex items-center gap-2 px-3 py-1 bg-gray-800 text-white rounded-lg text-sm font-medium" 
                            id="filterAll" onclick="filterTransactions('all')">
                        <i class="fas fa-list"></i>Toutes
                    </button>
                    <button class="inline-flex items-center gap-2 px-3 py-1 bg-green-50 text-green-700 rounded-lg text-sm hover:bg-green-100 transition-colors duration-200" 
                            id="filterCredit" onclick="filterTransactions('credit')">
                        <i class="fas fa-plus"></i>Crédits
                    </button>
                    <button class="inline-flex items-center gap-2 px-3 py-1 bg-red-50 text-red-700 rounded-lg text-sm hover:bg-red-100 transition-colors duration-200" 
                            id="filterDebit" onclick="filterTransactions('debit')">
                        <i class="fas fa-minus"></i>Débits
                    </button>
                    <button class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-sm hover:bg-blue-100 transition-colors duration-200" 
                            id="filterUSD" onclick="filterTransactions('USD')">
                        <i class="fas fa-dollar-sign"></i>USD
                    </button>
                    <button class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-50 text-yellow-700 rounded-lg text-sm hover:bg-yellow-100 transition-colors duration-200" 
                            id="filterCDF" onclick="filterTransactions('CDF')">
                        <i class="fas fa-coins"></i>CDF
                    </button>
                </div>
            </div>
            
            <div class="p-0">
                @if($recentTransactions->count() > 0)
                    <!-- Version Desktop/Tablet - Tableau -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/2">Description</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Devise</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider hidden xl:table-cell">Solde après</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->created_at->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $transaction->created_at->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $typeConfig = [
                                                    'credit' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-plus'],
                                                    'debit' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-minus'],
                                                ];
                                                $config = $typeConfig[$transaction->type] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-circle'];
                                            @endphp
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
                                                <i class="fas {{ $config['icon'] }}"></i>
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $transaction->description }}</div>
                                            @if($transaction->reference)
                                                <div class="text-xs text-gray-500 mt-1">Réf: {{ $transaction->reference }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                                {{ $transaction->wallet->currency }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center hidden lg:table-cell">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->wallet->currency === 'USD' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $transaction->wallet->currency }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right hidden xl:table-cell">
                                            <div class="text-sm text-gray-900">
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
                        <div class="p-4 space-y-4">
                            @foreach($recentTransactions as $transaction)
                                @php
                                    $source = '';
                                    $icon = 'fas fa-circle';
                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                    
                                    if (str_contains(strtolower($transaction->description), 'vente')) {
                                        if (str_contains($transaction->description, 'commission')) {
                                            $source = '💰 Commission Vente';
                                            $icon = 'fas fa-percentage';
                                            $badgeClass = 'bg-blue-100 text-blue-800';
                                        } else {
                                            $source = '🛍️ Vente Produit';
                                            $icon = 'fas fa-shopping-cart';
                                            $badgeClass = 'bg-green-100 text-green-800';
                                        }
                                    } elseif (str_contains(strtolower($transaction->description), 'parrainage')) {
                                        $source = '🤝 Argent Parrainage';
                                        $icon = 'fas fa-users';
                                        $badgeClass = 'bg-primary-100 text-primary-800';
                                    } elseif (str_contains(strtolower($transaction->description), 'conversion')) {
                                        $source = '🔄 Conversion de Devise';
                                        $icon = 'fas fa-exchange-alt';
                                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                                    } elseif (str_contains(strtolower($transaction->description), 'recharge')) {
                                        $source = '📱 Recharge Mobile Money';
                                        $icon = 'fas fa-mobile-alt';
                                        $badgeClass = 'bg-green-100 text-green-800';
                                    } else {
                                        $source = '💳 Transaction Générale';
                                        $icon = 'fas fa-credit-card';
                                        $badgeClass = 'bg-gray-100 text-gray-800';
                                    }
                                @endphp
                                
                                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200 transaction-card">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full {{ $transaction->type === 'credit' ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center">
                                                <i class="{{ $icon }} {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}"></i>
                                            </div>
                                            <div>
                                                <h6 class="font-semibold text-gray-900 text-sm">{{ $source }}</h6>
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                                    {{ $transaction->wallet->currency }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-700 mb-2">{{ $transaction->description }}</p>
                                        @if($transaction->reference)
                                            <p class="text-xs text-gray-500">Réf: {{ $transaction->reference }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    @if($recentTransactions->hasPages())
                        <div class="flex flex-col sm:flex-row justify-between items-center p-6 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center mb-4 sm:mb-0">
                                <small class="text-gray-600">
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
                    <div class="text-center py-16">
                        <div class="mb-6">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full">
                                <i class="fas fa-receipt text-3xl text-gray-400"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Aucune transaction</h3>
                        <p class="text-gray-500 mb-8 max-w-md mx-auto">Vos transactions apparaîtront ici une fois que vous aurez effectué des opérations.</p>
                        
                        <!-- Sources d'argent possibles -->
                        <div class="mb-8">
                            <h4 class="text-lg font-medium text-gray-700 mb-6">💡 Comment recevoir de l'argent dans votre portefeuille :</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-w-4xl mx-auto">
                                <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                                    <div class="text-center">
                                        <i class="fas fa-shopping-cart text-2xl text-green-600 mb-2"></i>
                                        <h5 class="font-semibold text-green-800">Vendre des articles</h5>
                                        <p class="text-sm text-green-700">Recevez l'argent de vos ventes</p>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                                    <div class="text-center">
                                        <i class="fas fa-percentage text-2xl text-blue-600 mb-2"></i>
                                        <h5 class="font-semibold text-blue-800">Commissions</h5>
                                        <p class="text-sm text-blue-700">Gagnez des commissions sur les ventes</p>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-primary-50 to-primary-100 p-4 rounded-lg border border-primary-200">
                                    <div class="text-center">
                                        <i class="fas fa-users text-2xl text-primary-600 mb-2"></i>
                                        <h5 class="font-semibold text-primary-800">Parrainage</h5>
                                        <p class="text-sm text-primary-700">Bonus de parrainage d'amis</p>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200">
                                    <div class="text-center">
                                        <i class="fas fa-gift text-2xl text-yellow-600 mb-2"></i>
                                        <h5 class="font-semibold text-yellow-800">Bonus</h5>
                                        <p class="text-sm text-yellow-700">Récompenses et bonus spéciaux</p>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 rounded-lg border border-emerald-200">
                                    <div class="text-center">
                                        <i class="fas fa-mobile-alt text-2xl text-emerald-600 mb-2"></i>
                                        <h5 class="font-semibold text-emerald-800">Recharge Mobile</h5>
                                        <p class="text-sm text-emerald-700">Ajout de fonds via Mobile Money</p>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 rounded-lg border border-indigo-200">
                                    <div class="text-center">
                                        <i class="fas fa-exchange-alt text-2xl text-indigo-600 mb-2"></i>
                                        <h5 class="font-semibold text-indigo-800">Conversion</h5>
                                        <p class="text-sm text-indigo-700">Change entre USD et CDF</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('wallet.add-funds', $usdWallet) }}" 
                               class="inline-flex items-center gap-2 bg-green-500 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-600 transition-colors duration-200">
                                <i class="fas fa-plus"></i>Ajouter des fonds USD
                            </a>
                            <a href="{{ route('wallet.add-funds', $cdfWallet) }}" 
                               class="inline-flex items-center gap-2 bg-yellow-500 text-white px-6 py-3 rounded-lg font-medium hover:bg-yellow-600 transition-colors duration-200">
                                <i class="fas fa-plus"></i>Ajouter des fonds CDF
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

// Fonction pour basculer l'affichage des dropdowns
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="-dropdown"]');
    
    // Fermer tous les autres dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });
    
    // Basculer le dropdown courant
    dropdown.classList.toggle('hidden');
}

// Fermer les dropdowns en cliquant ailleurs
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('[id$="-dropdown"]');
    const buttons = document.querySelectorAll('[onclick*="toggleDropdown"]');
    
    let clickedOnButton = false;
    buttons.forEach(button => {
        if (button.contains(event.target)) {
            clickedOnButton = true;
        }
    });
    
    if (!clickedOnButton) {
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
});

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
    refreshIcon.classList.add('fa-spin');
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
        refreshIcon.classList.remove('fa-spin');
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
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full ${alertClass} border rounded-lg p-4 shadow-lg`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas fa-${icon}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
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
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    
    messageDiv.innerHTML = `
        <div class="border rounded-lg p-4 ${alertClass}">
            <div class="flex items-center gap-2">
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
            </div>
        </div>
    `;
    messageDiv.classList.remove('hidden');
}

function refreshBalances() {
    const usdBalance = document.getElementById('usd-balance');
    const cdfBalance = document.getElementById('cdf-balance');
    
    const originalUsd = usdBalance.innerHTML;
    const originalCdf = cdfBalance.innerHTML;
    
    usdBalance.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    cdfBalance.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
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
        btn.classList.add('bg-gray-50', 'text-gray-700');
    });
    
    // Set active button
    const activeButton = document.getElementById('filter' + type.charAt(0).toUpperCase() + type.slice(1));
    if (activeButton) {
        activeButton.classList.remove('bg-gray-50', 'text-gray-700');
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