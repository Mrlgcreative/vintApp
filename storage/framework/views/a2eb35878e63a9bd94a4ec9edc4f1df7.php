<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Broadcast FCM - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">
                        📢 Broadcast Notification Push
                    </h1>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-purple-600 hover:text-purple-700">
                        ← Retour Admin
                    </a>
                </div>

                <?php if(auth()->guard()->check()): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800 font-semibold">
                        👤 Connecté: <?php echo e(Auth::user()->name); ?> (Admin)
                    </p>
                </div>

                <!-- Statistiques -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-blue-900 mb-4">📊 Statistiques des appareils</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-gray-600 text-sm">Total utilisateurs</p>
                            <p id="totalUsers" class="text-3xl font-bold text-gray-800">-</p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-gray-600 text-sm">Appareils avec notifications</p>
                            <p id="devicesWithFCM" class="text-3xl font-bold text-green-600">-</p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-gray-600 text-sm">Dernière mise à jour</p>
                            <p id="lastUpdate" class="text-sm font-semibold text-gray-800">-</p>
                        </div>
                    </div>
                    <button onclick="loadStats()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        🔄 Rafraîchir
                    </button>
                </div>

                <!-- Formulaire d'envoi -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-purple-900 mb-4">📨 Envoyer une notification à tous</h2>
                    
                    <form id="broadcastForm" class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                Titre de la notification
                            </label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                maxlength="255"
                                required
                                placeholder="Ex: Nouvelle fonctionnalité !"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                                Message
                            </label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="4"
                                maxlength="500"
                                required
                                placeholder="Ex: Découvrez notre nouveau système de messagerie instantanée !"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                <span id="charCount">0</span>/500 caractères
                            </p>
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-purple-600 text-white font-bold py-4 px-6 rounded-lg hover:bg-purple-700 transition transform hover:scale-105 shadow-lg"
                        >
                            🚀 Envoyer à tous les appareils
                        </button>
                    </form>
                </div>

                <!-- Console de debug -->
                <div class="bg-gray-900 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-green-400">💻 Console</h2>
                        <button onclick="clearConsole()" class="text-red-400 hover:text-red-300 text-sm">
                            Effacer
                        </button>
                    </div>
                    <div id="console" class="space-y-2 max-h-96 overflow-y-auto font-mono text-sm">
                        <!-- Logs will appear here -->
                    </div>
                </div>

                <?php else: ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <p class="text-red-800 font-semibold">
                        ⚠️ Vous devez être connecté en tant qu'administrateur pour accéder à cette page.
                    </p>
                    <a href="<?php echo e(route('login')); ?>" class="mt-4 inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                        Se connecter
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Fonction de log dans la console
        function log(message, type = 'info') {
            const console = document.getElementById('console');
            const timestamp = new Date().toLocaleTimeString('fr-FR');
            const colors = {
                info: 'text-blue-400',
                success: 'text-green-400',
                error: 'text-red-400',
                warning: 'text-yellow-400'
            };
            
            const logEntry = document.createElement('div');
            logEntry.className = `${colors[type] || colors.info} py-1`;
            logEntry.innerHTML = `<span class="text-gray-500">[${timestamp}]</span> ${message}`;
            console.appendChild(logEntry);
            console.scrollTop = console.scrollHeight;
        }

        function clearConsole() {
            document.getElementById('console').innerHTML = '';
            log('Console effacée', 'info');
        }

        // Charger les statistiques
        async function loadStats() {
            try {
                log('📊 Chargement des statistiques...', 'info');
                
                const response = await fetch('/api/admin/fcm-stats', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('totalUsers').textContent = data.stats.total_users;
                    document.getElementById('devicesWithFCM').textContent = data.stats.devices_with_fcm;
                    document.getElementById('lastUpdate').textContent = new Date().toLocaleString('fr-FR');
                    
                    log(`✅ Statistiques chargées: ${data.stats.devices_with_fcm} appareils actifs`, 'success');
                } else {
                    log('❌ Erreur: ' + data.message, 'error');
                }
            } catch (error) {
                log('❌ Erreur chargement stats: ' + error.message, 'error');
            }
        }

        // Gérer le compteur de caractères
        document.getElementById('message')?.addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });

        // Gérer l'envoi du formulaire
        document.getElementById('broadcastForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const title = document.getElementById('title').value;
            const message = document.getElementById('message').value;

            if (!title || !message) {
                log('❌ Veuillez remplir tous les champs', 'error');
                return;
            }

            try {
                log('📤 Envoi de la notification broadcast...', 'info');
                
                const response = await fetch('/api/admin/broadcast-fcm-test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title, message })
                });

                const data = await response.json();

                if (data.success) {
                    log('✅ ' + data.message, 'success');
                    if (data.stats) {
                        log(`📊 Succès: ${data.stats.success} | Échecs: ${data.stats.failure}`, 'info');
                        if (data.stats.failed_tokens.length > 0) {
                            log(`⚠️ ${data.stats.failed_tokens.length} token(s) invalide(s) détecté(s)`, 'warning');
                        }
                    }
                    
                    // Réinitialiser le formulaire
                    document.getElementById('broadcastForm').reset();
                    document.getElementById('charCount').textContent = '0';
                    
                    // Afficher une alerte
                    alert('🎉 Notification envoyée avec succès à tous les appareils !');
                } else {
                    log('❌ Erreur: ' + data.message, 'error');
                    alert('Erreur: ' + data.message);
                }
            } catch (error) {
                log('❌ Erreur envoi: ' + error.message, 'error');
                alert('Erreur lors de l\'envoi de la notification');
            }
        });

        // Charger les stats au chargement de la page
        window.addEventListener('load', () => {
            log('Page chargée', 'info');
            log('Administrateur: <?php echo e(Auth::user()->name ?? "Non connecté"); ?>', 'info');
            loadStats();
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/admin/broadcast-fcm.blade.php ENDPATH**/ ?>