# 🔔 Push Notifications Firebase - Configuration Complète

## ✅ Configuration Terminée

### 1️⃣ VAPID Key - ✅ FAIT

La clé VAPID a été configurée dans `.env`:

```env
FIREBASE_VAPID_KEY=BAE5dM7Fc4f3s7H5Isru52xxcR60apO46k9IuFcMni04qZW3iqbIjjhwP7gle-mWQ1vGyPxZ0i3SWvn1Q3UKnoE
```

### 2️⃣ Service Worker - ✅ FAIT

-   **`public/sw.js`**: Event listeners `push`, `notificationclick`, `notificationclose`
-   **`public/firebase-messaging-sw.js`**: Service Worker dédié Firebase avec `onBackgroundMessage()`
-   Navigation intelligente vers les commandes/messages
-   Actions: "Voir" et "Fermer"

### 3️⃣ Frontend Push Manager - ✅ FAIT

-   **`public/js/push-manager.js`**: Classe `PushNotificationManager`
    -   Initialisation Firebase Messaging
    -   Demande de permission avec modal stylisée
    -   Gestion des tokens FCM (subscribe/unsubscribe)
    -   Notifications foreground et in-app
    -   Prompt automatique après 30 secondes
    -   Affichage après 7 jours si dismissed

### 4️⃣ Backend Laravel - ✅ FAIT

-   **`app/Services/PushNotificationService.php`**:

    -   `sendToUser()`: Envoyer à un utilisateur
    -   `sendToToken()`: Envoyer à un token spécifique
    -   `notifyNewOrder()`: Nouvelle commande
    -   `notifyOrderConfirmed()`: Commande confirmée
    -   `notifyOrderShipped()`: Commande expédiée
    -   `notifyNewMessage()`: Nouveau message
    -   `notifyItemSold()`: Article vendu
    -   `notifyNewReview()`: Nouvel avis
    -   OAuth 2.0 avec Service Account JWT

-   **`app/Http/Controllers/Api/NotificationController.php`**:

    -   `POST /api/notifications/subscribe`: Sauvegarder token FCM
    -   `POST /api/notifications/unsubscribe`: Supprimer token
    -   `POST /api/notifications/closed`: Analytics
    -   `POST /api/notifications/test`: Tester notification

-   **Migration**: Colonnes `fcm_token`, `device_type`, `browser`, `fcm_token_updated_at` dans `users`

---

## 🔥 Prochaine Étape: Service Account Firebase

### ⚠️ IMPORTANT: Télécharger Service Account

Pour que le backend Laravel puisse envoyer des notifications, vous devez télécharger le fichier **Service Account JSON** depuis Firebase.

### 📋 Instructions:

1. **Aller dans Firebase Console**:

    ```
    https://console.firebase.google.com
    ```

2. **Sélectionner le projet**: `vintapp-e6fa7`

3. **Aller dans Project Settings** (⚙️ en haut à gauche)

4. **Onglet "Service accounts"**

5. **Cliquer sur "Generate new private key"**

    - ⚠️ Un fichier JSON sera téléchargé (ex: `vintapp-e6fa7-firebase-adminsdk-xxxxx.json`)
    - ⚠️ **NE JAMAIS PARTAGER CE FICHIER** (il contient une clé privée)

6. **Renommer le fichier** en: `firebase-service-account.json`

7. **Placer le fichier** dans:

    ```
    C:\Users\gloir\Desktop\vintApp\storage\app\firebase-service-account.json
    ```

8. **Vérifier le fichier** contient bien:
    ```json
    {
        "type": "service_account",
        "project_id": "vintapp-e6fa7",
        "private_key_id": "...",
        "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
        "client_email": "firebase-adminsdk-xxxxx@vintapp-e6fa7.iam.gserviceaccount.com",
        "client_id": "...",
        "auth_uri": "https://accounts.google.com/o/oauth2/auth",
        "token_uri": "https://oauth2.googleapis.com/token",
        "auth_provider_x509_cert_url": "...",
        "client_x509_cert_url": "..."
    }
    ```

---

## 🧪 Tester les Notifications

### 1. Activer les notifications dans l'app

Après 30 secondes de navigation, un modal s'affichera:

```
┌─────────────────────────────────┐
│   🔔                            │
│   Activer les notifications     │
│                                 │
│   ✓ Nouvelle commande confirmée│
│   ✓ Message d'un acheteur      │
│   ✓ Article vendu              │
│                                 │
│  [Plus tard]  [Activer]        │
└─────────────────────────────────┘
```

### 2. Tester manuellement

Ouvrir la console navigateur et exécuter:

```javascript
// Afficher le prompt immédiatement
window.pushManager.showPermissionPrompt();

// Vérifier le statut
console.log(window.pushManager.getPermissionStatus());

// Forcer l'abonnement
await window.pushManager.requestPermission();

// Voir le token actuel
console.log(window.pushManager.currentToken);
```

