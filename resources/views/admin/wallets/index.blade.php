@extends('layouts.admin')

@section('title', 'Gestion des Wallets')
@section('page-title', 'Gestion des Wallets Entreprise')

@section('page-actions')
<button type="button" onclick="openModal('createWalletModal')"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
    <i class="fas fa-plus"></i>Nouveau Portefeuille
</button>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Statistiques Principales -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Portefeuilles Actifs</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ $totalWallets ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Solde Total USD</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">${{ number_format($totalUsdBalance ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Solde Total CDF</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ number_format($totalCdfBalance ?? 0, 0, ',', ' ') }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-900/20 dark:text-sky-300">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Commissions du Jour</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ $commissionsToday ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Portefeuilles Entreprise -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">
            <h5 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-building text-primary-600 dark:text-primary-400"></i>
                Portefeuilles Entreprise VintApp
            </h5>
            <button class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors" onclick="openModal('createWalletModal')">
                <i class="fas fa-plus"></i>
                Nouveau Portefeuille
            </button>
        </div>
        <div class="p-5 sm:p-6">
            @if(!empty($enterpriseWallets) && count($enterpriseWallets) > 0)
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    @foreach($enterpriseWallets ?? [] as $wallet)
                        <div class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h6 class="flex items-center gap-2.5 text-base font-semibold text-slate-900 dark:text-white">
                                        <i class="fas fa-building text-primary-600 dark:text-primary-400"></i>
                                        Portefeuille Entreprise {{ $wallet->currency ?? 'N/A' }}
                                    </h6>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                        Commission: {{ $wallet->commission_rate ?? 5 }}%
                                    </p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                    {{ $wallet->currency ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mt-6 text-center">
                                <div class="text-4xl font-extrabold tabular-nums text-slate-900 dark:text-white">
                                    @if(($wallet->currency ?? 'USD') === 'USD')
                                        ${{ number_format($wallet->balance ?? 0, 2) }}
                                    @else
                                        {{ number_format($wallet->balance ?? 0, 0, ',', ' ') }} FC
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-slate-400">Solde disponible</p>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-900/50">
                                <div>
                                    <div class="text-xl font-semibold tabular-nums text-slate-900 dark:text-white">{{ $wallet->total_transactions ?? 0 }}</div>
                                    <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Transactions</div>
                                </div>
                                <div>
                                    <div class="text-xl font-semibold tabular-nums text-slate-900 dark:text-white">
                                        @if(($wallet->currency ?? 'USD') === 'USD')
                                            ${{ number_format($wallet->total_commissions ?? 0, 2) }}
                                        @else
                                            {{ number_format($wallet->total_commissions ?? 0, 0, ',', ' ') }} FC
                                        @endif
                                    </div>
                                    <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Commissions</div>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col gap-2 sm:flex-row">
                                <button class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors"
                                        onclick="openCommissionModal('{{ $wallet->id ?? '' }}')">
                                    <i class="fas fa-plus-circle"></i>
                                    Ajouter
                                </button>
                                <button class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                        onclick="openWithdrawModal('{{ $wallet->id ?? '' }}')">
                                    <i class="fas fa-arrow-down"></i>
                                    Retirer
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-wallet mb-3 text-4xl text-slate-200 dark:text-slate-600"></i>
                    <h5 class="mb-2 text-base font-semibold text-slate-900 dark:text-white">Aucun Portefeuille Entreprise</h5>
                    <p class="mx-auto mb-6 max-w-md text-sm text-slate-400">Créez votre premier portefeuille entreprise pour commencer à gérer les commissions et les finances de VintApp.</p>
                    <button class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors" onclick="openModal('createWalletModal')">
                        <i class="fas fa-plus"></i>
                        Créer Premier Portefeuille
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Graphiques et Analytics -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                    <h5 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-chart-area text-primary-600 dark:text-primary-400"></i>
                        Évolution des Commissions (30 derniers jours)
                    </h5>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="relative h-80">
                        <canvas id="commissionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="h-full rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                    <h5 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-chart-pie text-primary-600 dark:text-primary-400"></i>
                        Répartition par Devise
                    </h5>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="relative h-80">
                        <canvas id="currencyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières Transactions Entreprise -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">
            <h5 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-history text-primary-600 dark:text-primary-400"></i>
                Dernières Transactions Entreprise
            </h5>
            <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                <i class="fas fa-external-link-alt"></i>
                Voir Toutes
            </a>
        </div>
        @if(!empty($recentTransactions) && count($recentTransactions) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Date/Heure</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Montant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Devise</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($recentTransactions ?? [] as $transaction)
                            <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        {{ $transaction->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $transaction->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    @if($transaction->type === 'credit')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                            <i class="fas fa-plus"></i>Commission
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                            <i class="fas fa-minus"></i>Retrait
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <span class="font-semibold tabular-nums {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-amber-600 dark:text-amber-400' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}
                                        @if($transaction->currency === 'USD')
                                            ${{ number_format($transaction->amount, 2) }}
                                        @else
                                            {{ number_format($transaction->amount, 0, ',', ' ') }} FC
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    @if($transaction->currency === 'USD')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                            {{ $transaction->currency }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                            {{ $transaction->currency }}
                                        </span>
                                    @endif
                                </td>
                                <td class="max-w-[200px] px-4 py-3 align-middle">
                                    <div class="truncate text-slate-700 dark:text-slate-200">
                                        {{ $transaction->description ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    @switch($transaction->status ?? 'pending')
                                        @case('completed')
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">Complété</span>
                                            @break
                                        @case('pending')
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">En attente</span>
                                            @break
                                        @case('failed')
                                            <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">Échoué</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300">{{ $transaction->status ?? 'N/A' }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-history mb-3 text-4xl text-slate-200 dark:text-slate-600"></i>
                <p class="text-sm text-slate-400">Aucune transaction récente trouvée</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Créer Wallet -->
<div id="createWalletModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
    <div class="max-h-screen w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl dark:bg-slate-800">
        <div class="flex items-center justify-between rounded-t-2xl bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-4 text-white">
            <h5 class="flex items-center gap-3 text-base font-bold">
                <i class="fas fa-plus"></i>
                Créer un Nouveau Portefeuille Entreprise
            </h5>
            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg text-white transition-colors hover:bg-white/10" onclick="closeModal('createWalletModal')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('admin.wallets.store') }}" method="POST">
            @csrf
            <div class="p-6 sm:p-8">
                <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="currency" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Devise *</label>
                        <select class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors" name="currency" id="currency" required>
                            <option value="">Sélectionner une devise</option>
                            <option value="USD">USD (Dollar Américain)</option>
                            <option value="CDF">CDF (Franc Congolais)</option>
                        </select>
                        <small class="mt-1 block text-sm text-slate-400">Choisissez la devise principale du portefeuille</small>
                    </div>

                    <div>
                        <label for="commission_rate" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Taux de Commission (%)</label>
                        <input type="number" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors" name="commission_rate" id="commission_rate"
                               value="5.00" step="0.01" min="0" max="100" placeholder="Ex: 5.00">
                        <small class="mt-1 block text-sm text-slate-400">Pourcentage de commission sur les transactions</small>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="initial_balance" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Solde Initial</label>
                    <input type="number" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors" name="initial_balance" id="initial_balance"
                           value="0.00" step="0.01" min="0" placeholder="0.00">
                    <small class="mt-1 block text-sm text-slate-400">Solde initial du wallet (optionnel)</small>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-2 rounded-b-2xl border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-slate-700 dark:bg-slate-900">
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" onclick="closeModal('createWalletModal')">
                    Annuler
                </button>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-save"></i>
                    Créer Portefeuille
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ajouter Commission -->
<div id="addCommissionModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
    <div class="max-h-screen w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl dark:bg-slate-800">
        <div class="flex items-center justify-between rounded-t-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-5 py-4 text-white">
            <h5 class="flex items-center gap-3 text-base font-bold">
                <i class="fas fa-plus-circle"></i>
                Ajouter Commission
            </h5>
            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg text-white transition-colors hover:bg-white/10" onclick="closeModal('addCommissionModal')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <form id="addCommissionForm" method="POST">
            @csrf
            <div class="p-6 sm:p-8">
                <div class="mb-6">
                    <label for="commissionAmount" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Montant *</label>
                    <input type="number" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition-colors" name="amount" id="commissionAmount"
                           step="0.01" min="0.01" required placeholder="Entrez le montant">
                </div>

                <div class="mb-6">
                    <label for="commissionDescription" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                    <textarea class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition-colors" name="description" id="commissionDescription"
                              rows="3" placeholder="Description de la commission (optionnel)"></textarea>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-2 rounded-b-2xl border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-slate-700 dark:bg-slate-900">
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" onclick="closeModal('addCommissionModal')">
                    Annuler
                </button>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-plus"></i>
                    Ajouter Commission
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Retirer Fonds -->
<div id="withdrawModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
    <div class="max-h-screen w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl dark:bg-slate-800">
        <div class="flex items-center justify-between rounded-t-2xl bg-gradient-to-r from-red-600 to-red-700 px-5 py-4 text-white">
            <h5 class="flex items-center gap-3 text-base font-bold">
                <i class="fas fa-arrow-down"></i>
                Retirer des Fonds
            </h5>
            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg text-white transition-colors hover:bg-white/10" onclick="closeModal('withdrawModal')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <form id="withdrawForm" method="POST">
            @csrf
            <div class="p-6 sm:p-8">
                <div class="mb-6">
                    <label for="withdrawAmount" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Montant *</label>
                    <input type="number" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 transition-colors" name="amount" id="withdrawAmount"
                           step="0.01" min="0.01" required placeholder="Entrez le montant à retirer">
                </div>

                <div class="mb-6">
                    <label for="withdrawDescription" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                    <textarea class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 transition-colors" name="description" id="withdrawDescription"
                              rows="3" placeholder="Raison du retrait (optionnel)"></textarea>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-2 rounded-b-2xl border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-slate-700 dark:bg-slate-900">
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" onclick="closeModal('withdrawModal')">
                    Annuler
                </button>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-minus"></i>
                    Retirer Fonds
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Fonctions pour gérer les modales
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openCommissionModal(walletId) {
    const form = document.getElementById('addCommissionForm');
    form.action = `/admin/wallets/${walletId}/add-commission`;
    openModal('addCommissionModal');
}

function openWithdrawModal(walletId) {
    const form = document.getElementById('withdrawForm');
    form.action = `/admin/wallets/${walletId}/withdraw`;
    openModal('withdrawModal');
}

// Fermer les modales en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const modals = ['createWalletModal', 'addCommissionModal', 'withdrawModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            closeModal(modalId);
        }
    });
});

// Fermer les modales avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['createWalletModal', 'addCommissionModal', 'withdrawModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Graphique des commissions
    const commissionsCtx = document.getElementById('commissionsChart');
    if (commissionsCtx) {
        new Chart(commissionsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels'] ?? []) !!},
                datasets: [{
                    label: 'Commissions USD',
                    data: {!! json_encode($chartData['usd'] ?? []) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Commissions CDF',
                    data: {!! json_encode($chartData['cdf'] ?? []) !!},
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Graphique en secteurs des devises
    const currencyCtx = document.getElementById('currencyChart');
    if (currencyCtx) {
        new Chart(currencyCtx, {
            type: 'doughnut',
            data: {
                labels: ['USD', 'CDF'],
                datasets: [{
                    data: [{!! $totalUsdBalance ?? 0 !!}, {!! $totalCdfBalance ?? 0 !!}],
                    backgroundColor: ['#28a745', '#007bff'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
});
</script>

<!-- Script de test pour déboguer les modals -->
<script src="{{ asset('js/wallet-modal-test.js') }}"></script>

<script>
// Test supplémentaire pour vérifier la console
setTimeout(function() {
    console.log('=== DIAGNOSTIC MODAL WALLETS ===');
    console.log('Page chargée après 2 secondes');
    console.log('Tentative d\'ouverture du modal de test...');

    // Test si on peut ouvrir un modal manuellement
    if (typeof openModal !== 'undefined') {
        console.log('✅ Fonction openModal disponible');
        // Tester dans 1 seconde
        setTimeout(() => {
            console.log('Test d\'ouverture automatique du modal createWalletModal...');
            try {
                openModal('createWalletModal');
                console.log('✅ Modal ouvert avec succès');

                // Fermer après 3 secondes
                setTimeout(() => {
                    closeModal('createWalletModal');
                    console.log('✅ Modal fermé avec succès');
                }, 3000);
            } catch (error) {
                console.error('❌ Erreur lors de l\'ouverture du modal:', error);
            }
        }, 1000);
    } else {
        console.error('❌ Fonction openModal non disponible');
    }
}, 2000);
</script>
@endpush
