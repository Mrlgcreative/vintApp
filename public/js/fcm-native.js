/**
 * Gestion des notifications push FCM pour l'app mobile (webview Capacitor).
 * Chargé UNIQUEMENT lorsque la webview Capacitor native est détectée.
 *
 * - Demande la permission de notification (Android 13+)
 * - Crée le canal "vintapp_notifications" utilisé par le backend
 * - Récupère le token FCM natif et l'enregistre sur POST /api/fcm-token
 * - Écoute la réception d'une notification et le tap pour navigation
 */
(function () {
    'use strict';

    var Capacitor = window.Capacitor;
    if (!Capacitor || !Capacitor.isNativePlatform || !Capacitor.isNativePlatform()) {
        return;
    }

    var FirebaseMessaging = Capacitor.Plugins && Capacitor.Plugins.FirebaseMessaging;
    if (!FirebaseMessaging) {
        return;
    }

    var isAuthenticated = window.isAuthenticated === true;

    function postJson(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                    ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    : ''
            },
            credentials: 'same-origin'
        });
    }

    async function registerToken() {
        try {
            var tokenResult = await FirebaseMessaging.getToken();
            var token = tokenResult && tokenResult.token;
            if (!token) {
                return;
            }

            var response = await postJson('/api/fcm-token', {
                token: token,
                device_type: 'mobile'
            });

            if (response.ok) {
                console.log('[VintApp FCM] Token enregistré');
            } else {
                console.warn('[VintApp FCM] Enregistrement du token refusé (' + response.status + ')');
            }
        } catch (e) {
            console.warn('[VintApp FCM] Échec enregistrement token:', e);
        }
    }

    async function init() {
        try {
            // Canal de notification Android (utilisé par le backend)
            try {
                await FirebaseMessaging.createChannel({
                    id: 'vintapp_notifications',
                    name: 'Notifications VintApp',
                    description: 'Alertes VintApp (commandes, messages, promos)',
                    importance: 5,
                    visibility: 1,
                    lights: true,
                    vibration: true
                });
            } catch (e) {
                console.warn('[VintApp FCM] Canal de notification non créé:', e);
            }

            // Permission (Android 13+)
            var permission = await FirebaseMessaging.requestPermissions();
            if (!permission || permission.receive === 'denied') {
                console.warn('[VintApp FCM] Permission refusée');
                return;
            }

            // Token renouvelé dans le temps
            await FirebaseMessaging.addListener('tokenReceived', function () {
                registerToken();
            });

            // Réception d'une notification pendant que l'app est active
            await FirebaseMessaging.addListener('notificationReceived', function (msg) {
                console.log('[VintApp FCM] Notification reçue:', msg);
            });

            // Tap sur une notification (après ouverture de l'app)
            await FirebaseMessaging.addListener('notificationActionPerformed', function (event) {
                var notification = event && event.notification;
                var data = notification && notification.data ? notification.data : {};
                if (data && data.url) {
                    window.location.href = data.url;
                }
            });

            if (isAuthenticated) {
                registerToken();
            }
        } catch (e) {
            console.warn('[VintApp FCM] Init:', e);
        }
    }

    // Attend la vérification d'auth au cas où le script s'exécute pendant une
    // redirection de login, puis initialise.
    init();
})();