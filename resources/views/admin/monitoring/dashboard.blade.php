@extends('layouts.admin')

@section('content')
<div class="py-6" id="monitoring-app">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">📈 Monitoring & Métriques</h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Dernière mise à jour: <span id="last-update">{{ now()->format('d/m/Y H:i:s') }}</span>
                    <span id="refresh-indicator" class="ml-2 hidden">
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    </span>
                </p>
            </div>
            
            <div class="flex gap-2 sm:gap-3 items-center flex-wrap">
                <span id="live-connection" class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hidden items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-slate-400"></span>
                    <span id="live-connection-text">Connexion...</span>
                </span>
                <button
                    id="toggle-refresh"
                    class="px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition bg-green-600 text-white hover:bg-green-700"
                >
                    🔄 <span class="hidden sm:inline">Auto-refresh</span> ON
                </button>
                
                <button
                    id="refresh-btn"
                    onclick="refreshStats()"
                    class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg text-sm sm:text-base hover:bg-blue-700 transition"
                >
                    🔄 Actualiser
                </button>
            </div>
        </div>

        <!-- Health Status -->
        <div class="mb-6">
            <div id="health-status" class="px-4 sm:px-6 py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg flex items-center justify-between bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 animate-pulse">
                État du système: <span class="uppercase">Chargement...</span>
            </div>
            
            <!-- Health Checks Details -->
            <div id="health-checks" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mt-4">
                <!-- Skeleton placeholders -->
                @for($i = 0; $i < 3; $i++)
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4 animate-pulse">
                    <div class="flex items-center justify-between">
                        <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-24"></div>
                        <div class="h-5 bg-slate-200 dark:bg-slate-700 rounded w-12"></div>
                    </div>
                    <div class="mt-3 space-y-2">
                        <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-32"></div>
                        <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-28"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6">
            <!-- Database Stats -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-slate-800 dark:text-slate-100">Base de données</h3>
                    <span class="text-xl sm:text-3xl">🗄️</span>
                </div>
                <div id="db-stats" class="space-y-1.5 sm:space-y-2 text-xs sm:text-base">
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Revenue Today -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-3 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold">Revenus <span class="hidden sm:inline">Aujourd'hui</span></h3>
                    <span class="text-xl sm:text-3xl">💰</span>
                </div>
                <div id="revenue-today" class="space-y-1.5 sm:space-y-2">
                    <div class="text-lg sm:text-2xl font-bold">Chargement...</div>
                </div>
                <p id="orders-today" class="text-xs sm:text-sm mt-2 sm:mt-3 opacity-90">
                    0 commandes
                </p>
            </div>

            <!-- Cache Stats -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-slate-800 dark:text-slate-100">Cache</h3>
                    <span class="text-xl sm:text-3xl">⚡</span>
                </div>
                <div id="cache-stats" class="space-y-1.5 sm:space-y-2 text-xs sm:text-base">
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Performance Stats -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-slate-800 dark:text-slate-100">Performance</h3>
                    <span class="text-xl sm:text-3xl">📊</span>
                </div>
                <div id="performance-stats" class="space-y-1.5 sm:space-y-2 text-xs sm:text-base">
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Stats Grid (live) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 mb-6">
            <!-- Users Online -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-3 sm:p-6 text-white">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <h3 class="text-xs sm:text-sm font-semibold">Utilisateurs en ligne</h3>
                    <span class="text-lg sm:text-2xl">🟢</span>
                </div>
                <div id="users-online" class="text-2xl sm:text-3xl font-bold">—</div>
                <p class="text-[11px] sm:text-xs mt-1 sm:mt-2 opacity-90">10 dernières minutes</p>
            </div>

            <!-- System Load -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">Charge serveur (1/5 min)</h3>
                    <span class="text-lg sm:text-2xl">🧮</span>
                </div>
                <div id="system-load" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">—</div>
                <div id="load-bar" class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-2">
                    <div id="load-bar-fill" class="h-1.5 rounded-full bg-green-500 transition-all" style="width:0%"></div>
                </div>
            </div>

            <!-- New users today -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">Nouveaux inscrits (24h)</h3>
                    <span class="text-lg sm:text-2xl">👥</span>
                </div>
                <div id="new-users-today" class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">—</div>
                <p class="text-[11px] sm:text-xs mt-1 sm:mt-2 text-slate-500 dark:text-slate-400">Aujourd'hui</p>
            </div>

            <!-- Items pending -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100">Annonces en attente</h3>
                    <span class="text-lg sm:text-2xl">⏳</span>
                </div>
                <div id="items-pending" class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">—</div>
                <p class="text-[11px] sm:text-xs mt-1 sm:mt-2 text-slate-500 dark:text-slate-400">À vérifier</p>
            </div>
        </div>

        <!-- History Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-6 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-slate-800 dark:text-slate-100 flex items-center">
                        <span class="mr-2">💹</span> Revenus du jour
                    </h3>
                    <span class="text-xs text-slate-400">temps réel</span>
                </div>
                <canvas id="revenue-chart" style="height:170px;width:100%" height="200"></canvas>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-slate-800 dark:text-slate-100 flex items-center">
                        <span class="mr-2">⚡</span> Temps de réponse moyen (ms)
                    </h3>
                    <span class="text-xs text-slate-400">temps réel</span>
                </div>
                <canvas id="performance-chart" style="height:170px;width:100%" height="200"></canvas>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-slate-800 dark:text-slate-100 flex items-center">
                        <span class="mr-2">🟢</span> Utilisateurs connectés
                    </h3>
                    <span class="text-xs text-slate-400">temps réel</span>
                </div>
                <canvas id="users-chart" style="height:170px;width:100%" height="200"></canvas>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-slate-800 dark:text-slate-100 flex items-center">
                        <span class="mr-2">🚨</span> Nombre d'erreurs
                    </h3>
                    <span class="text-xs text-slate-400">temps réel</span>
                </div>
                <canvas id="errors-chart" style="height:170px;width:100%" height="200"></canvas>
            </div>
        </div>

        <!-- Business Events & Errors -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-6">
            <!-- Business Events -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 sm:mb-4 flex items-center">
                    <span class="mr-2">📈</span>
                    Événements Business
                </h3>
                <div id="business-events" class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 dark:text-slate-400">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Errors -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 sm:mb-4 flex items-center">
                    <span class="mr-2">🚨</span>
                    Erreurs
                </h3>
                <div id="errors-section" class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 dark:text-slate-400">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-4 sm:mt-6 bg-slate-50 dark:bg-slate-800 rounded-lg p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 sm:mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 sm:flex sm:flex-wrap gap-2 sm:gap-3">
                <a href="/telescope" target="_blank" 
                   class="px-4 py-2.5 sm:py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm sm:text-base text-center">
                    🔭 Ouvrir Telescope
                </a>
                <form action="/admin/monitoring/reset" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 sm:py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition text-sm sm:text-base">
                        🔄 Réinitialiser métriques
                    </button>
                </form>
                <a href="/admin/monitoring/health" target="_blank"
                   class="px-4 py-2.5 sm:py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm sm:text-base text-center">
                    🏥 Health Check API
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
        'USD': { locale: 'en-US', currency: 'USD', symbol: '$' },
        'CDF': { locale: 'fr-CD', currency: 'CDF', symbol: 'FC' },
        'XAF': { locale: 'fr-CM', currency: 'XAF', symbol: 'FCFA' },
        'EUR': { locale: 'fr-FR', currency: 'EUR', symbol: '€' },
    };
    
    const config = currencyConfig[currency] || currencyConfig['XAF'];
    
    return new Intl.NumberFormat(config.locale, {
        style: 'currency',
        currency: config.currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(amount);
}

function getHealthColor(status) {
    const colors = {
        healthy: 'text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/40',
        degraded: 'text-yellow-700 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/40',
        unhealthy: 'text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/40',
    };
    return colors[status] || 'text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700';
}

async function refreshStats() {
    const btn = document.getElementById('refresh-btn');
    const indicator = document.getElementById('refresh-indicator');
    btn && (btn.disabled = true);
    indicator && indicator.classList.remove('hidden');
    
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
        toggleBtn.innerHTML = '\u23f8\ufe0f <span class="hidden sm:inline">Auto-refresh</span> OFF';
        toggleBtn.classList.remove('bg-green-600');
        toggleBtn.classList.add('bg-slate-300', 'dark:bg-slate-600', 'text-slate-700', 'dark:text-slate-300');
    } finally {
        const btn = document.getElementById('refresh-btn');
        const indicator = document.getElementById('refresh-indicator');
        btn && (btn.disabled = false);
        indicator && indicator.classList.add('hidden');
    }
}

