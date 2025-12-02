# Guide de mise à jour de la clé API Firebase

## Problème

Erreur : `Firebase: Error (auth/api-key-expired.-please-renew-the-api-key.)`

## Solution

### Étape 1 : Obtenir une nouvelle clé API

1. Connectez-vous à la [Console Firebase](https://console.firebase.google.com)
2. Sélectionnez votre projet **vintapp-e6fa7**
3. Cliquez sur l'icône ⚙️ (Paramètres) > **Paramètres du projet**
4. Dans l'onglet **Général**, descendez jusqu'à **Vos applications**
5. Sélectionnez l'application Web
6. Copiez toute la configuration Firebase, elle ressemble à ceci :

```javascript
const firebaseConfig = {
    apiKey: "VOTRE_NOUVELLE_CLE_API",
    authDomain: "vintapp-e6fa7.firebaseapp.com",
    projectId: "vintapp-e6fa7",
    storageBucket: "vintapp-e6fa7.appspot.com",
    messagingSenderId: "880178183981",
    appId: "1:880178183981:web:VOTRE_APP_ID",
};
```

### Étape 2 : Mettre à jour le fichier .env

Ouvrez le fichier `.env` à la racine du projet et mettez à jour les valeurs suivantes :

```env
# Firebase Configuration
FIREBASE_API_KEY="VOTRE_NOUVELLE_CLE_API"
FIREBASE_AUTH_DOMAIN="vintapp-e6fa7.firebaseapp.com"
FIREBASE_PROJECT_ID="vintapp-e6fa7"
FIREBASE_STORAGE_BUCKET="vintapp-e6fa7.appspot.com"
FIREBASE_MESSAGING_SENDER_ID="880178183981"
FIREBASE_APP_ID="VOTRE_APP_ID"
FIREBASE_CREDENTIALS="storage/app/firebase-service-account.json"
FIREBASE_DATABASE_URL="https://vintapp-e6fa7.firebaseio.com"
```

### Étape 3 : Vider le cache Laravel

Exécutez ces commandes dans le terminal :

```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Étape 4 : Redémarrer le serveur

Si vous utilisez `php artisan serve`, arrêtez-le (Ctrl+C) et redémarrez-le :

```powershell
php artisan serve
```

### Étape 5 : Tester

Rechargez la page de connexion dans votre navigateur et testez la connexion Google.

## Note importante

Les fichiers suivants utilisent maintenant la configuration depuis `.env` :

-   `resources/views/auth/login.blade.php`
-   `resources/views/auth/register.blade.php`
-   `resources/views/auth/firebase-login.blade.php`

Vous n'avez plus besoin de modifier ces fichiers directement pour changer la clé API.

## En cas de problème

Si après la mise à jour vous avez toujours des erreurs :

1. Vérifiez que la clé API est correcte dans le fichier `.env`
2. Vérifiez dans la console Firebase que les restrictions de clé API n'empêchent pas son utilisation
3. Consultez les logs Laravel : `storage/logs/laravel.log`
