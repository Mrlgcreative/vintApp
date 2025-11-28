<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWA Debug - VintApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-md mx-auto mt-8">
        <h1 class="text-2xl font-bold text-purple-600 mb-6">🔧 PWA Debug</h1>
        
        <!-- État -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="font-semibold mb-3">📊 État actuel</h2>
            <div id="status" class="space-y-2 text-sm"></div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="font-semibold mb-3">🎯 Actions</h2>
            <button onclick="clearAll()" class="w-full bg-red-500 text-white py-2 px-4 rounded mb-2">
                🗑️ Réinitialiser tout
            </button>
            <button onclick="showInstall()" class="w-full bg-green-500 text-white py-2 px-4 rounded mb-2">
                📱 Forcer l'affichage du bouton
            </button>
            <button onclick="checkPWA()" class="w-full bg-blue-500 text-white py-2 px-4 rounded">
                🔄 Rafraîchir l'état
            </button>
        </div>

        <!-- Logs -->
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="font-semibold mb-3">📝 Logs</h2>
            <div id="logs" class="text-xs font-mono space-y-1 max-h-64 overflow-y-auto"></div>
        </div>

        <a href="/" class="block mt-4 text-center text-purple-600 underline">← Retour à l'accueil</a>
    </div>

    <script>
        function log(msg, color = 'text-gray-700') {
            const div = document.createElement('div');
            div.className = color;
            div.textContent = `[${new Date().toLocaleTimeString()}] ${msg}`;
            document.getElementById('logs').prepend(div);
        }

        function clearAll() {
            localStorage.clear();
            sessionStorage.clear();
            
            // Supprimer tous les cookies
            document.cookie.split(";").forEach(c => {
                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
            });
            
            log('✅ Tout nettoyé ! Rechargez la page.', 'text-green-600');
            
            setTimeout(() => window.location.reload(), 1500);
        }

        function showInstall() {
            localStorage.removeItem('pwa-install-dismissed');
            localStorage.removeItem('pwa-installed');
            log('✅ Flags d\'installation supprimés', 'text-green-600');
            
            // Déclencher l'événement beforeinstallprompt manuellement si disponible
            if (window.deferredPrompt) {
                window.deferredPrompt.prompt();
                log('📱 Prompt d\'installation affiché', 'text-blue-600');
            } else {
                log('⚠️ Aucun prompt disponible. Rechargez la page.', 'text-yellow-600');
            }
        }

        function checkPWA() {
            const status = document.getElementById('status');
            status.innerHTML = '';
            
            const info = [
                { label: 'Standalone mode', value: window.matchMedia('(display-mode: standalone)').matches ? '✅ Oui' : '❌ Non' },
                { label: 'Service Worker', value: 'serviceWorker' in navigator ? '✅ Supporté' : '❌ Non supporté' },
                { label: 'Notifications', value: 'Notification' in window ? `✅ ${Notification.permission}` : '❌ Non supporté' },
                { label: 'Install dismissed', value: localStorage.getItem('pwa-install-dismissed') ? '⚠️ Oui' : '✅ Non' },
                { label: 'PWA installed', value: localStorage.getItem('pwa-installed') ? '⚠️ Oui' : '✅ Non' },
                { label: 'Defer prompt', value: window.deferredPrompt ? '✅ Disponible' : '❌ Non disponible' }
            ];
            
            info.forEach(item => {
                const div = document.createElement('div');
                div.className = 'flex justify-between';
                div.innerHTML = `<span class="text-gray-600">${item.label}:</span><span class="font-medium">${item.value}</span>`;
                status.appendChild(div);
            });
            
            log('🔄 État rafraîchi', 'text-blue-600');
        }

        // Capturer l'événement beforeinstallprompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.deferredPrompt = e;
            log('✅ Event beforeinstallprompt capturé', 'text-green-600');
            checkPWA();
        });

        // Vérifier l'état au chargement
        window.addEventListener('load', () => {
            log('🔧 Page de debug chargée', 'text-blue-600');
            checkPWA();
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/pwa-debug.blade.php ENDPATH**/ ?>