@extends('app')

@section('title', 'Test Navigation Skeleton')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                🔄 Test Navigation Skeleton
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                Cliquez sur les liens ci-dessous pour voir le skeleton en action
            </p>
            <div class="mt-4 inline-flex items-center px-4 py-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                <i class="fas fa-info-circle text-purple-600 dark:text-purple-400 mr-2"></i>
                <span class="text-sm text-purple-800 dark:text-purple-300">
                    Chaque lien affiche un skeleton différent selon le type de page
                </span>
            </div>
        </div>

        <!-- Grid de tests -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            
            <!-- Test 1: Product Grid -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border-2 border-purple-200 dark:border-purple-800 hover:border-purple-400 dark:hover:border-purple-600 transition-all duration-300">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white">
                    <i class="fas fa-th-large text-3xl mb-2"></i>
                    <h3 class="text-xl font-bold">Product Grid</h3>
                    <p class="text-sm opacity-90">Grille de produits</p>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Affiche une grille de 12 cards produits avec images et prix
                    </p>
                    <a href="{{ route('items.index') }}" 
                       class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                        <i class="fas fa-arrow-right mr-2"></i>Tester
                    </a>
                    <code class="block mt-2 text-xs text-gray-500 dark:text-gray-400">
                        /items
                    </code>
                </div>
            </div>

            <!-- Test 2: Product Detail -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border-2 border-blue-200 dark:border-blue-800 hover:border-blue-400 dark:hover:border-blue-600 transition-all duration-300">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                    <i class="fas fa-image text-3xl mb-2"></i>
                    <h3 class="text-xl font-bold">Product Detail</h3>
                    <p class="text-sm opacity-90">Détail produit</p>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Affiche galerie d'images + informations du produit
                    </p>
                    @php
                        $firstItem = \App\Models\Item::first();
                    @endphp
                    @if($firstItem)
                        <a href="{{ route('items.show', $firstItem) }}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                            <i class="fas fa-arrow-right mr-2"></i>Tester
                        </a>
                        <code class="block mt-2 text-xs text-gray-500 dark:text-gray-400">
                            /items/{{ $firstItem->id }}
                        </code>
                    @else
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Aucun produit disponible
                        </div>
                    @endif
                </div>
            </div>

            <!-- Test 3: Dashboard -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border-2 border-green-200 dark:border-green-800 hover:border-green-400 dark:hover:border-green-600 transition-all duration-300">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
                    <i class="fas fa-chart-line text-3xl mb-2"></i>
                    <h3 class="text-xl font-bold">Dashboard</h3>
                    <p class="text-sm opacity-90">Tableau de bord</p>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Affiche stats, graphiques et tableaux
                    </p>
                    <a href="{{ route('dashboard') }}" 
                       class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                        <i class="fas fa-arrow-right mr-2"></i>Tester
                    </a>
                    <code class="block mt-2 text-xs text-gray-500 dark:text-gray-400">
                        /dashboard
                    </code>
                </div>
            </div>

            <!-- Test 4: Profile -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border-2 border-pink-200 dark:border-pink-800 hover:border-pink-400 dark:hover:border-pink-600 transition-all duration-300">
                <div class="bg-gradient-to-r from-pink-500 to-pink-600 p-6 text-white">
                    <i class="fas fa-user text-3xl mb-2"></i>
                    <h3 class="text-xl font-bold">Profile</h3>
                    <p class="text-sm opacity-90">Profil utilisateur</p>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Affiche avatar, tabs et informations profil
                    </p>
                    @auth
                        <a href="{{ route('profile.index') }}" 
                           class="block w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                            <i class="fas fa-arrow-right mr-2"></i>Tester
                        </a>
                        <code class="block mt-2 text-xs text-gray-500 dark:text-gray-400">
                            /profile
                        </code>
                    @else
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Connexion requise
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Test 5: Generic -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border-2 border-yellow-200 dark:border-yellow-800 hover:border-yellow-400 dark:hover:border-yellow-600 transition-all duration-300">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6 text-white">
                    <i class="fas fa-file text-3xl mb-2"></i>
                    <h3 class="text-xl font-bold">Generic Page</h3>
                    <p class="text-sm opacity-90">Page générique</p>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Skeleton par défaut pour pages inconnues
                    </p>
                    <a href="/about" 
                       data-skeleton-type="generic"
                       class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                        <i class="fas fa-arrow-right mr-2"></i>Tester
                    </a>
                    <code class="block mt-2 text-xs text-gray-500 dark:text-gray-400">
                        data-skeleton-type="generic"
                    </code>
                </div>
            </div>

            <!-- Test 6: No Skeleton -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border-2 border-red-200 dark:border-red-800 hover:border-red-400 dark:hover:border-red-600 transition-all duration-300">
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-white">
                    <i class="fas fa-ban text-3xl mb-2"></i>
                    <h3 class="text-xl font-bold">No Skeleton</h3>
                    <p class="text-sm opacity-90">Sans skeleton</p>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Lien désactivé pour le skeleton (navigation normale)
                    </p>
                    <a href="{{ route('items.index') }}" 
                       data-no-skeleton
                       class="block w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                        <i class="fas fa-arrow-right mr-2"></i>Tester
                    </a>
                    <code class="block mt-2 text-xs text-gray-500 dark:text-gray-400">
                        data-no-skeleton
                    </code>
                </div>
            </div>

        </div>

        <!-- Instructions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                Instructions
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">
                        ✅ Ce qui déclenche le skeleton :
                    </h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Tous les liens internes (même domaine)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Clics sur navigation, boutons, cards</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Détection automatique du type de page</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">
                        ❌ Ce qui NE déclenche PAS le skeleton :
                    </h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                            <span>Liens externes (target="_blank")</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                            <span>Liens avec data-no-skeleton</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                            <span>Navigation back/forward du navigateur</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Console de debug -->
        <div class="bg-gray-900 dark:bg-black rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-terminal mr-2"></i>
                    Console de Debug
                </h2>
                <button onclick="clearConsole()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">
                    <i class="fas fa-trash mr-2"></i>Effacer
                </button>
            </div>
            
            <div id="debug-console" class="bg-gray-800 dark:bg-gray-950 rounded-lg p-4 h-64 overflow-y-auto font-mono text-sm">
                <div class="text-green-400">$ Navigation Skeleton Manager initialisé</div>
                <div class="text-gray-400">Cliquez sur un lien pour voir les logs...</div>
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl shadow-xl p-8 text-white">
            <h2 class="text-2xl font-bold mb-4">📊 Statistiques du système</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                    <div class="text-3xl font-bold" id="stat-clicks">0</div>
                    <div class="text-sm opacity-80">Clics interceptés</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                    <div class="text-3xl font-bold" id="stat-skeletons">0</div>
                    <div class="text-sm opacity-80">Skeletons affichés</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                    <div class="text-3xl font-bold" id="stat-avg-time">0ms</div>
                    <div class="text-sm opacity-80">Temps moyen</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                    <div class="text-3xl font-bold" id="stat-types">6</div>
                    <div class="text-sm opacity-80">Types de skeleton</div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
