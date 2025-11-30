<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Test Notifications Push FCM - VintApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-purple-600 mb-6">🔔 Test Notifications Push FCM</h1>
            
            <?php if(auth()->guard()->check()): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-green-800">✅ Connecté en tant que: <strong><?php echo e(Auth::user()->name); ?></strong> (ID: <?php echo e(Auth::id()); ?>)</p>
                <?php if(Auth::user()->fcm_token): ?>
                <p class="text-green-700 text-sm mt-2">📱 Token FCM enregistré: <?php echo e(Str::limit(Auth::user()->fcm_token, 50)); ?></p>
                <?php else: ?>
                <p class="text-orange-700 text-sm mt-2">⚠️ Aucun token FCM enregistré</p>
                <?php endif; ?>
            </div>

            <!-- Instructions -->
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-bold text-blue-900 mb-2">📱 Étape 1: Autoriser notifications</h3>
                    <p class="text-sm text-blue-800">Cliquez sur le bouton ci-dessous pour demander la permission</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h3 class="font-bold text-purple-900 mb-2">🧪 Étape 2: Tester notification</h3>
                    <p class="text-sm text-purple-800">Envoyez une notification de test à votre appareil</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-4">
                <button onclick="requestPermission()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                    🔔 Demander Permission Notifications
                </button>

                <button onclick="sendTestNotification('approved')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                    ✅ Test Notification Approbation
                </button>

                <button onclick="sendTestNotification('rejected')" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg">
                    ❌ Test Notification Rejet
                </button>
            </div>

            <!-- Console de debug -->
            <div class="mt-6 bg-gray-900 rounded-lg p-4">
                <h3 class="text-white font-bold mb-2">📋 Console</h3>
                <div id="console" class="text-green-400 font-mono text-sm space-y-1 max-h-96 overflow-y-auto"></div>
            </div>
            <?php else: ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-800">❌ Vous devez être connecté pour tester les notifications</p>
                <a href="<?php echo e(route('login')); ?>" class="text-red-600 underline">Se connecter</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(auth()->guard()->check()): ?>
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>

    <script>
        // Configuration Firebase
        const firebaseConfig = {
            apiKey: "<?php echo e(config('services.firebase.api_key')); ?>",
            authDomain: "<?php echo e(config('services.firebase.auth_domain')); ?>",
            projectId: "<?php echo e(config('services.firebase.project_id')); ?>",
            storageBucket: "<?php echo e(config('services.firebase.storage_bucket')); ?>",
            messagingSenderId: "<?php echo e(config('services.firebase.messaging_sender_id')); ?>",
            appId: "<?php echo e(config('services.firebase.app_id')); ?>"
        };

        const vapidKey = "<?php echo e(config('services.firebase.vapid_key')); ?>";

        // Initialiser Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        function log(message, type = 'info') {
            const console = document.getElementById('console');
            const time = new Date().toLocaleTimeString('fr-FR');
            const colors = {
                info: 'text-blue-400',
                success: 'text-green-400',
                error: 'text-red-400',
                warning: 'text-yellow-400'
            };
            const color = colors[type] || colors.info;
            
            const entry = document.createElement('div');
            entry.className = color;
            entry.textContent = `[${time}] ${message}`;
            console.appendChild(entry);
            console.scrollTop = console.scrollHeight;
        }

        async function requestPermission() {
            try {
                log('📱 Demande de permission notifications...', 'info');

                if (!('Notification' in window)) {
                    log('❌ Ce navigateur ne supporte pas les notifications', 'error');
                    return;
                }

                const permission = await Notification.requestPermission();
                log(`🔔 Permission: ${permission}`, permission === 'granted' ? 'success' : 'warning');

                if (permission === 'granted') {
                    // Enregistrer le Service Worker
                    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                    log('✅ Service Worker enregistré', 'success');

                    // Récupérer le token
                    const token = await messaging.getToken({
                        vapidKey: vapidKey,
                        serviceWorkerRegistration: registration
                    });

                    if (token) {
                        log('✅ Token FCM obtenu: ' + token.substring(0, 30) + '...', 'success');
                        
                        // Sauvegarder le token
                        await saveToken(token);
                    } else {
                        log('❌ Impossible d\'obtenir le token FCM', 'error');
                    }

                    // Écouter les messages
                    messaging.onMessage((payload) => {
                        log('📬 Notification reçue: ' + payload.notification.title, 'success');
                        console.log('Payload:', payload);
                    });
                } else {
                    log('❌ Permission refusée', 'error');
                }
            } catch (error) {
                log('❌ Erreur: ' + error.message, 'error');
                console.error(error);
            }
        }

        async function saveToken(token) {
            try {
                const response = await fetch('/api/fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        token: token,
                        device_type: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent) ? 'mobile' : 'desktop'
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    log('✅ Token enregistré sur le serveur', 'success');
                    location.reload(); // Rafraîchir pour voir le token
                } else {
                    log('❌ Erreur enregistrement: ' + data.message, 'error');
                }
            } catch (error) {
                log('❌ Erreur sauvegarde token: ' + error.message, 'error');
            }
        }

        async function sendTestNotification(type) {
            try {
                log(`📤 Envoi notification test (${type})...`, 'info');

                const response = await fetch('/api/test-fcm-notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ type: type })
                });

                const data = await response.json();
                
                if (data.success) {
                    log('✅ Notification envoyée ! Vérifiez votre téléphone', 'success');
                    alert('📱 Notification envoyée ! Vérifiez la barre de notification de votre téléphone');
                } else {
                    log('❌ Erreur: ' + data.message, 'error');
                    alert('Erreur: ' + data.message);
                }
            } catch (error) {
                log('❌ Erreur envoi: ' + error.message, 'error');
                console.error(error);
            }
        }

        // Log initial
        log('🚀 Page de test chargée', 'info');
        log('📱 Navigateur: ' + navigator.userAgent, 'info');
        log('🔔 Support notifications: ' + ('Notification' in window ? 'OUI' : 'NON'), 'info');
    </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/test-fcm.blade.php ENDPATH**/ ?>