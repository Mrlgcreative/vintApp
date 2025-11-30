<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Test Notifications Temps Réel</title>
    
    
    <?php if(file_exists(public_path('build/manifest.json'))): ?>
        <?php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            $cssFile = $manifest['resources/js/app.js']['css'][0] ?? null;
        ?>
        <?php if($jsFile): ?>
            <script type="module" src="<?php echo e(asset('build/' . $jsFile)); ?>"></script>
        <?php endif; ?>
        <?php if($cssFile): ?>
            <link rel="stylesheet" href="<?php echo e(asset('build/' . $cssFile)); ?>">
        <?php endif; ?>
    <?php else: ?>
        
        <script type="module" src="<?php echo e(asset('build/assets/app-LEPBYlP0.js')); ?>"></script>
        <link rel="stylesheet" href="<?php echo e(asset('build/assets/app-YxIKdCll.css')); ?>">
    <?php endif; ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .test-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        .status-connected { background: #10b981; box-shadow: 0 0 20px #10b981; }
        .status-disconnected { background: #ef4444; }
        .log-container {
            background: #1e293b;
            border-radius: 10px;
            padding: 20px;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            max-height: 400px;
            overflow-y: auto;
            margin: 20px 0;
        }
        .log-entry {
            margin: 5px 0;
            padding: 5px;
            border-left: 3px solid #00ff00;
            padding-left: 10px;
        }
        .log-error { border-left-color: #ef4444; color: #ef4444; }
        .log-success { border-left-color: #10b981; color: #10b981; }
        .log-info { border-left-color: #3b82f6; color: #3b82f6; }
    </style>
</head>
<body>
    <div class="test-card">
        <h1 class="text-center mb-4">
            <i class="fas fa-bell text-primary"></i>
            Test Notifications Temps Réel
        </h1>

        <!-- Status de connexion -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    <span class="status-indicator" id="statusIndicator"></span>
                    État de la connexion
                </h5>
                <p class="mb-0" id="statusText">Vérification...</p>
                <small class="text-muted">Utilisateur connecté: <strong><?php echo e(Auth::user()->name ?? 'Non connecté'); ?></strong> (ID: <?php echo e(Auth::id() ?? 'N/A'); ?>)</small>
            </div>
        </div>

        <!-- Configuration Pusher -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-cog me-2"></i>Configuration</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>BROADCAST_CONNECTION:</strong></td>
                        <td><span class="badge bg-info"><?php echo e(config('broadcasting.default')); ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Pusher Key:</strong></td>
                        <td><code><?php echo e(config('broadcasting.connections.pusher.key') ?: 'Non configuré'); ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Pusher Cluster:</strong></td>
                        <td><code><?php echo e(config('broadcasting.connections.pusher.options.cluster') ?: 'Non configuré'); ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Canal d'écoute:</strong></td>
                        <td><code>private-user.<?php echo e(Auth::id()); ?></code></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Console de logs -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-terminal me-2"></i>Console de Logs</h5>
                <div class="log-container" id="logContainer">
                    <div class="log-entry log-info">[INIT] Démarrage du test...</div>
                </div>
                <button class="btn btn-sm btn-secondary mt-2" onclick="clearLogs()">
                    <i class="fas fa-trash me-1"></i> Effacer les logs
                </button>
            </div>
        </div>

        <!-- Actions de test -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-vial me-2"></i>Actions de Test</h5>
                <p class="text-muted">Déclenchez une notification de test pour voir si le système fonctionne.</p>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="sendTestNotification('new_order')">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Tester: Nouvelle Commande
                    </button>
                    <button class="btn btn-success" onclick="sendTestNotification('payment_confirmed')">
                        <i class="fas fa-check-circle me-2"></i>
                        Tester: Paiement Confirmé
                    </button>
                    <button class="btn btn-info" onclick="sendTestNotification('order_shipped')">
                        <i class="fas fa-shipping-fast me-2"></i>
                        Tester: Commande Expédiée
                    </button>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="alert alert-info">
            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Comment tester ?</h6>
            <ol class="mb-0">
                <li>Vérifiez que le statut de connexion est <strong class="text-success">Connecté</strong></li>
                <li>Ouvrez un deuxième onglet avec un autre utilisateur</li>
                <li>Dans l'onglet 2, passez une commande</li>
                <li>Dans cet onglet, vous devriez voir la notification apparaître !</li>
            </ol>
        </div>
    </div>

    <script>
        const logContainer = document.getElementById('logContainer');
        const statusIndicator = document.getElementById('statusIndicator');
        const statusText = document.getElementById('statusText');

        function addLog(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.className = `log-entry log-${type}`;
            logEntry.textContent = `[${timestamp}] ${message}`;
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        function clearLogs() {
            logContainer.innerHTML = '<div class="log-entry log-info">[INIT] Logs effacés</div>';
        }

        function updateStatus(connected, message) {
            statusIndicator.className = `status-indicator ${connected ? 'status-connected' : 'status-disconnected'}`;
            statusText.textContent = message;
        }

        // Attendre que Echo soit chargé
        function initializeEcho() {
            // Test de connexion Echo
            if (window.Echo) {
                addLog('✅ Laravel Echo détecté', 'success');
            
            <?php if(Auth::check()): ?>
                const userId = <?php echo e(Auth::id()); ?>;
                addLog(`📡 Écoute sur le canal: private-user.${userId}`, 'info');

                window.Echo.private(`user.${userId}`)
                    .listen('.order.notification', (data) => {
                        addLog(`📬 NOTIFICATION REÇUE !`, 'success');
                        addLog(`Type: ${data.type}`, 'success');
                        addLog(`Message: ${data.message}`, 'success');
                        addLog(`Commande: #${data.order_number}`, 'success');
                        addLog(`Montant: ${data.total_amount} ${data.currency}`, 'success');
                        console.log('📬 Notification complète:', data);
                    })
                    .subscribed(() => {
                        addLog('✅ Souscription au canal réussie !', 'success');
                        updateStatus(true, 'Connecté et en écoute');
                    })
                    .error((error) => {
                        addLog(`❌ Erreur de souscription: ${error.message || 'Unknown'}`, 'error');
                        updateStatus(false, 'Erreur de connexion');
                        console.error('❌ Erreur complète:', error);
                    });

                // Test de connexion Pusher
                if (window.Echo.connector.pusher) {
                    window.Echo.connector.pusher.connection.bind('connected', () => {
                        addLog('✅ Connecté au serveur Pusher', 'success');
                    });

                    window.Echo.connector.pusher.connection.bind('disconnected', () => {
                        addLog('⚠️ Déconnecté du serveur Pusher', 'error');
                        updateStatus(false, 'Déconnecté');
                    });

                    window.Echo.connector.pusher.connection.bind('error', (error) => {
                        addLog(`❌ Erreur Pusher: ${error.error?.data?.message || 'Unknown'}`, 'error');
                        console.error('❌ Erreur Pusher:', error);
                    });
                }
            <?php else: ?>
                addLog('❌ Utilisateur non connecté', 'error');
                updateStatus(false, 'Non connecté');
            <?php endif; ?>
            } else {
                addLog('❌ Laravel Echo non trouvé !', 'error');
                addLog('Vérifiez que les assets sont compilés (npm run build)', 'error');
                updateStatus(false, 'Echo non initialisé');
            }
        }

        // Attendre que Echo soit chargé (avec retry)
        let retryCount = 0;
        const maxRetries = 20; // 20 tentatives = 2 secondes
        
        function waitForEcho() {
            if (window.Echo) {
                addLog('🚀 Initialisation des notifications...', 'info');
                initializeEcho();
            } else if (retryCount < maxRetries) {
                retryCount++;
                addLog(`⏳ Attente de Laravel Echo... (${retryCount}/${maxRetries})`, 'info');
                setTimeout(waitForEcho, 100); // Réessayer après 100ms
            } else {
                addLog('❌ Timeout: Laravel Echo non chargé après 2 secondes', 'error');
                addLog('Vérifiez que les assets sont compilés (npm run build)', 'error');
                updateStatus(false, 'Echo non initialisé');
            }
        }

        // Démarrer après le chargement du DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', waitForEcho);
        } else {
            waitForEcho();
        }

        function sendTestNotification(type) {
            addLog(`🧪 Envoi d'une notification de test: ${type}`, 'info');
            
            // NOTE: Cette fonction nécessite une route API de test
            // Pour l'instant, elle log simplement une tentative
            fetch('/api/test-notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type })
            })
            .then(response => response.json())
            .then(data => {
                addLog('✅ Notification test envoyée', 'success');
            })
            .catch(error => {
                addLog('ℹ️ Utilisez une vraie commande pour tester', 'info');
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/test-notifications.blade.php ENDPATH**/ ?>