let clickCount = 0;
let skeletonCount = 0;
let totalTime = 0;

function addLog(message, type = 'info') {
    const console = document.getElementById('debug-console');
    const timestamp = new Date().toLocaleTimeString();
    const colors = {
        info: 'text-blue-400',
        success: 'text-green-400',
        warning: 'text-yellow-400',
        error: 'text-red-400'
    };
    
    const log = document.createElement('div');
    log.className = colors[type] || 'text-gray-400';
    log.innerHTML = `[${timestamp}] ${message}`;
    console.appendChild(log);
    console.scrollTop = console.scrollHeight;
}

function clearConsole() {
    document.getElementById('debug-console').innerHTML = `
        <div class="text-green-400">$ Console effacée</div>
        <div class="text-gray-400">Prêt pour de nouveaux logs...</div>
    `;
}

function updateStats() {
    document.getElementById('stat-clicks').textContent = clickCount;
    document.getElementById('stat-skeletons').textContent = skeletonCount;
    const avgTime = skeletonCount > 0 ? Math.round(totalTime / skeletonCount) : 0;
    document.getElementById('stat-avg-time').textContent = avgTime + 'ms';
}

// Intercepter les clics pour les stats (sans bloquer la navigation)
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link && link.href && !link.hasAttribute('data-no-skeleton')) {
        clickCount++;
        updateStats();
        addLog(`🖱️ Clic sur: ${link.textContent.trim()}`, 'info');
        
        if (!link.href.includes('http') || link.href.includes(window.location.hostname)) {
            skeletonCount++;
            const startTime = Date.now();
            
            addLog(`✨ Skeleton affiché pour: ${link.href}`, 'success');
            
            // Simuler le temps d'affichage
            setTimeout(() => {
                const time = Date.now() - startTime;
                totalTime += time;
                updateStats();
            }, 100);
        }
    }
}, true);

// Log de l'événement skeleton
document.addEventListener('skeletonHidden', () => {
    addLog('✅ Skeleton caché, contenu affiché', 'success');
});

// Vérifier que le système est chargé
window.addEventListener('DOMContentLoaded', () => {
    if (window.navigationSkeletonManager) {
        addLog('✅ Navigation Skeleton Manager chargé', 'success');
        addLog(`📋 Patterns exclus: ${window.navigationSkeletonManager.options.excludePatterns.join(', ')}`, 'info');
    } else {
        addLog('❌ Navigation Skeleton Manager non trouvé', 'error');
    }
});
</script>
@endsection
