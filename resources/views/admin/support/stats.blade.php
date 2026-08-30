@extends('layouts.support')

@section('title', 'Statistiques Support')

@section('content')
<div>
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Statistiques Support</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Analyse des performances du support client</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.support.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-arrow-left"></i>Retour
            </a>
        </div>
    </div>

    <!-- Filtre par période -->
    <div class="mb-6">
        <div class="inline-flex rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-1 shadow-sm">
            @foreach([7 => '7 jours', 14 => '14 jours', 30 => '30 jours', 60 => '60 jours', 90 => '90 jours'] as $value => $label)
                <a href="{{ route('admin.support.stats', ['period' => $value]) }}"
                   class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-lg transition-colors {{ (int)$period === $value ? 'bg-primary-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Vue d'ensemble -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Chats</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $stats['overview']['total_chats'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-comments text-[10px] text-sky-500"></i>
                    Total
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-comments text-xs text-sky-500"></i>
                    Conversations
                </div>
                <div class="text-xs text-slate-400">Sur la période</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Ouverts</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-red-600">{{ $stats['overview']['open_chats'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    Ouverts
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-exclamation-circle text-xs text-red-500"></i>
                    À traiter
                </div>
                <div class="text-xs text-slate-400">En attente de réponse</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">En Cours</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-amber-600">{{ $stats['overview']['in_progress_chats'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fas fa-clock text-[10px]"></i>
                    En cours
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-clock text-xs text-amber-500"></i>
                    Traitement en cours
                </div>
                <div class="text-xs text-slate-400">Assignées et suivies</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Fermés</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-emerald-600">{{ $stats['overview']['closed_chats'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fas fa-check-circle text-[10px]"></i>
                    Fermés
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-check-circle text-xs text-emerald-500"></i>
                    Résolus
                </div>
                <div class="text-xs text-slate-400">Clôturés sur la période</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Rép. Moy.</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-violet-600">{{ $stats['overview']['avg_response_time'] }}<span class="text-xs sm:text-sm font-normal ml-1">min</span></p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fas fa-stopwatch text-[10px]"></i>
                    Réponse
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-stopwatch text-xs text-violet-500"></i>
                    Premier temps de réponse
                </div>
                <div class="text-xs text-slate-400">En minutes en moyenne</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Résol. Moy.</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-indigo-600">{{ $stats['overview']['avg_resolution_time'] }}<span class="text-xs sm:text-sm font-normal ml-1">h</span></p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <i class="fas fa-hourglass-end text-[10px]"></i>
                    Résolution
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-hourglass-end text-xs text-indigo-500"></i>
                    Délai de résolution
                </div>
                <div class="text-xs text-slate-400">En heures en moyenne</div>
            </div>
        </div>
    </div>

    <!-- Graphique activité quotidienne -->
    <div class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
            <h2 class="flex items-center gap-2 text-base sm:text-lg font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-chart-line text-sky-500"></i>
                Activité Quotidienne
            </h2>
        </div>
        <div class="p-4 sm:p-6">
            <canvas id="dailyChart" height="300"></canvas>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Par catégorie -->
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h2 class="flex items-center gap-2 text-base sm:text-lg font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-tags text-emerald-500"></i>
                    Par Catégorie
                </h2>
            </div>
            <div class="p-5 sm:p-6">
                @if($stats['by_category']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['by_category'] as $cat)
                            @php
                                $total = $stats['by_category']->sum('count');
                                $percent = $total > 0 ? round(($cat->count / $total) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 capitalize">{{ $cat->category ?? 'Non classé' }}</span>
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $cat->count }} <span class="text-xs text-slate-400">({{ $percent }}%)</span></span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                                    <div class="h-2 rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-4 text-center text-sm text-slate-500 dark:text-slate-400">Aucune donnée pour cette période</p>
                @endif
            </div>
        </div>

        <!-- Par priorité -->
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h2 class="flex items-center gap-2 text-base sm:text-lg font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-flag text-amber-500"></i>
                    Par Priorité
                </h2>
            </div>
            <div class="p-5 sm:p-6">
                @if($stats['by_priority']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['by_priority'] as $prio)
                            @php
                                $total = $stats['by_priority']->sum('count');
                                $percent = $total > 0 ? round(($prio->count / $total) * 100) : 0;
                                $colors = [
                                    'low' => 'bg-sky-500',
                                    'medium' => 'bg-amber-500',
                                    'high' => 'bg-orange-500',
                                    'urgent' => 'bg-red-500',
                                ];
                                $labels = [
                                    'low' => 'Basse',
                                    'medium' => 'Moyenne',
                                    'high' => 'Haute',
                                    'urgent' => 'Urgente',
                                ];
                                $color = $colors[$prio->priority] ?? 'bg-slate-500';
                                $label = $labels[$prio->priority] ?? ucfirst($prio->priority ?? 'Non définie');
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</span>
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $prio->count }} <span class="text-xs text-slate-400">({{ $percent }}%)</span></span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                                    <div class="{{ $color }} h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-4 text-center text-sm text-slate-500 dark:text-slate-400">Aucune donnée pour cette période</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Performance des admins -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
            <h2 class="flex items-center gap-2 text-base sm:text-lg font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-users-cog text-violet-500"></i>
                Performance des Agents
            </h2>
        </div>
        <div class="p-5 sm:p-6">
            @if($stats['admin_performance']->count() > 0)
                <!-- Mobile: cards -->
                <div class="space-y-3 lg:hidden">
                    @foreach($stats['admin_performance'] as $admin)
                        @php $rate = $admin->total_assigned > 0 ? round(($admin->closed_chats / $admin->total_assigned) * 100) : 0; @endphp
                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-700/50">
                            <div class="mb-2 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-100 text-xs font-bold text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $admin->name }}</span>
                                </div>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $rate >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : ($rate >= 50 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                    {{ $rate }}%
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400">Assignés</span>
                                    <span class="ml-1 font-semibold text-slate-900 dark:text-white">{{ $admin->total_assigned }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400">Résolus</span>
                                    <span class="ml-1 font-semibold text-slate-900 dark:text-white">{{ $admin->closed_chats }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop: table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Agent</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Chats Assignés</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Chats Résolus</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Taux de Résolution</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($stats['admin_performance'] as $admin)
                                @php $rate = $admin->total_assigned > 0 ? round(($admin->closed_chats / $admin->total_assigned) * 100) : 0; @endphp
                                <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/30">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-violet-100 text-sm font-bold text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $admin->name }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $admin->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-900 dark:text-white">{{ $admin->total_assigned }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-900 dark:text-white">{{ $admin->closed_chats }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="h-2 w-20 rounded-full bg-slate-200 dark:bg-slate-700">
                                                <div class="{{ $rate >= 80 ? 'bg-emerald-500' : ($rate >= 50 ? 'bg-amber-500' : 'bg-red-500') }} h-2 rounded-full transition-all duration-500" style="width: {{ $rate }}%"></div>
                                            </div>
                                            <span class="text-sm font-semibold {{ $rate >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($rate >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">{{ $rate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="py-4 text-center text-sm text-slate-500 dark:text-slate-400">Aucun agent trouvé</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyStats = @json($stats['daily_stats']);
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(75,85,99,0.3)' : 'rgba(209,213,219,0.5)';

    const labels = dailyStats.map(s => {
        const d = new Date(s.date);
        return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    });

    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Nouveaux chats',
                    data: dailyStats.map(s => s.new_chats),
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14,165,233,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                },
                {
                    label: 'Chats fermés',
                    data: dailyStats.map(s => s.closed_chats),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                },
                {
                    label: 'Messages',
                    data: dailyStats.map(s => s.messages),
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    labels: { color: textColor, usePointStyle: true, padding: 16, font: { size: 12 } }
                },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#fff',
                    titleColor: isDark ? '#f8fafc' : '#0f172a',
                    bodyColor: isDark ? '#cbd5e1' : '#475569',
                    borderColor: isDark ? '#334155' : '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor, maxRotation: 45, font: { size: 10 } },
                    grid: { color: gridColor }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, stepSize: 1, font: { size: 11 } },
                    grid: { color: gridColor }
                }
            }
        }
    });
});
</script>
@endpush