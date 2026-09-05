@extends('layouts.admin')

@section('title', 'Téléchargements de l\'application')

@section('page-title', 'Téléchargements de l\'application')
@section('page-subtitle', 'Statistiques d\'adoption de VintApp sur Android')

@section('page-actions')
<div class="flex items-center gap-2">
    <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white p-0.5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        @foreach ([7 => '7j', 30 => '30j', 90 => '90j'] as $label => $value)
            <a href="{{ route('admin.downloads', ['days' => $value]) }}"
               class="inline-flex h-8 items-center rounded-md px-3 text-xs font-medium transition-colors {{ $days == $value ? 'bg-primary-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- ====== KPI Cards ====== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total téléchargements</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:text-violet-400">
                    <i class="fas fa-download text-sm"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($total) }}</p>
            <p class="mt-1 text-xs text-slate-400">Depuis le lancement</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Aujourd'hui</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-calendar-day text-sm"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($today) }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ now()->translatedFormat('l d F') }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Cette semaine</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400">
                    <i class="fas fa-calendar-week text-sm"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($thisWeek) }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ number_format($thisMonth) }} ce mois</p>
        </div>

        <div class="rounded-xl border border-slate-700 bg-gradient-to-br from-slate-800 to-slate-900 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-300">Meilleur jour</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-white">
                    <i class="fas fa-ranking-star text-sm"></i>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums tracking-tight text-white">
                {{ $bestDay ? number_format($bestDay['total']) : 0 }}
                <span class="text-base font-semibold text-slate-400">téléch.</span>
            </p>
            <p class="mt-1 text-xs text-slate-400">{{ $bestDay ? \Carbon\Carbon::parse($bestDay['date'])->translatedFormat('d F Y') : '—' }}</p>
        </div>
    </div>

    {{-- ====== Graphique principal (courbe) + Donut plateformes ====== --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 xl:col-span-2">
            <div class="flex flex-col gap-1 p-5 pb-2">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Évolution des téléchargements</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Nombre de téléchargements par jour ({{ $days }} derniers jours)</p>
            </div>
            <div class="px-2 pt-3 sm:px-6">
                <div class="relative h-[260px] w-full">
                    <canvas id="downloadsChart"></canvas>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-center gap-5 px-5 pb-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-300">
                    <span class="h-2 w-2 rounded-full bg-violet-500"></span> Android
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-300">
                    <span class="h-2 w-2 rounded-full bg-slate-300 dark:bg-slate-600"></span> Autres
                </span>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex flex-col gap-1 p-5 pb-2">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Appareils</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Répartition par type d'appareil</p>
            </div>
            <div class="px-4 pt-2">
                <div class="relative mx-auto h-[200px] w-[200px]">
                    <canvas id="devicesChart"></canvas>
                </div>
            </div>
            <div class="mt-2 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 px-5 pb-5">
                @foreach ($devices as $i => $d)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-300">
                        <span class="h-2 w-2 rounded-full bg-{{ ['violet-500', 'sky-500', 'emerald-500', 'amber-500', 'rose-500', 'slate-400'][$i % 6] }}"></span>
                        {{ $d->device_type ?: 'Inconnu' }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ====== OS + Plateformes (barres) ====== --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-3 border-b border-slate-100 p-5 dark:border-slate-700">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-500/10 text-primary-600 dark:text-primary-400"><i class="fas fa-mobile-screen text-sm"></i></span>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Systèmes d'exploitation</h3>
                    <p class="text-xs text-slate-400">Top des OS utilisateurs</p>
                </div>
            </div>
            <div class="space-y-3 p-5">
                @forelse ($os as $o)
                    @php
                        $pct = $total > 0 ? round(($o->total / $total) * 100) : 0;
                        $max = $os->max('total') ?: 1;
                        $bar = round(($o->total / $max) * 100);
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs">
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $o->os ?: 'Inconnu' }}</span>
                            <span class="tabular-nums text-slate-400">{{ number_format($o->total) }} · {{ $pct }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                            <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-primary-500 transition-all" style="width: {{ $bar }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-slate-400">Aucune donnée disponible</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-3 border-b border-slate-100 p-5 dark:border-slate-700">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"><i class="fas fa-box-open text-sm"></i></span>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Par plateforme</h3>
                    <p class="text-xs text-slate-400">Android, iOS, Web…</p>
                </div>
            </div>
            <div class="space-y-4 p-5">
                @forelse ($platforms as $p)
                    <div class="flex items-center gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300">
                            <i class="fas fa-{{ $p->platform === 'android' ? 'android' : 'mobile-screen-button' }} text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 capitalize dark:text-white">{{ $p->platform }}</p>
                            <p class="text-xs text-slate-400">{{ number_format($p->total) }} téléchargement(s)</p>
                        </div>
                        <div class="relative h-14 w-14">
                            <canvas class="platformDonut {{ $p->platform }}" data-total="{{ $p->total }}" data-count="{{ $platforms->count() }}"></canvas>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-slate-400">Aucune donnée disponible</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-3 border-b border-slate-100 p-5 dark:border-slate-700">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400"><i class="fas fa-globe text-sm"></i></span>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Appareils détaillés</h3>
                    <p class="text-xs text-slate-400">Types d'appareils détectés</p>
                </div>
            </div>
            <div class="max-h-[300px] divide-y divide-slate-100 overflow-y-auto dark:divide-slate-700">
                @forelse ($devices as $d)
                    <div class="flex items-center justify-between px-5 py-3">
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $d->device_type ?: 'Inconnu' }}</span>
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold tabular-nums text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ number_format($d->total) }}</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-slate-400">Aucune donnée disponible</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ====== Derniers téléchargements ====== --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between border-b border-slate-200 p-5 dark:border-slate-700">
            <div>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Derniers téléchargements</h3>
                <p class="text-xs text-slate-400">15 derniers événements</p>
            </div>
            <a href="{{ route('download') }}" target="_blank" class="inline-flex h-8 items-center gap-2 rounded-lg border border-slate-200 px-3 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
                <i class="fas fa-up-right-from-square text-[10px]"></i> Page de téléchargement
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full caption-bottom text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-400 dark:border-slate-700">
                        <th class="px-5 py-3 font-semibold">Date</th>
                        <th class="px-5 py-3 font-semibold">Plateforme</th>
                        <th class="px-5 py-3 font-semibold">Appareil</th>
                        <th class="hidden px-5 py-3 font-semibold sm:table-cell">Navigateur</th>
                        <th class="hidden px-5 py-3 font-semibold md:table-cell">Système</th>
                        <th class="hidden px-5 py-3 font-semibold lg:table-cell">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($recent as $d)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600 dark:text-slate-300">{{ $d->created_at->translatedFormat('d M Y · H:i') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                    <i class="fas fa-android text-[11px] {{ $d->platform === 'android' ? 'text-emerald-500' : 'text-slate-400' }}"></i>
                                    <span class="capitalize">{{ $d->platform }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ $d->device_type ?: '—' }}</td>
                            <td class="hidden px-5 py-3 text-slate-600 dark:text-slate-300 sm:table-cell">{{ $d->browser ?: '—' }}</td>
                            <td class="hidden px-5 py-3 text-slate-600 dark:text-slate-300 md:table-cell">{{ $d->os ?: '—' }}</td>
                            <td class="hidden p-2 px-5 py-3 font-mono text-xs text-slate-400 lg:table-cell">{{ $d->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-cloud-arrow-down text-2xl text-slate-300 dark:text-slate-600"></i>
                                    <p class="text-sm text-slate-400">Aucun téléchargement enregistré pour le moment.</p>
                                    <p class="text-xs text-slate-400">Les téléchargements depuis la page <a href="{{ route('download') }}" class="font-medium text-primary-600 dark:text-primary-400">/download</a> seront suivis ici.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function chartColors() {
    const dark = document.documentElement.classList.contains('dark');
    return {
        grid: dark ? 'rgba(148,163,184,0.08)' : 'rgba(148,163,184,0.15)',
        text: dark ? '#94a3b8' : '#64748b',
        violet: '#8b5cf6',
        sky: '#0ea5e9',
        emerald: '#10b981',
        amber: '#f59e0b',
        rose: '#f43f5e',
        slate: dark ? '#64748b' : '#cbd5e1',
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const c = chartColors();

    // Courbe téléchargements
    const ctx = document.getElementById('downloadsChart');
    if (ctx) {
        const labels = @json($dailySeries->pluck('date')->map(fn ($d) => \Carbon\Carbon::parse($d)->translatedFormat('d M')));
        const totals = @json($dailySeries->pluck('total'));
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(139,92,246,0.25)');
        gradient.addColorStop(1, 'rgba(139,92,246,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Android',
                    data: totals,
                    borderColor: c.violet,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointBackgroundColor: c.violet,
                    pointBorderColor: dark => dark ? '#1e293b' : '#ffffff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                        titleColor: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
                        bodyColor: c.text,
                        borderColor: c.grid,
                        borderWidth: 1,
                        padding: 12,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: c.text, precision: 0, maxTicksLimit: 6 },
                        grid: { color: c.grid },
                        border: { display: false },
                    },
                    x: {
                        ticks: { color: c.text, maxTicksLimit: 8, maxRotation: 0 },
                        grid: { display: false },
                        border: { display: false },
                    },
                },
            },
        });
    }

    // Donut appareils
    const deviceCtx = document.getElementById('devicesChart');
    if (deviceCtx) {
        const deviceLabels = @json($devices->map(fn ($d) => $d->device_type ?: 'Inconnu'));
        const deviceTotals = @json($devices->pluck('total'));
        new Chart(deviceCtx, {
            type: 'doughnut',
            data: {
                labels: deviceLabels,
                datasets: [{
                    data: deviceTotals,
                    backgroundColor: [c.violet, c.sky, c.emerald, c.amber, c.rose, c.slate],
                    borderWidth: 0,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#0f172a', padding: 12, titleColor: '#f8fafc', bodyColor: '#cbd5e1' },
                },
            },
        });
    }

    // Mini donuts plateformes (Android vs autres)
    document.querySelectorAll('canvas.platformDonut').forEach((canvas) => {
        const total = parseInt(canvas.dataset.total, 10) || 0;
        const android = @json($platforms->where('platform', 'android')->first()?->total ?? 0);
        const other = Math.max(0, total - android);
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [total === 0 ? 1 : total, other],
                    backgroundColor: canvas.classList.contains('android') ? c.violet : c.slate,
                    borderWidth: 0,
                    cutout: '72%',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                },
            },
        });
    });
});
</script>
@endpush