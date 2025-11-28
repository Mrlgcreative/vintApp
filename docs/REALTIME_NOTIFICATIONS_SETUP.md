# 🔔 Configuration des Notifications en Temps Réel

## 📋 Guide de Configuration Pusher

### Étape 1: Créer un compte Pusher (GRATUIT)

1. **Visitez**: https://dashboard.pusher.com/accounts/sign_up
2. **Inscrivez-vous** avec votre email
3. **Créez une nouvelle app**:
   - Name: `vintapp-notifications`
   - Cluster: Choisissez le plus proche (`eu` pour Europe, `us2` pour USA, etc.)
   - Frontend tech: `Vanilla JS`
   - Backend tech: `Laravel`

### Étape 2: Récupérer les Credentials

Une fois l'app créée, allez dans **App Keys** et copiez:
- `app_id`
- `key`
- `secret`
- `cluster`

### Étape 3: Configurer le .env

Ajoutez/modifiez ces lignes dans votre fichier `.env`:

```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
PUSHER_SCHEME=https
PUSHER_HOST=
PUSHER_PORT=443

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
```

### Étape 4: Recompiler les Assets

```bash
npm run build
```

### Étape 5: Redémarrer le serveur Laravel

```bash
php artisan config:clear
php artisan cache:clear
php artisan serve
```

## 🧪 Tester les Notifications

1. **Ouvrez 2 onglets**:
   - Onglet 1: Connecté comme **vendeur**
   - Onglet 2: Connecté comme **acheteur**

2. **Dans l'onglet acheteur**: Passez une commande

3. **Dans l'onglet vendeur**: Vous devriez voir une notification apparaître instantanément ! 🎉

## 📊 Types de Notifications

- ✅ **new_order**: Nouvelle commande passée (→ vendeur)
- 💰 **payment_confirmed**: Paiement confirmé (→ vendeur)
- 📦 **order_shipped**: Commande expédiée (→ acheteur)
- 🚚 **order_delivered**: Commande livrée (→ acheteur)
- ✅ **order_completed**: Commande terminée (→ vendeur + acheteur)

## 🔍 Débogage

### Vérifier que Pusher reçoit les événements:

1. Allez sur votre **Pusher Dashboard**
2. Cliquez sur **Debug Console**
3. Passez une commande
4. Vous devriez voir les événements apparaître en temps réel

### Si aucune notification n'apparaît:

1. **Vérifiez la console JavaScript** (F12)
2. **Vérifiez les logs Laravel**: `tail -f storage/logs/laravel.log`
3. **Vérifiez que BROADCAST_CONNECTION=pusher** dans .env
4. **Vérifiez que les credentials Pusher sont corrects**

## 🆓 Plan Gratuit Pusher

- ✅ 200,000 messages/jour
- ✅ 100 connexions simultanées
- ✅ Support SSL/TLS
- ✅ Parfait pour le développement et petites apps

## 📝 Fichiers Modifiés

- ✅ `app/Events/OrderNotification.php` - Événement broadcast
- ✅ `routes/channels.php` - Canaux privés
- ✅ `resources/js/bootstrap.js` - Configuration Laravel Echo
- ✅ `resources/views/app.blade.php` - Composant notifications
- ✅ `app/Http/Controllers/OrderController.php` - Trigger notifications

## 🚀 Déploiement en Production

Pour la production, considérez:
- **Laravel WebSockets** (auto-hébergé, gratuit)
- **Soketi** (compatible Pusher, open source)
- **Ably** (alternative à Pusher)
