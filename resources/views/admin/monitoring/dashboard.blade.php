@extends('layouts.admin')

@section('content')
<div class="py-6" id="monitoring-app">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">📈 Monitoring & Métriques</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Dernière mise à jour: <span id="last-update">{{ now()->format('d/m/Y H:i:s') }}</span>
                </p>
            </div>
            
            <div class="flex gap-3">
                <button
                    id="toggle-refresh"
                    class="px-4 py-2 rounded-lg font-medium transition bg-green-600 text-white hover:bg-green-700"
                >
                    🔄 Auto-refresh ON
                </button>
                
                <button
                    onclick="refreshStats()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    🔄 Actualiser
                </button>
            </div>
        </div>

        <!-- Health Status -->
        <div class="mb-6">
            <div id="health-status" class="px-6 py-4 rounded-lg font-semibold text-lg flex items-center justify-between">
                État du système: <span class="uppercase">Chargement...</span>
            </div>
            
            <!-- Health Checks Details -->
            <div id="health-checks" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <!-- Sera rempli par JavaScript -->
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Database Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Base de données</h3>
                    <span class="text-3xl">🗄️</span>
                </div>
                <div id="db-stats" class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Revenue Today -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Revenus Aujourd'hui</h3>
                    <span class="text-3xl">💰</span>
                </div>
                <div id="revenue-today" class="space-y-2">
                    <div class="text-2xl font-bold">Chargement...</div>
                </div>
                <p id="orders-today" class="text-sm mt-3 opacity-90">
                    0 commandes
                </p>
            </div>

            <!-- Cache Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Cache</h3>
                    <span class="text-3xl">⚡</span>
                </div>
                <div id="cache-stats" class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Performance Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Performance</h3>
                    <span class="text-3xl">📊</span>
                </div>
                <div id="performance-stats" class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Events & Errors -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Business Events -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                    <span class="mr-2">📈</span>
                    Événements Business
                </h3>
                <div id="business-events" class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Errors -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                    <span class="mr-2">🚨</span>
                    Erreurs
                </h3>
                <div id="errors-section" class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Actions Rapides</h3>
            <div class="flex flex-wrap gap-3">
                <a href="/telescope" target="_blank" 
                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    🔭 Ouvrir Telescope
                </a>
                <form action="/admin/monitoring/reset" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        🔄 Réinitialiser métriques
                    </button>
                </form>
                <a href="/admin/monitoring/health" target="_blank"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
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
        healthy: 'text-green-600 bg-green-100',
        degraded: 'text-yellow-600 bg-yellow-100',
        unhealthy: 'text-red-600 bg-red-100',
    };
    return colors[status] || 'text-gray-600 bg-gray-100';
}

async function refreshStats() {
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
        document.getElementById('toggle-refresh').textContent = '⏸️ Auto-refresh OFF';
        document.getElementById('toggle-refresh').classList.remove('bg-green-600');
        document.getElementById('toggle-refresh').classList.add('bg-gray-300', 'text-gray-700');
    }
}

function updateDashboard(data) {
    const { stats, health, timestamp } = data;
    
    // Update timestamp
    document.getElementById('last-update').textContent = new Date(timestamp).toLocaleString('fr-FR');
    
    // Update health status
    const healthStatus = document.getElementById('health-status');
    healthStatus.className = `px-6 py-4 rounded-lg font-semibold text-lg flex items-center justify-between ${getHealthColor(health.status)}`;
    healthStatus.innerHTML = `
        <span>État du système: <span class="uppercase">${health.status}</span></span>
        <span class="text-2xl">${health.status === 'healthy' ? '✅' : health.status === 'degraded' ? '⚠️' : '❌'}</span>
    `;
    
    // Update health checks
    const healthChecks = document.getElementById('health-checks');
    healthChecks.innerHTML = Object.entries(health.checks).map(([name, check]) => `
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-gray-700 dark:text-gray-300 capitalize">${name}</h3>
                <span class="px-2 py-1 text-xs rounded ${
                    check.status === 'ok' ? 'bg-green-100 text-green-700' :
                    check.status === 'warning' ? 'bg-yellow-100 text-yellow-700' :
                    'bg-red-100 text-red-700'
                }">
                    ${check.status}
                </span>
            </div>
            ${check.usage_percent ? `
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <p>Utilisation: ${check.usage_percent}%</p>
                    <p>Espace libre: ${check.free_gb} GB</p>
                </div>
            ` : ''}
            ${check.error ? `<p class="mt-2 text-xs text-red-600">${check.error}</p>` : ''}
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
        // Afficher les revenus par devise
        revenueTodayElement.innerHTML = Object.entries(revenueData).map(([currency, amount]) => `
            <div class="flex justify-between items-baseline">
                <span class="text-sm opacity-80">${currency}:</span>
                <span class="text-2xl font-bold">${formatCurrency(amount, currency)}</span>
            </div>
        `).join('');
    } else {
        // Aucun revenu aujourd'hui
        revenueTodayElement.innerHTML = '<div class="text-2xl font-bold">Aucun revenu</div>';
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
    this.textContent = autoRefresh ? '🔄 Auto-refresh ON' : '⏸️ Auto-refresh OFF';
    
    if (autoRefresh) {
        this.classList.remove('bg-gray-300', 'text-gray-700');
        this.classList.add('bg-green-600', 'text-white');
    } else {
        this.classList.remove('bg-green-600', 'text-white');
        this.classList.add('bg-gray-300', 'text-gray-700');
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
