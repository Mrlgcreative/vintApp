@extends('layouts.admin')

@section('title', 'Monitoring & Métriques')

@section('content')
<div class="py-6" id="monitoring-app">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Monitoring & Métriques</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Dernière mise à jour: <span id="last-update" class="font-medium text-slate-700 dark:text-slate-300">{{ now()->format('d/m/Y H:i:s') }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <span id="live-connection" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 hidden">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                    <span id="live-connection-text">Connexion...</span>
                </span>

                <button
                    id="toggle-refresh"
                    class="inline-flex items-center gap-2 rounded-md bg-slate-900 dark:bg-violet-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-700 dark:hover:bg-violet-600"
                >
                    <i class="fas fa-sync-alt text-xs" aria-hidden="true"></i>
                    <span class="hidden sm:inline">Auto</span>
                    <span id="toggle-refresh-state" class="rounded-sm bg-white/20 px-1 text-xs">ON</span>
                </button>

                <button
                    id="refresh-btn"
                    onclick="refreshStats()"
                    class="inline-flex items-center gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700"
                >
                    <i class="fas fa-arrows-rotate text-xs" aria-hidden="true"></i>
                    Actualiser
                </button>
            </div>
        </div>

        <!-- Health Status -->
        <div id="health-status" class="flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3.5">
            <div class="text-sm">
                <span class="text-slate-500 dark:text-slate-400">État du système</span>
                <span class="ml-2 font-semibold uppercase tracking-wide text-slate-800 dark:text-slate-100">Chargement...</span>
            </div>
            <span id="health-status-icon">
                <i class="fas fa-circle-notch fa-spin text-slate-400" aria-hidden="true"></i>
            </span>
        </div>

        <div id="health-checks" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            @for($i = 0; $i < 3; $i++)
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 animate-pulse">
                <div class="flex items-center justify-between">
                    <div class="h-4 w-24 rounded bg-slate-200 dark:bg-slate-700"></div>
                    <div class="h-5 w-14 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="h-3 w-32 rounded bg-slate-200 dark:bg-slate-700"></div>
                    <div class="h-1.5 w-full rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    <div class="h-3 w-24 rounded bg-slate-200 dark:bg-slate-700"></div>
                </div>
            </div>
            @endfor
        </div>

        <!-- Alertes & Anomalies -->
        <div id="alerts-panel" class="mt-6 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-4 py-3">
                <p class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-shield-halved text-slate-400" aria-hidden="true"></i>
                    Alertes & Anomalies
                    <span id="alerts-count" class="hidden rounded-full px-2 py-0.5 text-xs font-semibold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300"></span>
                </p>
                <span class="text-xs text-slate-400 dark:text-slate-500">détection automatique</span>
            </div>

            <div id="alerts-list" class="divide-y divide-slate-100 dark:divide-slate-700/60">
                @forelse($alerts ?? [] as $alert)
                    <div class="flex items-start gap-3 px-4 py-3" data-alert-type="{{ $alert['type'] }}"
                         data-alert-severity="{{ $alert['severity'] }}">
                        <span class="mt-0.5 flex h-7 w-7 flex-none items-center justify-center rounded-full {{ $alert['severity'] === 'critical' ? 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400' : ($alert['severity'] === 'warning' ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400' : 'bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400') }}">
                            <i class="fas {{ $alert['severity'] === 'critical' ? 'fa-triangle-exclamation' : ($alert['severity'] === 'warning' ? 'fa-exclamation' : 'fa-info') }} text-xs" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ ucfirst($alert['label']) }}</p>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $alert['message'] }}</p>
                        </div>
                        <span class="flex-none self-start uppercase text-[10px] font-semibold tracking-wide {{ $alert['severity'] === 'critical' ? 'text-red-600 dark:text-red-400' : ($alert['severity'] === 'warning' ? 'text-amber-600 dark:text-amber-400' : 'text-sky-600 dark:text-sky-400') }}">
                            {{ $alert['severity'] }}
                        </span>
                    </div>
                @empty
                    <div id="alerts-empty" class="flex items-center gap-2 px-4 py-4 text-sm text-slate-400 dark:text-slate-500">
                        <i class="fas fa-circle-check text-emerald-500" aria-hidden="true"></i>
                        Aucune anomalie détectée. Le système est sain.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Primary Stats (shadcn stat cards) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <div class="flex items-center justify-between pb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Base de données</p>
                    <i class="fas fa-database text-slate-400 dark:text-slate-500" aria-hidden="true"></i>
                </div>
                <div id="db-stats" class="space-y-1">
                    <div class="text-sm text-slate-400 dark:text-slate-500">Chargement...</div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <div class="flex items-center justify-between pb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Revenus du jour</p>
                    <i class="fas fa-wallet text-slate-400 dark:text-slate-500" aria-hidden="true"></i>
                </div>
                <div id="revenue-today" class="space-y-1">
                    <div class="text-sm text-slate-400 dark:text-slate-500">Chargement...</div>
                </div>
                <p id="orders-today" class="pt-1 text-xs text-slate-500 dark:text-slate-400">0 commandes</p>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <div class="flex items-center justify-between pb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Cache</p>
                    <i class="fas fa-bolt text-slate-400 dark:text-slate-500" aria-hidden="true"></i>
                </div>
                <div id="cache-stats" class="space-y-1">
                    <div class="text-sm text-slate-400 dark:text-slate-500">Chargement...</div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <div class="flex items-center justify-between pb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Performance</p>
                    <i class="fas fa-gauge-high text-slate-400 dark:text-slate-500" aria-hidden="true"></i>
                </div>
                <div id="performance-stats" class="space-y-1">
                    <div class="text-sm text-slate-400 dark:text-slate-500">Chargement...</div>
                </div>
            </div>
        </div>

        <!-- Real-time Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <div class="flex items-center justify-between pb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Utilisateurs en ligne</p>
                    <span class="h-2 w-2 rounded-full bg-emerald-500" style="box-shadow:0 0 0 3px rgba(16,185,129,.12)"></span>
                </div>
                <div id="users-online" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">—</div>
                <p class="pt-1 text-xs text-slate-500 dark:text-slate-400">10 dernières minutes</p>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <div class="flex items-center justify-between pb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Charge serveur</p>
                    <i class="fas fa-microchip text-slate-400 dark:text-slate-500" aria-hidden="true"></i>
                </div>
                <div id="system-load" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">—</div>
                <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                    <div id="load-bar-fill" class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width:0%"></div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <div class="flex items-center justify-between pb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Nouveaux inscrits</p>
                    <i class="fas fa-user-plus text-slate-400 dark:text-slate-500" aria-hidden="true"></i>
                </div>
                <div id="new-users-today" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">—</div>
                <p class="pt-1 text-xs text-slate-500 dark:text-slate-400">Aujourd'hui</p>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <div class="flex items-center justify-between pb-2">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Annonces en attente</p>
                    <i class="fas fa-clock text-slate-400 dark:text-slate-500" aria-hidden="true"></i>
                </div>
                <div id="items-pending" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">—</div>
                <p class="pt-1 text-xs text-slate-500 dark:text-slate-400">À vérifier</p>
            </div>
        </div>

        <!-- History Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="flex items-center justify-between space-y-0 border-b border-slate-200 dark:border-slate-700 p-4 pb-3">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Revenus du jour</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">temps réel</p>
                </div>
                <div class="p-4">
                    <div class="h-44"><canvas id="revenue-chart"></canvas></div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="flex items-center justify-between space-y-0 border-b border-slate-200 dark:border-slate-700 p-4 pb-3">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Temps de réponse moyen</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">temps réel</p>
                </div>
                <div class="p-4">
                    <div class="h-44"><canvas id="performance-chart"></canvas></div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="flex items-center justify-between space-y-0 border-b border-slate-200 dark:border-slate-700 p-4 pb-3">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Utilisateurs connectés</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">temps réel</p>
                </div>
                <div class="p-4">
                    <div class="h-44"><canvas id="users-chart"></canvas></div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="flex items-center justify-between space-y-0 border-b border-slate-200 dark:border-slate-700 p-4 pb-3">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Erreurs</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">temps réel</p>
                </div>
                <div class="p-4">
                    <div class="h-44"><canvas id="errors-chart"></canvas></div>
                </div>
            </div>
        </div>

        <!-- Business Events & Errors -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="border-b border-slate-200 dark:border-slate-700 p-4 pb-3">
                    <p class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        <i class="fas fa-chart-line text-slate-400" aria-hidden="true"></i>
                        Événements Business
                    </p>
                </div>
                <div id="business-events" class="space-y-3 p-4 text-sm">
                    <div class="text-slate-400 dark:text-slate-500">Chargement...</div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="border-b border-slate-200 dark:border-slate-700 p-4 pb-3">
                    <p class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        <i class="fas fa-triangle-exclamation text-slate-400" aria-hidden="true"></i>
                        Erreurs
                    </p>
                </div>
                <div id="errors-section" class="space-y-3 p-4 text-sm">
                    <div class="text-slate-400 dark:text-slate-500">Chargement...</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
            <p class="mb-3 text-sm font-medium text-slate-700 dark:text-slate-200">Actions rapides</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <a href="/telescope" target="_blank"
                   class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-900 dark:bg-violet-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-700 dark:hover:bg-violet-600">
                    <i class="fas fa-telescope text-xs" aria-hidden="true"></i>
                    Telescope
                </a>
                <form action="/admin/monitoring/reset" method="POST" class="contents">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fas fa-rotate-left text-xs" aria-hidden="true"></i>
                        Réinitialiser métriques
                    </button>
                </form>
                <a href="/admin/monitoring/health" target="_blank"
                   class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="fas fa-heart-pulse text-xs" aria-hidden="true"></i>
                    Health Check API
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let autoRefresh = true;
let refreshInterval = null;

