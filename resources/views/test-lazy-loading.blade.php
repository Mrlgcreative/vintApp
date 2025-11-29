@extends('app')

@section('title', 'Test Lazy Loading - VintApp')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-primary-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-rocket text-primary-600 mr-3"></i>
                Test du Lazy Loading
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                Cette page démontre le système de lazy loading optimisé pour VintApp PWA
            </p>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-images text-2xl text-blue-600"></i>
                    <span class="text-sm text-gray-500">Images</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white" id="totalImages">0</div>
                <div class="text-sm text-gray-500 mt-1">Total</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    <span class="text-sm text-gray-500">Chargées</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white" id="loadedImages">0</div>
                <div class="text-sm text-gray-500 mt-1">Images chargées</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-hourglass-half text-2xl text-amber-600"></i>
                    <span class="text-sm text-gray-500">En attente</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white" id="pendingImages">0</div>
                <div class="text-sm text-gray-500 mt-1">À charger</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-tachometer-alt text-2xl text-purple-600"></i>
                    <span class="text-sm text-gray-500">Performance</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white" id="loadTime">0s</div>
                <div class="text-sm text-gray-500 mt-1">Temps de chargement</div>
            </div>
        </div>

        <!-- Section 1: Images simples -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-image text-primary-600 mr-2"></i>
                Images simples avec lazy loading
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @for($i = 1; $i <= 12; $i++)
                    <div class="lazy-container aspect-ratio-1-1">
                        <img data-src="https://picsum.photos/400/400?random={{ $i }}"
                             src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400'%3E%3Crect fill='%23e5e7eb' width='400' height='400'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' fill='%239ca3af' font-size='20'%3EImage {{ $i }}%3C/text%3E%3C/svg%3E"
                             loading="lazy"
                             alt="Image test {{ $i }}"
                             class="w-full h-full object-cover rounded-lg test-image">
                    </div>
                @endfor
            </div>
        </div>

        <!-- Section 2: Progressive loading -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-layer-group text-primary-600 mr-2"></i>
                Progressive Image Loading
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for($i = 13; $i <= 15; $i++)
                    <div class="progressive-image rounded-lg overflow-hidden">
                        <img src="https://picsum.photos/50/50?random={{ $i }}&blur=10"
                             class="progressive-image__placeholder">
                        <img data-src="https://picsum.photos/600/400?random={{ $i }}"
                             loading="lazy"
                             alt="Progressive {{ $i }}"
                             class="progressive-image__full w-full h-64 object-cover test-image">
                    </div>
                @endfor
            </div>
        </div>

        <!-- Section 3: Background images -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-paint-brush text-primary-600 mr-2"></i>
                Images de fond (background)
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @for($i = 16; $i <= 19; $i++)
                    <div data-bg="https://picsum.photos/800/400?random={{ $i }}"
                         class="lazy-container h-48 bg-cover bg-center rounded-lg flex items-center justify-center text-white font-bold text-2xl shadow-inner">
                        <span class="bg-black/50 px-4 py-2 rounded">Banner {{ $i - 15 }}</span>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Section 4: Skeleton loaders -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-spinner text-primary-600 mr-2"></i>
                Skeleton Loaders
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for($i = 1; $i <= 3; $i++)
                    <div class="skeleton-card">
                        <div class="skeleton-loader skeleton-image mb-4"></div>
                        <div class="skeleton-loader skeleton-title mb-2"></div>
                        <div class="skeleton-loader skeleton-text"></div>
                        <div class="skeleton-loader skeleton-text w-3/4 mb-4"></div>
                        <div class="flex items-center gap-4">
                            <div class="skeleton-loader skeleton-avatar"></div>
                            <div class="flex-1">
                                <div class="skeleton-loader skeleton-text mb-2"></div>
                                <div class="skeleton-loader skeleton-text w-1/2"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Section 5: Différents ratios -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-expand text-primary-600 mr-2"></i>
                Aspect Ratios
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">16:9 (Vidéo)</h3>
                    <div class="lazy-container aspect-ratio-16-9">
                        <img data-src="https://picsum.photos/1920/1080?random=20"
                             src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='9'%3E%3Crect fill='%23e5e7eb' width='16' height='9'/%3E%3C/svg%3E"
                             loading="lazy"
                             alt="16:9"
                             class="rounded-lg test-image">
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">4:3 (Photo)</h3>
                    <div class="lazy-container aspect-ratio-4-3">
                        <img data-src="https://picsum.photos/800/600?random=21"
                             src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='3'%3E%3Crect fill='%23e5e7eb' width='4' height='3'/%3E%3C/svg%3E"
                             loading="lazy"
                             alt="4:3"
                             class="rounded-lg test-image">
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">1:1 (Carré)</h3>
                    <div class="lazy-container aspect-ratio-1-1">
                        <img data-src="https://picsum.photos/600/600?random=22"
                             src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1' height='1'%3E%3Crect fill='%23e5e7eb' width='1' height='1'/%3E%3C/svg%3E"
                             loading="lazy"
                             alt="1:1"
                             class="rounded-lg test-image">
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-cog text-primary-600 mr-2"></i>
                Actions
            </h2>
            <div class="flex flex-wrap gap-4">
                <button onclick="loadAllImages()" 
                        class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Charger toutes les images
                </button>
                <button onclick="reloadPage()" 
                        class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Recharger la page
                </button>
                <button onclick="scrollToBottom()" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-arrow-down mr-2"></i>
                    Scroll vers le bas
                </button>
            </div>
        </div>

        <!-- Console de logs -->
        <div class="bg-gray-900 rounded-xl p-6 shadow-lg">
            <h2 class="text-2xl font-bold text-white mb-4">
                <i class="fas fa-terminal text-green-400 mr-2"></i>
                Console de logs
            </h2>
            <div id="logConsole" class="bg-black rounded p-4 h-64 overflow-y-auto font-mono text-sm text-green-400">
                <div class="log-entry">🚀 Système de lazy loading initialisé...</div>
            </div>
        </div>
    </div>
