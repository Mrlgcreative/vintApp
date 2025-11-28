@extends('app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
    <div class="max-w-md w-full text-center">
        <!-- Icon -->
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
            </svg>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            📡 Vous êtes hors ligne
        </h1>

        <!-- Description -->
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
            Impossible de se connecter à VintApp. Vérifiez votre connexion Internet et réessayez.
        </p>

        <!-- Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">État de la connexion</span>
                <span id="connection-status" class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                    Hors ligne
                </span>
            </div>
            
            <div class="flex items-center justify-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="reconnect-message">Tentative de reconnexion...</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <button onclick="window.location.reload()" 
                    class="w-full px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                🔄 Réessayer
            </button>
            
            <a href="/" 
               class="block w-full px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
                🏠 Retour à l'accueil
            </a>
        </div>

        <!-- Tips -->
        <div class="mt-12 text-left bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">💡 Astuces :</h3>
            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Vérifiez que votre Wi-Fi ou vos données mobiles sont activés</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Activez le mode avion puis désactivez-le</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Certaines fonctionnalités restent disponibles hors ligne grâce au cache</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Détection automatique de la reconnexion
    let reconnectAttempts = 0;
    const maxAttempts = 10;

    function checkConnection() {
        reconnectAttempts++;
        
        fetch('/', { method: 'HEAD', cache: 'no-cache' })
            .then(() => {
                // Connexion rétablie
                document.getElementById('connection-status').textContent = 'En ligne';
                document.getElementById('connection-status').className = 'px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                document.getElementById('reconnect-message').textContent = 'Connexion rétablie !';
                
                // Redirection après 2 secondes
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            })
            .catch(() => {
                // Toujours hors ligne
                if (reconnectAttempts < maxAttempts) {
                    const nextAttempt = reconnectAttempts + 1;
                    document.getElementById('reconnect-message').textContent = `Tentative ${nextAttempt}/${maxAttempts}...`;
                    
                    // Réessayer après un délai croissant
                    setTimeout(checkConnection, Math.min(reconnectAttempts * 2000, 10000));
                } else {
                    document.getElementById('reconnect-message').textContent = 'Impossible de se connecter. Veuillez réessayer manuellement.';
                }
            });
    }

    // Démarrer les tentatives de reconnexion
    setTimeout(checkConnection, 3000);

    // Écouter les changements de connexion
    window.addEventListener('online', () => {
        document.getElementById('connection-status').textContent = 'En ligne';
        document.getElementById('connection-status').className = 'px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        document.getElementById('reconnect-message').textContent = 'Connexion rétablie !';
        setTimeout(() => window.location.reload(), 2000);
    });

    window.addEventListener('offline', () => {
        document.getElementById('connection-status').textContent = 'Hors ligne';
        document.getElementById('connection-status').className = 'px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    });
</script>
@endsection