### 3. Envoyer une notification test via API

Une fois les notifications activées:

```bash
# Authentifié en tant qu'utilisateur
curl -X POST http://localhost:8000/api/notifications/test \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json"
```

Ou via l'interface (créer un bouton de test):

```javascript
fetch("/api/notifications/test", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
    },
})
    .then((r) => r.json())
    .then(console.log);
```

### 4. Déclencher une notification réelle

Dans `OrderController::store()`:

```php
use App\Services\PushNotificationService;

public function store(Request $request)
{
    // ... création de la commande ...

    // Notifier le vendeur
    $pushService = app(PushNotificationService::class);
    $pushService->notifyNewOrder($order->seller, $order);

    // Notifier l'acheteur
    $pushService->notifyOrderConfirmed($order->buyer, $order);

    return response()->json($order);
}
```

---

## 📊 Architecture Complète

```
┌─────────────────────────────────────────────────────────┐
│                      FRONTEND                           │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────────┐    ┌──────────────┐                 │
│  │  pwa.js      │    │push-manager.js│                 │
│  │  (PWA Core)  │    │ (FCM Client)  │                 │
│  └──────────────┘    └──────┬───────┘                 │
│                              │                          │
│                    ┌─────────▼─────────┐               │
│                    │  Service Worker   │               │
│                    │  - sw.js          │               │
│                    │  - firebase-sw.js │               │
│                    └─────────┬─────────┘               │
└─────────────────────────────┼──────────────────────────┘
                               │
                    ┌──────────▼──────────┐
                    │  Firebase Cloud     │
                    │  Messaging (FCM)    │
                    └──────────┬──────────┘
                               │
┌─────────────────────────────▼──────────────────────────┐
│                      BACKEND                            │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌────────────────────────────────────────┐            │
│  │ PushNotificationService.php            │            │
│  │ - sendToUser()                         │            │
│  │ - notifyNewOrder()                     │            │
│  │ - notifyOrderConfirmed()               │            │
│  │ - notifyNewMessage()                   │            │
│  └────────────────────────────────────────┘            │
│                                                         │
│  ┌────────────────────────────────────────┐            │
│  │ Api\NotificationController.php         │            │
│  │ POST /api/notifications/subscribe      │            │
│  │ POST /api/notifications/unsubscribe    │            │
│  │ POST /api/notifications/test           │            │
│  └────────────────────────────────────────┘            │
│                                                         │
│  ┌────────────────────────────────────────┐            │
│  │ Database (users table)                 │            │
│  │ - fcm_token                            │            │
│  │ - device_type                          │            │
│  │ - browser                              │            │
│  └────────────────────────────────────────┘            │
└─────────────────────────────────────────────────────────┘
```

---

## 🔒 Sécurité

### ✅ Ce qui est sécurisé:

-   ✅ VAPID Key exposée côté client (normal, publique)
-   ✅ Tokens FCM stockés en base (peuvent être révoqués)
-   ✅ Routes API protégées par `auth:sanctum`
-   ✅ Rate limiting (60 requêtes/min)
-   ✅ CSRF protection

### ⚠️ À protéger:

-   ⚠️ **Service Account JSON**: JAMAIS dans Git
-   ⚠️ Ajouter au `.gitignore`:
    ```
    storage/app/firebase-service-account.json
    ```

---

## 📝 Prochaines Étapes (après Service Account)

1. ✅ **Service Account configuré** → Tester notification backend
2. 📱 **Intégration OrderController**: Notifications nouvelle commande
3. 💬 **Intégration MessageController**: Notifications nouveaux messages
4. 🎨 **Badge compteur**: Afficher nombre de notifications non lues
5. 🔕 **Préférences**: Page pour désactiver certains types de notifications
6. 📊 **Analytics**: Tracker taux d'ouverture des notifications

---

## 🆘 Troubleshooting

### Notification pas reçue?

1. Vérifier permission: `Notification.permission === 'granted'`
2. Vérifier token FCM enregistré: `SELECT fcm_token FROM users WHERE id = X`
3. Vérifier logs Laravel: `tail -f storage/logs/laravel.log`
4. Vérifier Service Worker actif: DevTools → Application → Service Workers

### Erreur "Service Account not found"?

-   Le fichier `storage/app/firebase-service-account.json` n'existe pas
-   Télécharger depuis Firebase Console (voir instructions ci-dessus)

### Token FCM null?

-   L'utilisateur n'a pas activé les notifications
-   Vérifier que `push-manager.js` est chargé: DevTools → Network

### Notification affichée 2 fois?

-   Normal: 1x par Service Worker, 1x par foreground listener
-   Ajouter logique de déduplication si nécessaire

---

**Auteur**: VintApp Dev Team  
**Date**: 2025-11-28  
**Version**: 1.0.0