function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

function formatCurrency(amount, currency = 'XAF') {
    const currencyConfig = {
        'USD': { locale: 'en-US', currency: 'USD' },
        'CDF': { locale: 'fr-CD', currency: 'CDF' },
        'XAF': { locale: 'fr-CM', currency: 'XAF' },
        'EUR': { locale: 'fr-FR', currency: 'EUR' },
    };

    const config = currencyConfig[currency] || currencyConfig['XAF'];

    return new Intl.NumberFormat(config.locale, {
        style: 'currency',
        currency: config.currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(amount);
}

function getHealthTone(status) {
    return {
        healthy: { cls: 'border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300', badge: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300' },
        degraded: { cls: 'border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300', badge: 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' },
        unhealthy: { cls: 'border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300', badge: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' },
    }[status] || { cls: 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300', badge: 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' };
}

function getHealthIcon(status) {
    if (status === 'healthy') return '<i class="fas fa-circle-check text-emerald-500" aria-hidden="true"></i>';
    if (status === 'degraded') return '<i class="fas fa-triangle-exclamation text-amber-500" aria-hidden="true"></i>';
    if (status === 'unhealthy') return '<i class="fas fa-circle-xmark text-red-500" aria-hidden="true"></i>';
    return '<i class="fas fa-circle text-slate-400" aria-hidden="true"></i>';
}

async function refreshStats() {
    const btn = document.getElementById('refresh-btn');
    btn && (btn.disabled = true);

    try {
        const response = await fetch('/admin/monitoring/stats', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            if (response.status === 401 || response.status === 419) {
                window.location.reload();
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        updateDashboard(data);
    } catch (error) {
        console.error('Erreur lors du rafraîchissement:', error);
        autoRefresh = false;
        const toggleBtn = document.getElementById('toggle-refresh');
        const state = document.getElementById('toggle-refresh-state');
        toggleBtn.classList.remove('bg-slate-900', 'dark:bg-violet-500');
        toggleBtn.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
        if (state) state.textContent = 'OFF';
    } finally {
        btn && (btn.disabled = false);
    }
}

function updateDashboard(data) {
    const { stats, health, timestamp, alerts } = data;

    // Timestamp
    document.getElementById('last-update').textContent = new Date(timestamp).toLocaleString('fr-FR');

    // Health status
    const tone = getHealthTone(health.status);
    const healthStatus = document.getElementById('health-status');
    healthStatus.className = `flex items-center justify-between rounded-lg border px-4 py-3.5 ${tone.cls}`;
    document.getElementById('health-status-icon').innerHTML = getHealthIcon(health.status);
    const label = healthStatus.querySelector('div.text-sm');
    if (label) {
        const prior = label.querySelector('span:last-child');
        if (prior) prior.remove();
        label.appendChild(Object.assign(document.createElement('span'), {
            className: 'ml-2 font-semibold uppercase tracking-wide ' + tone.cls.split(' ').find(c => c.startsWith('text-')),
            textContent: health.status
        }));
    }

    // Health checks
    const healthChecks = document.getElementById('health-checks');
    healthChecks.innerHTML = Object.entries(health.checks || {}).map(([name, check]) => {
        const status = check.status === 'ok' ? 'ok' : check.status === 'warning' ? 'warning' : 'error';
        const tones = {
            ok: { badge: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300', bar: 'bg-emerald-500' },
            warning: { badge: 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300', bar: 'bg-amber-500' },
            error: { badge: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300', bar: 'bg-red-500' },
        }[status];
        const usage = Number(check.usage_percent) || 0;
        return `
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium capitalize text-slate-700 dark:text-slate-200">${name}</p>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium ${tones.badge}">${check.status}</span>
                </div>
                ${check.usage_percent ? `
                    <div class="mt-3 space-y-1.5">
                        <p class="text-xs text-slate-500 dark:text-slate-400">Utilisation: ${usage}%</p>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                            <div class="h-full rounded-full ${tones.bar}" style="width:${Math.min(100, usage)}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Libre: ${check.free_gb ?? '-'} GB</p>
                    </div>
                ` : ''}
                ${check.error ? `<p class="mt-2 text-xs text-red-600 dark:text-red-400">${check.error}</p>` : ''}
            </div>
        `;
    }).join('') || `<div class="col-span-full text-sm text-slate-400 dark:text-slate-500">Aucun contrôle</div>`;

    // Database
    document.getElementById('db-stats').innerHTML = `
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Utilisateurs</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">${formatNumber(stats.database.total_users)}</span>
        </div>
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Annonces actives</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">${formatNumber(stats.database.active_items)}</span>
        </div>
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Commandes en attente</span>
            <span class="text-sm font-semibold ${stats.database.pending_orders > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-slate-100'}">${formatNumber(stats.database.pending_orders)}</span>
        </div>
    `;

    // Revenue
    const revenueTodayElement = document.getElementById('revenue-today');
    const revenueData = stats.database.revenue_today;
    if (typeof revenueData === 'object' && revenueData && Object.keys(revenueData).length > 0) {
        revenueTodayElement.innerHTML = Object.entries(revenueData).map(([currency, amount]) => `
            <div class="flex items-baseline justify-between">
                <span class="text-xs text-slate-500 dark:text-slate-400">${currency}</span>
                <span class="text-base font-bold tracking-tight text-slate-900 dark:text-white">${formatCurrency(amount, currency)}</span>
            </div>
        `).join('');
    } else {
        revenueTodayElement.innerHTML = '<div class="text-sm text-slate-500 dark:text-slate-400">Aucun revenu</div>';
    }
    document.getElementById('orders-today').textContent = `${formatNumber(stats.database.total_orders_today)} commandes`;

    // Cache
    document.getElementById('cache-stats').innerHTML = `
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Taux de succès</span>
            <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">${stats.cache.hit_rate}%</span>
        </div>
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Hits</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">${formatNumber(stats.cache.hits)}</span>
        </div>
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Misses</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">${formatNumber(stats.cache.misses)}</span>
        </div>
    `;

    // Performance
    document.getElementById('performance-stats').innerHTML = `
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Temps moyen</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">${stats.performance.avg_response_time} ms</span>
        </div>
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Opérations lentes</span>
            <span class="text-sm font-semibold ${stats.performance.slow_operations > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'}">${stats.performance.slow_operations}</span>
        </div>
        <div class="flex items-baseline justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Total</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">${formatNumber(stats.performance.total_operations)}</span>
        </div>
    `;

    // Business events
    document.getElementById('business-events').innerHTML = `
        <div class="flex items-baseline justify-between">
            <span class="text-slate-500 dark:text-slate-400">Total événements</span>
            <span class="font-semibold text-slate-900 dark:text-slate-100">${formatNumber(stats.business.total_events)}</span>
        </div>
        ${stats.business.last_event ? `
            <div class="rounded-md border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/40 p-3">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">Dernier événement</p>
                <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200">${stats.business.last_event.event}</p>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">${new Date(stats.business.last_event.timestamp).toLocaleString('fr-FR')}</p>
            </div>
        ` : ''}
    `;

    // Errors
    document.getElementById('errors-section').innerHTML = `
        <div class="flex items-baseline justify-between">
            <span class="text-slate-500 dark:text-slate-400">Total erreurs</span>
            <span class="font-semibold ${stats.errors.total_errors > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'}">${stats.errors.total_errors}</span>
        </div>
        ${stats.errors.last_error ? `
            <div class="rounded-md border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20 p-3">
                <p class="text-xs font-medium uppercase tracking-wide text-red-500 dark:text-red-400">Dernière erreur</p>
                <p class="mt-1 text-sm font-medium break-all text-red-800 dark:text-red-300">${stats.errors.last_error.message}</p>
                <p class="mt-0.5 text-xs break-all text-red-600 dark:text-red-400">${stats.errors.last_error.file}:${stats.errors.last_error.line}</p>
                <p class="mt-0.5 text-xs text-red-500 dark:text-red-400">${new Date(stats.errors.last_error.timestamp).toLocaleString('fr-FR')}</p>
            </div>
        ` : `
            <div class="flex items-center gap-2 rounded-md border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-900/20 p-3 text-sm text-emerald-700 dark:text-emerald-300">
                <i class="fas fa-circle-check" aria-hidden="true"></i>
                Aucune erreur récente
            </div>
        `}
    `;

    window.lastData = data;
    renderCharts(data);
    renderRealtimeStats(stats);
    renderAlerts(alerts);
}

// Toggle auto-refresh
document.getElementById('toggle-refresh').addEventListener('click', function() {
    autoRefresh = !autoRefresh;
    const state = document.getElementById('toggle-refresh-state');
    if (state) state.textContent = autoRefresh ? 'ON' : 'OFF';
    if (autoRefresh) {
        this.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
        this.classList.add('bg-slate-900', 'dark:bg-violet-500', 'text-white');
    } else {
        this.classList.remove('bg-slate-900', 'dark:bg-violet-500', 'text-white');
        this.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
    }
});

/* ==================== Graphiques temps réel ==================== */

let charts = {};

function buildTimeSeries(points, valueKey) {
    const labels = (points || []).map(p => {
        const d = new Date(p.time);
        return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    });
    const values = (points || []).map(p => {
        let v = p.value;
        if (valueKey && typeof v === 'object') {
            v = Object.values(v).reduce((a, b) => a + Number(b), 0);
        }
        return Number(v) || 0;
    });
    return { labels, values };
}

function makeChart(canvasId, label, color, points, valueKey) {
    const el = document.getElementById(canvasId);
    if (!el || typeof Chart === 'undefined') return null;

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(100,116,139,0.10)';
    const tickColor = isDark ? '#94a3b8' : '#64748b';

    const { labels, values } = buildTimeSeries(points, valueKey);

    if (charts[canvasId]) {
        charts[canvasId].data.labels = labels;
        charts[canvasId].data.datasets[0].data = values;
        charts[canvasId].update();
        return charts[canvasId];
    }

    charts[canvasId] = new Chart(el, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label,
                data: values,
                borderColor: color,
                backgroundColor: color + '0f',
                fill: true,
                tension: 0.35,
                pointRadius: 0,
                pointHoverRadius: 4,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: tickColor, maxTicksLimit: 8, font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 } } }
            }
        }
    });
    return charts[canvasId];
}

function renderCharts(data) {
    if (!data) return;
    const series = data.stats.series || data.series || {};
    makeChart('revenue-chart', 'Revenus du jour', '#8B5CF6', series.revenue);
    makeChart('performance-chart', 'Temps moyen (ms)', '#94a3b8', series.performance);
    makeChart('users-chart', 'Utilisateurs connectés', '#f43f5e', series.users);
    makeChart('errors-chart', 'Erreurs', '#ef4444', series.errors);
}

/* ==================== Stats temps réel (cartes) ==================== */

function renderRealtimeStats(stats) {
    const rt = stats.realtime || {};

    const usersOnline = document.getElementById('users-online');
    if (usersOnline) usersOnline.textContent = formatNumber(rt.users_online ?? 0);

    const newUsers = document.getElementById('new-users-today');
    if (newUsers) newUsers.textContent = formatNumber(rt.new_users_today ?? 0);

    const itemsPending = document.getElementById('items-pending');
    if (itemsPending) itemsPending.textContent = formatNumber(rt.items_pending ?? 0);

    const systemLoad = document.getElementById('system-load');
    const loadFill = document.getElementById('load-bar-fill');
    if (systemLoad && rt.load_avg) {
        systemLoad.textContent = `${rt.load_avg['1min']} / ${rt.load_avg['5min']}`;
        if (loadFill) {
            const load = (rt.load_avg['1min'] / 10) * 100;
            loadFill.style.width = Math.min(100, load) + '%';
            loadFill.className = 'h-full rounded-full transition-all duration-500 ' +
                (load > 70 ? 'bg-red-500' : load > 40 ? 'bg-amber-500' : 'bg-emerald-500');
        }
    } else if (systemLoad) {
        systemLoad.textContent = 'N/A';
    }
}

/* ==================== Alertes & Anomalies ==================== */

function renderAlerts(alerts) {
    const list = document.getElementById('alerts-list');
    const count = document.getElementById('alerts-count');
    const listData = alerts || [];

    const severityStyle = s => {
        if (s === 'critical') return { dot: 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400', icon: 'fa-triangle-exclamation', label: 'text-red-600 dark:text-red-400' };
        if (s === 'warning') return { dot: 'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400', icon: 'fa-exclamation', label: 'text-amber-600 dark:text-amber-400' };
        return { dot: 'bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400', icon: 'fa-info', label: 'text-sky-600 dark:text-sky-400' };
    };

    if (!listData.length) {
        list.innerHTML = `
            <div class="flex items-center gap-2 px-4 py-4 text-sm text-slate-400 dark:text-slate-500">
                <i class="fas fa-circle-check text-emerald-500" aria-hidden="true"></i>
                Aucune anomalie détectée. Le système est sain.
            </div>`;
        count.classList.add('hidden');
        return;
    }

    list.innerHTML = listData.map(a => {
        const s = severityStyle(a.severity);
        return `
            <div class="flex items-start gap-3 px-4 py-3">
                <span class="mt-0.5 flex h-7 w-7 flex-none items-center justify-center rounded-full ${s.dot}">
                    <i class="fas ${s.icon} text-xs" aria-hidden="true"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-100">${a.label || a.type}</p>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">${a.message || ''}</p>
                </div>
                <span class="flex-none self-start uppercase text-[10px] font-semibold tracking-wide ${s.label}">${a.severity}</span>
            </div>`;
    }).join('');

    count.textContent = listData.length;
    count.classList.remove('hidden');
}

/* ==================== Connexion temps réel (Pusher) ==================== */

function setLiveConnection(state, label) {
    const pill = document.getElementById('live-connection');
    const text = document.getElementById('live-connection-text');
    if (!pill) return;

    pill.classList.remove('hidden');
    const dot = pill.querySelector('span');
    if (state === 'live') {
        dot.className = 'h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse';
        pill.className = 'inline-flex items-center gap-1.5 rounded-full border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-300';
    } else if (state === 'polling') {
        dot.className = 'h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse';
        pill.className = 'inline-flex items-center gap-1.5 rounded-full border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-900/20 px-3 py-1.5 text-xs font-medium text-amber-700 dark:text-amber-300';
    } else {
        dot.className = 'h-1.5 w-1.5 rounded-full bg-red-500';
        pill.className = 'inline-flex items-center gap-1.5 rounded-full border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20 px-3 py-1.5 text-xs font-medium text-red-700 dark:text-red-300';
    }
    text.textContent = label;
}

function initRealtime() {
    setLiveConnection('disconnected', 'Mode secours');

    try {
        @if($pusher['enabled'])
        if (window.Echo && typeof window.Echo !== 'undefined') {
            setLiveConnection('polling', 'Connexion temps réel...');
            window.Echo.channel('monitoring.updates')
                .listen('.monitoring.updated', (event) => {
                    setLiveConnection('live', 'Temps réel (WebSocket)');
                    updateDashboard({ stats: event.stats, health: event.health, timestamp: event.timestamp, alerts: event.alerts });
                })
                .error((error) => {
                    console.warn('Monitoring: Pusher indisponible, mode polling actif.', error);
                    setLiveConnection('polling', 'Polling (WebSocket indisponible)');
                });
        } else {
            setLiveConnection('polling', 'Polling (5s)');
        }
        @else
        setLiveConnection('polling', 'Polling (5s)');
        @endif
    } catch (e) {
        console.warn('Monitoring: impossible d\'initialiser le temps réel, mode polling.', e);
        setLiveConnection('polling', 'Polling (5s)');
    }
}

// Initial load
refreshStats();

// Auto-refresh every 5 seconds (fallback)
refreshInterval = setInterval(() => {
    if (autoRefresh) {
        refreshStats();
    }
}, 5000);

initRealtime();

setTimeout(() => {
    try {
        renderCharts(window.lastData);
    } catch (e) { /* pas encore de données */ }
}, 800);
</script>
@endsection