function updateDashboard(data) {
    const { stats, health, timestamp } = data;
    
    // Update timestamp
    document.getElementById('last-update').textContent = new Date(timestamp).toLocaleString('fr-FR');
    
    // Update health status
    const healthStatus = document.getElementById('health-status');
    healthStatus.className = `px-4 sm:px-6 py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg flex items-center justify-between ${getHealthColor(health.status)}`;
    healthStatus.innerHTML = `
        <span>État du système: <span class="uppercase">${health.status}</span></span>
        <span class="text-xl sm:text-2xl">${health.status === 'healthy' ? '\u2705' : health.status === 'degraded' ? '\u26a0\ufe0f' : '\u274c'}</span>
    `;
    
    // Update health checks
    const healthChecks = document.getElementById('health-checks');
    healthChecks.innerHTML = Object.entries(health.checks).map(([name, check]) => `
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-slate-700 dark:text-slate-300 capitalize text-sm sm:text-base">${name}</h3>
                <span class="px-2 py-1 text-xs rounded ${  
                    check.status === 'ok' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                    check.status === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' :
                    'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                }">
                    ${check.status}
                </span>
            </div>
            ${check.usage_percent ? `
                <div class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    <p>Utilisation: ${check.usage_percent}%</p>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-1">
                        <div class="h-1.5 rounded-full transition-all ${check.usage_percent > 90 ? 'bg-red-500' : check.usage_percent > 70 ? 'bg-yellow-500' : 'bg-green-500'}" style="width: ${check.usage_percent}%"></div>
                    </div>
                    <p class="mt-1">Libre: ${check.free_gb} GB</p>
                </div>
            ` : ''}
            ${check.error ? `<p class="mt-2 text-xs text-red-600 dark:text-red-400">${check.error}</p>` : ''}
        </div>
    `).join('');
    
    // Update database stats
    document.getElementById('db-stats').innerHTML = `
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Utilisateurs:</span>
            <span class="font-bold dark:text-slate-100">${formatNumber(stats.database.total_users)}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Annonces actives:</span>
            <span class="font-bold dark:text-slate-100">${formatNumber(stats.database.active_items)}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Commandes en attente:</span>
            <span class="font-bold text-orange-600">${formatNumber(stats.database.pending_orders)}</span>
        </div>
    `;
    
    // Update revenue
    const revenueTodayElement = document.getElementById('revenue-today');
    const revenueData = stats.database.revenue_today;
    
    if (typeof revenueData === 'object' && Object.keys(revenueData).length > 0) {
        revenueTodayElement.innerHTML = Object.entries(revenueData).map(([currency, amount]) => `
            <div class="flex justify-between items-baseline gap-2">
                <span class="text-xs sm:text-sm opacity-80">${currency}:</span>
                <span class="text-base sm:text-2xl font-bold truncate">${formatCurrency(amount, currency)}</span>
            </div>
        `).join('');
    } else {
        // Aucun revenu aujourd'hui
        revenueTodayElement.innerHTML = '<div class="text-lg sm:text-2xl font-bold">Aucun revenu</div>';
    }
    
    document.getElementById('orders-today').textContent = `${formatNumber(stats.database.total_orders_today)} commandes`;
    
    // Update cache stats
    document.getElementById('cache-stats').innerHTML = `
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Taux de succès:</span>
            <span class="font-bold text-green-600">${stats.cache.hit_rate}%</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Hits:</span>
            <span class="font-medium dark:text-slate-100">${formatNumber(stats.cache.hits)}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Misses:</span>
            <span class="font-medium dark:text-slate-100">${formatNumber(stats.cache.misses)}</span>
        </div>
    `;
    
    // Update performance stats
    document.getElementById('performance-stats').innerHTML = `
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Temps moyen:</span>
            <span class="font-bold dark:text-slate-100">${stats.performance.avg_response_time}ms</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Opérations lentes:</span>
            <span class="font-medium ${stats.performance.slow_operations > 0 ? 'text-red-600' : 'text-green-600'}">
                ${stats.performance.slow_operations}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Total:</span>
            <span class="font-medium dark:text-slate-100">${formatNumber(stats.performance.total_operations)}</span>
        </div>
    `;
    
    // Update business events
    document.getElementById('business-events').innerHTML = `
        <div class="flex justify-between items-center">
            <span class="text-slate-600 dark:text-slate-400">Total événements:</span>
            <span class="font-bold dark:text-slate-100">${formatNumber(stats.business.total_events)}</span>
        </div>
        ${stats.business.last_event ? `
            <div class="text-sm text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-700 p-3 rounded">
                <p class="font-medium text-slate-800 dark:text-slate-200">Dernier événement:</p>
                <p class="mt-1">${stats.business.last_event.event}</p>
                <p class="text-xs mt-1">${new Date(stats.business.last_event.timestamp).toLocaleString('fr-FR')}</p>
            </div>
        ` : ''}
    `;
    
    // Update errors
    document.getElementById('errors-section').innerHTML = `
        <div class="flex justify-between items-center">
            <span class="text-slate-600 dark:text-slate-400">Total erreurs:</span>
            <span class="font-bold ${stats.errors.total_errors > 0 ? 'text-red-600' : 'text-green-600'}">
                ${stats.errors.total_errors}
            </span>
        </div>
        ${stats.errors.last_error ? `
            <div class="text-sm text-slate-600 bg-red-50 dark:bg-red-900/20 p-3 rounded border border-red-200 dark:border-red-800">
                <p class="font-medium text-red-800 dark:text-red-400">Dernière erreur:</p>
                <p class="mt-1 text-red-700 dark:text-red-300">${stats.errors.last_error.message}</p>
                <p class="text-xs mt-1 text-red-600 dark:text-red-400">
                    ${stats.errors.last_error.file}:${stats.errors.last_error.line}
                </p>
                <p class="text-xs mt-1">${new Date(stats.errors.last_error.timestamp).toLocaleString('fr-FR')}</p>
            </div>
        ` : `
            <div class="text-sm text-green-600 bg-green-50 dark:bg-green-900/20 p-3 rounded">
                ✅ Aucune erreur récente
            </div>
        `}
    `;

    // Temps réel : graphiques + cartes live
    window.lastData = data;
    renderCharts(data);
    renderRealtimeStats(stats);
}

