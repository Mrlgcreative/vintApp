<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    health: Object,
    timestamp: String,
});

const currentStats = ref(props.stats);
const currentHealth = ref(props.health);
const lastUpdate = ref(props.timestamp);
const autoRefresh = ref(true);
let refreshInterval = null;

// Rafraîchir les stats toutes les 5 secondes
const fetchStats = async () => {
    if (!autoRefresh.value) return;

    try {
        const response = await fetch('/admin/monitoring/stats', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin' // Important pour inclure les cookies de session
        });
        
        if (!response.ok) {
            if (response.status === 401 || response.status === 419) {
                // Session expirée, recharger la page pour rediriger vers login
                window.location.reload();
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.error('Response is not JSON, got:', contentType);
            // Probablement redirigé vers login, recharger la page
            window.location.reload();
            return;
        }
        
        const data = await response.json();
        
        currentStats.value = data.stats;
        currentHealth.value = data.health;
        lastUpdate.value = data.timestamp;
    } catch (error) {
        console.error('Erreur lors du rafraîchissement:', error);
        // Désactiver auto-refresh en cas d'erreur répétée
        autoRefresh.value = false;
    }
};

onMounted(() => {
    refreshInterval = setInterval(fetchStats, 5000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

const toggleAutoRefresh = () => {
    autoRefresh.value = !autoRefresh.value;
};

const getHealthColor = (status) => {
    const colors = {
        healthy: 'text-green-600 bg-green-100',
        degraded: 'text-yellow-600 bg-yellow-100',
        unhealthy: 'text-red-600 bg-red-100',
    };
    return colors[status] || 'text-gray-600 bg-gray-100';
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('fr-FR').format(num);
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
    }).format(amount);
};
</script>

<template>
    <Head title="Monitoring - Dashboard" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Monitoring & Métriques</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Dernière mise à jour: {{ new Date(lastUpdate).toLocaleString('fr-FR') }}
                        </p>
                    </div>
                    
                    <div class="flex gap-3">
                        <button
                            @click="toggleAutoRefresh"
                            :class="[
                                'px-4 py-2 rounded-lg font-medium transition',
                                autoRefresh 
                                    ? 'bg-green-600 text-white hover:bg-green-700' 
                                    : 'bg-gray-300 text-gray-700 hover:bg-gray-400'
                            ]"
                        >
                            {{ autoRefresh ? '🔄 Auto-refresh ON' : '⏸️ Auto-refresh OFF' }}
                        </button>
                        
                        <button
                            @click="fetchStats"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        >
                            🔄 Actualiser
                        </button>
                    </div>
                </div>

                <!-- Health Status -->
                <div class="mb-6">
                    <div :class="[
                        'px-6 py-4 rounded-lg font-semibold text-lg flex items-center justify-between',
                        getHealthColor(currentHealth.status)
                    ]">
                        <span>
                            État du système: 
                            <span class="uppercase">{{ currentHealth.status }}</span>
                        </span>
                        <span class="text-2xl">
                            {{ currentHealth.status === 'healthy' ? '✅' : 
                               currentHealth.status === 'degraded' ? '⚠️' : '❌' }}
                        </span>
                    </div>
                    
                    <!-- Health Checks Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div v-for="(check, name) in currentHealth.checks" :key="name"
                             class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium text-gray-700 capitalize">{{ name }}</h3>
                                <span :class="[
                                    'px-2 py-1 text-xs rounded',
                                    check.status === 'ok' ? 'bg-green-100 text-green-700' :
                                    check.status === 'warning' ? 'bg-yellow-100 text-yellow-700' :
                                    'bg-red-100 text-red-700'
                                ]">
                                    {{ check.status }}
                                </span>
                            </div>
                            <div v-if="check.usage_percent" class="mt-2 text-sm text-gray-600">
                                <p>Utilisation: {{ check.usage_percent }}%</p>
                                <p>Espace libre: {{ check.free_gb }} GB</p>
                            </div>
                            <p v-if="check.error" class="mt-2 text-xs text-red-600">
                                {{ check.error }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Database Stats -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Base de données</h3>
                            <span class="text-3xl">🗄️</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Utilisateurs:</span>
                                <span class="font-bold">{{ formatNumber(currentStats.database.total_users) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Annonces actives:</span>
                                <span class="font-bold">{{ formatNumber(currentStats.database.active_items) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Commandes en attente:</span>
                                <span class="font-bold text-orange-600">{{ formatNumber(currentStats.database.pending_orders) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Today -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Revenus Aujourd'hui</h3>
                            <span class="text-3xl">💰</span>
                        </div>
                        <div class="text-3xl font-bold">
                            {{ formatCurrency(currentStats.database.revenue_today) }}
                        </div>
                        <p class="text-sm mt-2 opacity-90">
                            {{ formatNumber(currentStats.database.total_orders_today) }} commandes
                        </p>
                    </div>

                    <!-- Cache Stats -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Cache</h3>
                            <span class="text-3xl">⚡</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Taux de succès:</span>
                                <span class="font-bold text-green-600">{{ currentStats.cache.hit_rate }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Hits:</span>
                                <span class="font-medium">{{ formatNumber(currentStats.cache.hits) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Misses:</span>
                                <span class="font-medium">{{ formatNumber(currentStats.cache.misses) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Stats -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Performance</h3>
                            <span class="text-3xl">📊</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Temps moyen:</span>
                                <span class="font-bold">{{ currentStats.performance.avg_response_time }}ms</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Opérations lentes:</span>
                                <span :class="[
                                    'font-medium',
                                    currentStats.performance.slow_operations > 0 ? 'text-red-600' : 'text-green-600'
                                ]">
                                    {{ currentStats.performance.slow_operations }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-medium">{{ formatNumber(currentStats.performance.total_operations) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Events & Errors -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Business Events -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="mr-2">📈</span>
                            Événements Business
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total événements:</span>
                                <span class="font-bold">{{ formatNumber(currentStats.business.total_events) }}</span>
                            </div>
                            <div v-if="currentStats.business.last_event" class="text-sm text-gray-600 bg-gray-50 p-3 rounded">
                                <p class="font-medium text-gray-800">Dernier événement:</p>
                                <p class="mt-1">{{ currentStats.business.last_event.event }}</p>
                                <p class="text-xs mt-1">{{ new Date(currentStats.business.last_event.timestamp).toLocaleString('fr-FR') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Errors -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="mr-2">🚨</span>
                            Erreurs
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total erreurs:</span>
                                <span :class="[
                                    'font-bold',
                                    currentStats.errors.total_errors > 0 ? 'text-red-600' : 'text-green-600'
                                ]">
                                    {{ currentStats.errors.total_errors }}
                                </span>
                            </div>
                            <div v-if="currentStats.errors.last_error" class="text-sm text-gray-600 bg-red-50 p-3 rounded border border-red-200">
                                <p class="font-medium text-red-800">Dernière erreur:</p>
                                <p class="mt-1 text-red-700">{{ currentStats.errors.last_error.message }}</p>
                                <p class="text-xs mt-1 text-red-600">
                                    {{ currentStats.errors.last_error.file }}:{{ currentStats.errors.last_error.line }}
                                </p>
                                <p class="text-xs mt-1">{{ new Date(currentStats.errors.last_error.timestamp).toLocaleString('fr-FR') }}</p>
                            </div>
                            <div v-else class="text-sm text-green-600 bg-green-50 p-3 rounded">
                                ✅ Aucune erreur récente
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions Rapides</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="/telescope" target="_blank" 
                           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            🔭 Ouvrir Telescope
                        </a>
                        <button
                            @click="$inertia.post('/admin/monitoring/reset')"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                        >
                            🔄 Réinitialiser métriques
                        </button>
                        <a href="/admin/monitoring/health" target="_blank"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            🏥 Health Check API
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
