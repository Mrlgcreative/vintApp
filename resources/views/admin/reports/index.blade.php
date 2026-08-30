@extends('layouts.admin')

@section('title', 'Rapports')
@section('page-title', 'Rapports et Statistiques')
@section('page-subtitle', 'Analyses de la plateforme selon la période')

@section('content')
<!-- Filtres de période -->
<div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <form method="GET" action="{{ route('admin.reports') }}" class="flex flex-wrap items-end gap-4">
        <label class="flex flex-col gap-1.5">
            <span class="flex items-center gap-1.5 text-sm font-medium text-slate-700 dark:text-slate-300">
                <i class="fas fa-calendar-days text-xs text-primary-500"></i>
                Période
            </span>
            <select name="period" onchange="this.form.submit()" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white sm:w-auto">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>7 derniers jours</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 derniers jours</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 derniers jours</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>1 an</option>
            </select>
        </label>
    </form>
</div>

<!-- Rapport Revenus -->
<div class="mb-8">
    <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
        <i class="fas fa-dollar-sign text-emerald-600"></i>
        Revenus
    </h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-3">
        <!-- Total des revenus -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total des revenus</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">${{ number_format($reports['revenue']['total'], 2) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fas fa-chart-line text-[10px]"></i>
                    Revenus
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-chart-line text-xs text-emerald-500"></i>
                    Revenus encaissés
                </div>
                <div class="text-xs text-slate-400">Sur {{ $period }} jours</div>
            </div>
        </div>

        <!-- Nombre de transactions -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Transactions</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($reports['revenue']['count']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-sky-200 bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-400">
                    <i class="fas fa-exchange-alt text-[10px]"></i>
                    Transactions
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-exchange-alt text-xs text-sky-500"></i>
                    Transactions complétées
                </div>
                <div class="text-xs text-slate-400">Sur {{ $period }} jours</div>
            </div>
        </div>

        <!-- Revenu moyen -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Revenu moyen</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">${{ number_format($reports['revenue']['average'], 2) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fas fa-calculator text-[10px]"></i>
                    Moyenne
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-calculator text-xs text-violet-500"></i>
                    Par transaction
                </div>
                <div class="text-xs text-slate-400">Revenu moyen estimé</div>
            </div>
        </div>
    </div>
</div>

<!-- Rapport Utilisateurs -->
<div class="mb-8">
    <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
        <i class="fas fa-users text-primary-600"></i>
        Utilisateurs
    </h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-3">
        <!-- Nouveaux utilisateurs -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Nouveaux utilisateurs</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($reports['users']['new_users']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-primary-200 bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 dark:border-primary-500/30 dark:bg-primary-500/10 dark:text-primary-400">
                    <i class="fas fa-user-plus text-[10px]"></i>
                    Nouveaux
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-user-plus text-xs text-primary-500"></i>
                    Inscriptions récentes
                </div>
                <div class="text-xs text-slate-400">Sur {{ $period }} jours</div>
            </div>
        </div>

        <!-- Utilisateurs actifs -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Utilisateurs actifs</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($reports['users']['active_users']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fas fa-user-check text-[10px]"></i>
                    Actifs
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-user-check text-xs text-emerald-500"></i>
                    Utilisateurs actifs
                </div>
                <div class="text-xs text-slate-400">Activité récente</div>
            </div>
        </div>

        <!-- Total utilisateurs -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($reports['users']['total_users']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fas fa-users text-[10px]"></i>
                    Total
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-users text-xs text-violet-500"></i>
                    Tous les utilisateurs
                </div>
                <div class="text-xs text-slate-400">Comptes enregistrés</div>
            </div>
        </div>
    </div>
</div>

<!-- Rapport Transactions -->
<div class="mb-8">
    <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
        <i class="fas fa-receipt text-sky-600"></i>
        Transactions
    </h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
        <!-- Total transactions -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($reports['transactions']['total']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-sky-200 bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-400">
                    <i class="fas fa-list text-[10px]"></i>
                    Total
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-list text-xs text-sky-500"></i>
                    Toutes les transactions
                </div>
                <div class="text-xs text-slate-400">Sur {{ $period }} jours</div>
            </div>
        </div>

        <!-- Transactions complétées -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Complétées</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-emerald-600">{{ number_format($reports['transactions']['completed']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fas fa-check-circle text-[10px]"></i>
                    Complétées
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-check-circle text-xs text-emerald-500"></i>
                    Transactions réussies
                </div>
                <div class="text-xs text-slate-400">
                    @if($reports['transactions']['total'] > 0)
                        {{ number_format(($reports['transactions']['completed'] / $reports['transactions']['total']) * 100, 1) }}% du total
                    @else
                        0% du total
                    @endif
                </div>
            </div>
        </div>

        <!-- Transactions en attente -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-amber-600">{{ number_format($reports['transactions']['pending']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fas fa-clock text-[10px]"></i>
                    En attente
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-clock text-xs text-amber-500"></i>
                    En cours de traitement
                </div>
                <div class="text-xs text-slate-400">
                    @if($reports['transactions']['total'] > 0)
                        {{ number_format(($reports['transactions']['pending'] / $reports['transactions']['total']) * 100, 1) }}% du total
                    @else
                        0% du total
                    @endif
                </div>
            </div>
        </div>

        <!-- Transactions échouées -->
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Échouées</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-red-600">{{ number_format($reports['transactions']['failed']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                    <i class="fas fa-times-circle text-[10px]"></i>
                    Échouées
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-times-circle text-xs text-red-500"></i>
                    Transactions en échec
                </div>
                <div class="text-xs text-slate-400">
                    @if($reports['transactions']['total'] > 0)
                        {{ number_format(($reports['transactions']['failed'] / $reports['transactions']['total']) * 100, 1) }}% du total
                    @else
                        0% du total
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Articles populaires -->
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
        <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
            <i class="fas fa-fire text-orange-500"></i>
            Articles les plus populaires
        </h3>
        @if($reports['popular_items']->count() > 0)
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ $reports['popular_items']->count() }} articles
            </span>
        @endif
    </div>

    @if($reports['popular_items']->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 dark:bg-slate-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Article</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Favoris</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Commandes</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Prix</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white dark:bg-slate-800">
                @foreach($reports['popular_items'] as $index => $item)
                <tr class="transition-colors hover:bg-slate-50 dark:bg-slate-900">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full font-bold text-sm {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400')) }}">
                            {{ $index + 1 }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if(!empty($item->images) && is_array($item->images))
                            <img src="{{ Storage::url($item->images[0]) }}" alt="{{ $item->name }}" class="h-12 w-12 rounded-lg object-cover">
                            @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800">
                                <i class="fas fa-image text-slate-400"></i>
                            </div>
                            @endif
                            <div>
                                <div class="font-medium text-slate-900 dark:text-white">{{ $item->name }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $item->category->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium bg-accent-100 text-accent-700">
                            <i class="fas fa-heart text-xs"></i>
                            {{ number_format($item->favorites_count) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium bg-sky-100 text-sky-700">
                            <i class="fas fa-shopping-cart text-xs"></i>
                            {{ number_format($item->orders_count) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-semibold text-slate-900 dark:text-white">${{ number_format($item->price, 2) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="p-12 text-center">
        <i class="fas fa-box-open text-5xl text-slate-300 mb-4"></i>
        <p class="font-medium text-slate-500 dark:text-slate-400">Aucun article populaire pour cette période</p>
    </div>
    @endif
</div>

@endsection