// Toggle auto-refresh
document.getElementById('toggle-refresh').addEventListener('click', function() {
    autoRefresh = !autoRefresh;
    this.innerHTML = autoRefresh 
        ? '\ud83d\udd04 <span class="hidden sm:inline">Auto-refresh</span> ON' 
        : '\u23f8\ufe0f <span class="hidden sm:inline">Auto-refresh</span> OFF';
    
    if (autoRefresh) {
        this.classList.remove('bg-slate-300', 'dark:bg-slate-600', 'text-slate-700', 'dark:text-slate-300');
        this.classList.add('bg-green-600', 'text-white');
    } else {
        this.classList.remove('bg-green-600', 'text-white');
        this.classList.add('bg-slate-300', 'dark:bg-slate-600', 'text-slate-700', 'dark:text-slate-300');
    }
});

// Initial load
refreshStats();

// Auto-refresh every 5 seconds (fallback si Pusher indisponible)
refreshInterval = setInterval(() => {
    if (autoRefresh) {
        refreshStats();
    }
}, 5000);

/* ==================== Graphiques temps réel ==================== */

let charts = {};

function buildTimeSeries(points, valueKey) {
    const labels = (points || []).map(p => {
        const d = new Date(p.time);
        return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    });
    let values = (points || []).map(p => {
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

    const { labels, values } = buildTimeSeries(points, valueKey);

    // Détruire l'ancien chart
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
                backgroundColor: color + '22',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false },
                y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.15)' } }
            }
        }
    });
    return charts[canvasId];
}

