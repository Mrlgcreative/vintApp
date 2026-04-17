@extends('layouts.admin')

@section('content')
<div class="py-6" id="monitoring-app">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">📈 Monitoring & Métriques</h1>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Dernière mise à jour: <span id="last-update">{{ now()->format('d/m/Y H:i:s') }}</span>
                    <span id="refresh-indicator" class="ml-2 hidden">
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    </span>
                </p>
            </div>
            
            <div class="flex gap-2 sm:gap-3">
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
            <div id="health-status" class="px-4 sm:px-6 py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg flex items-center justify-between bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 animate-pulse">
                État du système: <span class="uppercase">Chargement...</span>
            </div>
            
            <!-- Health Checks Details -->
            <div id="health-checks" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mt-4">
                <!-- Skeleton placeholders -->
                @for($i = 0; $i < 3; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 animate-pulse">
                    <div class="flex items-center justify-between">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                        <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-12"></div>
                    </div>
                    <div class="mt-3 space-y-2">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-28"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6">
            <!-- Database Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-gray-800 dark:text-gray-100">Base de données</h3>
                    <span class="text-xl sm:text-3xl">🗄️</span>
                </div>
                <div id="db-stats" class="space-y-1.5 sm:space-y-2 text-xs sm:text-base">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
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
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-gray-800 dark:text-gray-100">Cache</h3>
                    <span class="text-xl sm:text-3xl">⚡</span>
                </div>
                <div id="cache-stats" class="space-y-1.5 sm:space-y-2 text-xs sm:text-base">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Performance Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-3 sm:p-6">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-sm sm:text-lg font-semibold text-gray-800 dark:text-gray-100">Performance</h3>
                    <span class="text-xl sm:text-3xl">📊</span>
                </div>
                <div id="performance-stats" class="space-y-1.5 sm:space-y-2 text-xs sm:text-base">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Events & Errors -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-6">
            <!-- Business Events -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3 sm:mb-4 flex items-center">
                    <span class="mr-2">📈</span>
                    Événements Business
                </h3>
                <div id="business-events" class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Errors -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3 sm:mb-4 flex items-center">
                    <span class="mr-2">🚨</span>
                    Erreurs
                </h3>
                <div id="errors-section" class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-4 sm:mt-6 bg-gray-50 dark:bg-gray-800 rounded-lg p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3 sm:mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 sm:flex sm:flex-wrap gap-2 sm:gap-3">
                <a href="/telescope" target="_blank" 
                   class="px-4 py-2.5 sm:py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm sm:text-base text-center">
                    🔭 Ouvrir Telescope
                </a>
                <form action="/admin/monitoring/reset" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 sm:py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm sm:text-base">
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
    return colors[status] || 'text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700';
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
        toggleBtn.classList.add('bg-gray-300', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-gray-700 dark:text-gray-300 capitalize text-sm sm:text-base">${name}</h3>
                <span class="px-2 py-1 text-xs rounded ${  
                    check.status === 'ok' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                    check.status === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' :
                    'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                }">
                    ${check.status}
                </span>
            </div>
            ${check.usage_percent ? `
                <div class="mt-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                    <p>Utilisation: ${check.usage_percent}%</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-1">
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
            <span class="text-gray-600 dark:text-gray-400">Utilisateurs:</span>
            <span class="font-bold dark:text-gray-100">${formatNumber(stats.database.total_users)}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Annonces actives:</span>
            <span class="font-bold dark:text-gray-100">${formatNumber(stats.database.active_items)}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Commandes en attente:</span>
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
            <span class="text-gray-600 dark:text-gray-400">Taux de succès:</span>
            <span class="font-bold text-green-600">${stats.cache.hit_rate}%</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Hits:</span>
            <span class="font-medium dark:text-gray-100">${formatNumber(stats.cache.hits)}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Misses:</span>
            <span class="font-medium dark:text-gray-100">${formatNumber(stats.cache.misses)}</span>
        </div>
    `;
    
    // Update performance stats
    document.getElementById('performance-stats').innerHTML = `
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Temps moyen:</span>
            <span class="font-bold dark:text-gray-100">${stats.performance.avg_response_time}ms</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Opérations lentes:</span>
            <span class="font-medium ${stats.performance.slow_operations > 0 ? 'text-red-600' : 'text-green-600'}">
                ${stats.performance.slow_operations}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Total:</span>
            <span class="font-medium dark:text-gray-100">${formatNumber(stats.performance.total_operations)}</span>
        </div>
    `;
    
    // Update business events
    document.getElementById('business-events').innerHTML = `
        <div class="flex justify-between items-center">
            <span class="text-gray-600 dark:text-gray-400">Total événements:</span>
            <span class="font-bold dark:text-gray-100">${formatNumber(stats.business.total_events)}</span>
        </div>
        ${stats.business.last_event ? `
            <div class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                <p class="font-medium text-gray-800 dark:text-gray-200">Dernier événement:</p>
                <p class="mt-1">${stats.business.last_event.event}</p>
                <p class="text-xs mt-1">${new Date(stats.business.last_event.timestamp).toLocaleString('fr-FR')}</p>
            </div>
        ` : ''}
    `;
    
    // Update errors
    document.getElementById('errors-section').innerHTML = `
        <div class="flex justify-between items-center">
            <span class="text-gray-600 dark:text-gray-400">Total erreurs:</span>
            <span class="font-bold ${stats.errors.total_errors > 0 ? 'text-red-600' : 'text-green-600'}">
                ${stats.errors.total_errors}
            </span>
        </div>
        ${stats.errors.last_error ? `
            <div class="text-sm text-gray-600 bg-red-50 dark:bg-red-900/20 p-3 rounded border border-red-200 dark:border-red-800">
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
}

// Toggle auto-refresh
document.getElementById('toggle-refresh').addEventListener('click', function() {
    autoRefresh = !autoRefresh;
    this.innerHTML = autoRefresh 
        ? '\ud83d\udd04 <span class="hidden sm:inline">Auto-refresh</span> ON' 
        : '\u23f8\ufe0f <span class="hidden sm:inline">Auto-refresh</span> OFF';
    
    if (autoRefresh) {
        this.classList.remove('bg-gray-300', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
        this.classList.add('bg-green-600', 'text-white');
    } else {
        this.classList.remove('bg-green-600', 'text-white');
        this.classList.add('bg-gray-300', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-300');
    }
});

// Initial load
refreshStats();

// Auto-refresh every 5 seconds
refreshInterval = setInterval(() => {
    if (autoRefresh) {
        refreshStats();
    }
}, 5000);
</script>
@endsection
