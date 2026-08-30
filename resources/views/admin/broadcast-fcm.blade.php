@extends('layouts.admin')

@section('title', 'Broadcast Push FCM')
@section('page-title', 'Broadcast Push FCM')
@section('page-subtitle', 'Envoyez une notification push à tous les appareils enregistrés')

@section('page-actions')
<a href="{{ route('admin.dashboard') }}"
   class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
    <i class="fas fa-arrow-left"></i>Retour Admin
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Connecté -->
    <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 px-4 py-3">
        <i class="fas fa-user-shield text-emerald-600 dark:text-emerald-400"></i>
        <p class="flex-1 text-sm font-medium text-emerald-800 dark:text-emerald-200">
            Connecté: {{ Auth::user()->name }} (Admin)
        </p>
    </div>

    <!-- Statistiques des appareils -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
                <i class="fas fa-chart-column text-primary-600"></i>
                Statistiques des appareils
            </h2>
            <button type="button" onclick="loadStats()"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors">
                <i class="fas fa-rotate"></i>Rafraîchir
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Total utilisateurs</p>
                <p id="totalUsers" class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">-</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Appareils avec notifications</p>
                <p id="devicesWithFCM" class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-green-600">-</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Dernière mise à jour</p>
                <p id="lastUpdate" class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-200">-</p>
            </div>
        </div>
    </div>

    <!-- Envoyer une notification -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <h2 class="mb-5 flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
            <i class="fas fa-paper-plane text-primary-600"></i>
            Envoyer une notification à tous
        </h2>

        <form id="broadcastForm" class="space-y-4">
            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Titre de la notification
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="255"
                    required
                    placeholder="Ex: Nouvelle fonctionnalité !"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                >
            </div>

            <div>
                <label for="message" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Message
                </label>
                <textarea
                    id="message"
                    name="message"
                    rows="4"
                    maxlength="500"
                    required
                    placeholder="Ex: Découvrez notre nouveau système de messagerie instantanée !"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                ></textarea>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    <span id="charCount">0</span>/500 caractères
                </p>
            </div>

            <button
                type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors"
            >
                <i class="fas fa-bullhorn"></i>
                Envoyer à tous les appareils
            </button>
        </form>
    </div>

    <!-- Console de debug -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
                <i class="fas fa-terminal text-green-600"></i>
                Console
            </h2>
            <button type="button" onclick="clearConsole()"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-eraser"></i>Effacer
            </button>
        </div>
        <div id="console" class="max-h-96 space-y-2 overflow-y-auto rounded-lg bg-slate-900 p-4 font-mono text-sm">
            <!-- Logs will appear here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fonction de log dans la console
    function log(message, type = 'info') {
        const console = document.getElementById('console');
        const timestamp = new Date().toLocaleTimeString('fr-FR');
        const colors = {
            info: 'text-sky-400',
            success: 'text-green-400',
            error: 'text-red-400',
            warning: 'text-yellow-400'
        };

        const logEntry = document.createElement('div');
        logEntry.className = `${colors[type] || colors.info} py-1`;
        logEntry.innerHTML = `<span class="text-slate-500">[${timestamp}]</span> ${message}`;
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
        log('Administrateur: {{ Auth::user()->name ?? "Non connecté" }}', 'info');
        loadStats();
    });
</script>
@endpush