function renderCharts(data) {
    if (!data) return;
    const series = data.stats.series || data.series || {};
    makeChart('revenue-chart', 'Revenus du jour', '#10b981', series.revenue);
    makeChart('performance-chart', 'Temps moyen (ms)', '#6366f1', series.performance);
    makeChart('users-chart', 'Utilisateurs connectés', '#3b82f6', series.users);
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
            loadFill.className = 'h-1.5 rounded-full transition-all ' +
                (load > 70 ? 'bg-red-500' : load > 40 ? 'bg-yellow-500' : 'bg-green-500');
        }
    } else if (systemLoad) {
        systemLoad.textContent = 'N/A';
    }
}

/* ==================== Connexion temps réel (Pusher) ==================== */

function setLiveConnection(state, label) {
    const pill = document.getElementById('live-connection');
    const text = document.getElementById('live-connection-text');
    if (!pill) return;

    pill.classList.remove('hidden');
    const dot = pill.querySelector('span');
    if (state === 'live') {
        dot.className = 'inline-block w-2 h-2 rounded-full bg-green-500 animate-pulse';
        pill.className = 'px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium flex items-center gap-2 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300';
    } else if (state === 'polling') {
        dot.className = 'inline-block w-2 h-2 rounded-full bg-yellow-500 animate-pulse';
        pill.className = 'px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium flex items-center gap-2 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300';
    } else {
        dot.className = 'inline-block w-2 h-2 rounded-full bg-red-500';
        pill.className = 'px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium flex items-center gap-2 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300';
    }
    text.textContent = label;
}

function initRealtime() {
    setLiveConnection('disconnected', 'Mode secours');

    // Démarrer les graphiques vides, puis tenter la connexion Pusher
    try {
        @if($pusher['enabled'])
        if (window.Echo && typeof window.Echo !== 'undefined') {
            setLiveConnection('polling', 'Connexion temps réel...');
            window.Echo.channel('monitoring.updates')
                .listen('.monitoring.updated', (event) => {
                    setLiveConnection('live', 'Temps réel (WebSocket)');
                    updateDashboard({ stats: event.stats, health: event.health, timestamp: event.timestamp });
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

// Initialise la connexion temps réel au chargement
initRealtime();

// Rend les graphiques initiaux (les séries viennent de refreshStats initial)
setTimeout(() => {
    try {
        renderCharts(lastData);
    } catch (e) { /* ignorer si pas encore de données */ }
}, 800);
</script>
@endsection