</div>

<script>
let startTime = performance.now();
let loadedCount = 0;
let totalCount = 0;

function updateStats() {
    const images = document.querySelectorAll('.test-image');
    totalCount = images.length;
    loadedCount = document.querySelectorAll('.test-image.lazy-loaded').length;
    
    document.getElementById('totalImages').textContent = totalCount;
    document.getElementById('loadedImages').textContent = loadedCount;
    document.getElementById('pendingImages').textContent = totalCount - loadedCount;
    
    const elapsed = ((performance.now() - startTime) / 1000).toFixed(2);
    document.getElementById('loadTime').textContent = elapsed + 's';
}

function addLog(message, type = 'info') {
    const console = document.getElementById('logConsole');
    const time = new Date().toLocaleTimeString();
    const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
    const color = type === 'success' ? 'text-green-400' : type === 'error' ? 'text-red-400' : 'text-blue-400';
    
    const entry = document.createElement('div');
    entry.className = `log-entry ${color} mb-1`;
    entry.textContent = `${icon} [${time}] ${message}`;
    console.appendChild(entry);
    console.scrollTop = console.scrollHeight;
}

// Écouter les événements de lazy loading
document.addEventListener('lazyloaded', function(e) {
    const element = e.target;
    if (element.classList.contains('test-image')) {
        loadedCount++;
        updateStats();
        
        if (element.dataset.src) {
            addLog(`Image chargée: ${element.alt || element.dataset.src.substring(0, 50)}`, 'success');
        } else if (element.parentElement.dataset.bg) {
            addLog(`Background chargé: ${element.parentElement.dataset.bg.substring(0, 50)}`, 'success');
        }
    }
});

// Actions
function loadAllImages() {
    addLog('Forçage du chargement de toutes les images...', 'info');
    if (window.lazyLoader) {
        window.lazyLoader.loadAll();
        addLog('Toutes les images ont été chargées!', 'success');
    }
}

function reloadPage() {
    location.reload();
}

function scrollToBottom() {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
    addLog('Scroll automatique vers le bas...', 'info');
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
    setInterval(updateStats, 500);
    
    addLog('Page chargée! Scrollez pour voir les images se charger.', 'success');
    
    // Log quand le lazy loader est prêt
    if (window.lazyLoader) {
        addLog('LazyLoadingManager initialisé avec succès', 'success');
        addLog(`Configuration: rootMargin=${window.lazyLoader.options.rootMargin}, threshold=${window.lazyLoader.options.threshold}`, 'info');
    }
});

// Performance monitoring
if ('PerformanceObserver' in window) {
    const perfObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (entry.entryType === 'resource' && entry.name.includes('picsum.photos')) {
                addLog(`Image téléchargée en ${entry.duration.toFixed(0)}ms`, 'info');
            }
        }
    });
    perfObserver.observe({ entryTypes: ['resource'] });
}
</script>
@endsection
