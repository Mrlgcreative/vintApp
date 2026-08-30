@extends('layouts.admin')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
@php
    $formatUsd = fn ($n) => '$' . number_format((float) $n, 2);
    $formatCdf = fn ($n) => number_format((float) $n, 0, ',', ' ') . ' FC';
@endphp

<div class="space-y-6">
    {{-- ====== Cartes KPI (style dashboard-01) ====== --}}
    <div class="grid grid-cols-1 gap-4 @xl/main:grid-cols-2 xl:grid-cols-4">
        {{-- Utilisateurs --}}
        <div class="rounded-xl border border-slate-200 bg-gradient-to-t from-primary-500/5 to-white shadow-sm dark:border-slate-700 dark:from-primary-500/10 dark:to-slate-800">
            <div class="relative p-5 pb-2">
                <p class="text-sm text-slate-500 dark:text-slate-400">Utilisateurs</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['total_users']) }}</p>
                <div class="absolute right-4 top-4">
                    <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-white px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-800/40 dark:bg-slate-900 dark:text-emerald-300">
                        <i class="fas fa-arrow-trend-up text-[10px]"></i>
                        +{{ $stats['new_users_today'] }}
                    </span>
                </div>
            </div>
            <div class="flex flex-col gap-0.5 px-5 pb-4 text-sm">
                <div class="flex items-center gap-2 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-user-plus text-xs text-emerald-500"></i>
                    {{ $stats['new_users_today'] }} inscrit{{ $stats['new_users_today'] > 1 ? 's' : '' }} aujourd'hui
                </div>
                <div class="text-xs text-slate-400">Base totale de la plateforme</div>
            </div>
        </div>

        {{-- Revenus USD --}}
        <div class="rounded-xl border border-slate-200 bg-gradient-to-t from-primary-500/5 to-white shadow-sm dark:border-slate-700 dark:from-primary-500/10 dark:to-slate-800">
            <div class="relative p-5 pb-2">
                <p class="text-sm text-slate-500 dark:text-slate-400">Revenus USD</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $formatUsd($stats['total_revenue_usd']) }}</p>
                <div class="absolute right-4 top-4">
                    <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                        <i class="fas fa-arrow-right-arrow-left text-[10px]"></i>
                        {{ $stats['transactions_today'] }}
                    </span>
                </div>
            </div>
            <div class="flex flex-col gap-0.5 px-5 pb-4 text-sm">
                <div class="flex items-center gap-2 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-wallet text-xs text-primary-500"></i>
                    Sous-wallets entreprise
                </div>
                <div class="text-xs text-slate-400">{{ $stats['transactions_today'] }} transaction(s) aujourd'hui</div>
            </div>
        </div>

        {{-- Articles actifs --}}
        <div class="rounded-xl border border-slate-200 bg-gradient-to-t from-primary-500/5 to-white shadow-sm dark:border-slate-700 dark:from-primary-500/10 dark:to-slate-800">
            <div class="relative p-5 pb-2">
                <p class="text-sm text-slate-500 dark:text-slate-400">Articles actifs</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['active_items']) }}</p>
            </div>
            <div class="flex flex-col gap-0.5 px-5 pb-4 text-sm">
                <div class="flex items-center gap-2 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-box-open text-xs text-sky-500"></i>
                    {{ number_format($stats['total_items']) }} au total
                </div>
                <div class="text-xs text-slate-400">Annonces disponibles</div>
            </div>
        </div>

        {{-- Commandes en attente --}}
        <div class="rounded-xl border border-slate-200 bg-gradient-to-t from-amber-500/5 to-white shadow-sm dark:border-slate-700 dark:from-amber-500/10 dark:to-slate-800">
            <div class="relative p-5 pb-2">
                <p class="text-sm text-slate-500 dark:text-slate-400">Commandes en attente</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['pending_orders']) }}</p>
            </div>
            <div class="flex flex-col gap-0.5 px-5 pb-4 text-sm">
                <div class="flex items-center gap-2 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-clock text-xs text-amber-500"></i>
                    À traiter
                </div>
                <div class="text-xs text-slate-400">En attente de traitement</div>
            </div>
        </div>
    </div>

    {{-- ====== Graphique interactif (style chart-area-interactive) ====== --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="relative flex flex-col gap-1 p-5 pb-3">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Évolution — activité</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Utilisateurs et revenus des 30 derniers jours</p>
            <div class="absolute right-4 top-4 inline-flex items-center gap-0.5 rounded-md border border-slate-200 p-0.5 dark:border-slate-600">
                <button type="button" data-range="7d"
                        class="inline-flex h-7 items-center rounded px-3 text-xs font-medium transition-colors">
                    7 derniers jours
                </button>
                <button type="button" data-range="30d"
                        class="inline-flex h-7 items-center rounded bg-slate-100 px-3 text-xs font-semibold text-slate-900 transition-colors dark:bg-slate-700 dark:text-white">
                    30 derniers jours
                </button>
            </div>
        </div>
        <div class="px-2 pb-4 pt-3 sm:px-6">
            <div class="relative h-[250px] w-full">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ====== Sous-wallets Entreprise ====== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="mb-1 flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                <span class="flex h-6 w-6 items-center justify-center rounded-md bg-cyan-500/10 text-cyan-600 dark:text-cyan-300"><i class="fas fa-truck text-[10px]"></i></span>
                Transport
            </p>
            <p class="text-xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $formatUsd($stats['enterprise_transport_usd'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="mb-1 flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                <span class="flex h-6 w-6 items-center justify-center rounded-md bg-primary-500/10 text-primary-600 dark:text-primary-300"><i class="fas fa-bolt text-[10px]"></i></span>
                Boost
            </p>
            <p class="text-xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $formatUsd($stats['enterprise_boost_usd'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="mb-1 flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                <span class="flex h-6 w-6 items-center justify-center rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-300"><i class="fas fa-shield-halved text-[10px]"></i></span>
                Vérifications
            </p>
            <p class="text-xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $formatUsd($stats['verification_revenue_usd'] ?? 0) }}</p>
            <p class="text-xs text-slate-400">{{ $stats['completed_verifications'] ?? 0 }} payées</p>
        </div>
        <div class="rounded-xl border border-slate-700 bg-gradient-to-br from-slate-800 to-slate-900 p-4 shadow-sm dark:border-slate-600 dark:from-slate-700 dark:to-slate-800">
            <p class="mb-1 flex items-center gap-2 text-xs font-medium text-slate-300">
                <span class="flex h-6 w-6 items-center justify-center rounded-md bg-white/10 text-white"><i class="fas fa-building-columns text-[10px]"></i></span>
                Total Entreprise
            </p>
            <p class="text-xl font-semibold tabular-nums tracking-tight text-white">{{ $formatUsd(($stats['enterprise_commission_usd'] ?? 0) + ($stats['enterprise_transport_usd'] ?? 0) + ($stats['enterprise_boost_usd'] ?? 0)) }}</p>
            <p class="text-xs text-slate-400">Sous-wallets USD</p>
        </div>
    </div>

    {{-- ====== Dernières transactions + Nouveaux utilisateurs ====== --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- Dernières transactions --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Dernières transactions</h3>
                <a href="{{ route('admin.transactions.index') }}"
                   class="inline-flex h-8 items-center rounded-md px-2.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white">
                    Tout voir
                </a>
            </div>
            <div class="overflow-hidden rounded-b-xl">
                <table class="w-full caption-bottom text-sm">
                    <thead>
                        <tr class="border-y border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/60">
                            <th class="h-9 px-4 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Utilisateur</th>
                            <th class="h-9 px-4 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="h-9 px-4 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-800/40">
                                <td class="px-4 py-2.5 align-middle">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900 dark:text-white">{{ $transaction->user?->name ?? 'Utilisateur supprimé' }}</p>
                                        <p class="truncate text-xs text-slate-400">{{ $transaction->description }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 align-middle">
                                    <span class="inline-flex items-center gap-1.5 rounded-md border border-transparent px-2 py-0.5 text-xs font-medium
                                        {{ $transaction->status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                           : ($transaction->status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'
                                           : 'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-300') }}">
                                        <i class="fas fa-circle text-[5px] opacity-70"></i>
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right align-middle whitespace-nowrap">
                                    <p class="font-semibold tabular-nums text-slate-900 dark:text-white">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</p>
                                    <p class="text-xs text-slate-400">{{ $transaction->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="h-24 px-4 text-center align-middle text-sm text-slate-400">Aucune transaction récente</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Nouveaux utilisateurs --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Nouveaux utilisateurs</h3>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex h-8 items-center rounded-md px-2.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white">
                    Tout voir
                </a>
            </div>
            <div class="overflow-hidden rounded-b-xl">
                <table class="w-full caption-bottom text-sm">
                    <thead>
                        <tr class="border-y border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/60">
                            <th class="h-9 px-4 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Utilisateur</th>
                            <th class="h-9 px-4 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Inscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                            @if($user)
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-800/40">
                                    <td class="px-4 py-2.5 align-middle">
                                        <div class="flex items-center gap-3">
                                            <span class="relative flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-500/10 text-xs font-semibold text-primary-600 dark:bg-primary-400/10 dark:text-primary-300">
                                                @if($user->avatar)
                                                    <img src="{{ $user->avatar_url }}" alt="" class="h-full w-full object-cover">
                                                @else
                                                    {{ $user->initial ?? strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                @endif
                                                @if(method_exists($user, 'isOnline') && $user->isOnline())
                                                    <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-500 dark:border-slate-800"></span>
                                                @endif
                                            </span>
                                            <div class="min-w-0">
                                                <p class="truncate font-medium text-slate-900 dark:text-white">{{ $user->name ?? 'Utilisateur' }}</p>
                                                <p class="truncate text-xs text-slate-400">{{ $user->email ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-right align-middle whitespace-nowrap text-slate-500 dark:text-slate-400">
                                        {{ $user->created_at?->diffForHumans() ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="2" class="h-24 px-4 text-center align-middle text-sm text-slate-400">Aucun nouvel utilisateur</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart');
    if (!ctx) return;

    const dailyStats = @json($dailyStats);
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.03)';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const violet = '#7c3aed';
    const emerald = '#10b981';

    const formatLabel = (dateStr) => {
        const d = new Date(dateStr);
        return d.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
    };

    const makeGradient = (hex, alphaTop, alphaBottom) => {
        const g = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
        g.addColorStop(0, hex.replace(')', alphaTop).replace('rgb', 'rgba'));
        g.addColorStop(1, hex.replace(')', alphaBottom).replace('rgb', 'rgba'));
        return g;
    };
    const hexToRgba = (hex, alpha) => {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r},${g},${b},${alpha})`;
    };

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyStats.map(s => formatLabel(s.date)),
            datasets: [{
                label: 'Utilisateurs',
                data: dailyStats.map(s => s.users),
                borderColor: violet,
                backgroundColor: hexToRgba(violet, isDark ? 0.18 : 0.08),
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 0,
                pointHitRadius: 20
            }, {
                label: 'Revenus (USD)',
                data: dailyStats.map(s => s.revenue),
                borderColor: emerald,
                backgroundColor: hexToRgba(emerald, isDark ? 0.18 : 0.08),
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 0,
                pointHitRadius: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 6,
                        boxHeight: 6,
                        color: textColor,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#ffffff',
                    titleColor: isDark ? '#f8fafc' : '#0f172a',
                    bodyColor: isDark ? '#cbd5e1' : '#64748b',
                    borderColor: isDark ? '#334155' : '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    boxPadding: 4,
                    usePointStyle: true
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, font: { size: 10 }, maxRotation: 0, autoSkipPadding: 24 }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 10 }, padding: 8 }
                }
            }
        }
    });

    const buttons = document.querySelectorAll('[data-range]');
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const range = btn.dataset.range;
            const days = range === '7d' ? 7 : 30;
            const sliced = dailyStats.slice(-days);
            chart.data.labels = sliced.map(s => formatLabel(s.date));
            chart.data.datasets[0].data = sliced.map(s => s.users);
            chart.data.datasets[1].data = sliced.map(s => s.revenue);
            chart.update();

            buttons.forEach(b => {
                const active = b.dataset.range === range;
                b.classList.toggle('bg-slate-100', active);
                b.classList.toggle('dark:bg-slate-700', active);
                b.classList.toggle('font-semibold', active);
                b.classList.toggle('text-slate-900', active);
                b.classList.toggle('dark:text-white', active);
            });
        });
    });
});
</script>
@endpush