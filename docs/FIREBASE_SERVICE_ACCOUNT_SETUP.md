# Configuration Firebase Service Account

## 🚨 IMPORTANT: Fichier manquant

Le fichier `storage/app/firebase-service-account.json` est **OBLIGATOIRE** pour envoyer des notifications push depuis le backend Laravel.

---

## 📥 Comment obtenir le Service Account

### Étape 1: Accéder à Firebase Console

1. Ouvrir: **https://console.firebase.google.com**
2. Sélectionner le projet: **`vintapp-e6fa7`**

### Étape 2: Générer la clé privée

1. Cliquer sur l'icône **⚙️ (Settings)** en haut à gauche
2. Sélectionner **"Project settings"**
3. Aller dans l'onglet **"Service accounts"**
4. Section **"Firebase Admin SDK"**
5. Cliquer sur le bouton **"Generate new private key"**
6. Confirmer en cliquant **"Generate key"**
7. Un fichier JSON sera téléchargé (ex: `vintapp-e6fa7-firebase-adminsdk-xxxxx-xxxxxxxxxx.json`)

### Étape 3: Installation

1. **Renommer** le fichier téléchargé en: `firebase-service-account.json`
2. **Placer** le fichier dans: `C:\Users\gloir\Desktop\vintApp\storage\app\`
3. **Vérifier** que le chemin complet est: `C:\Users\gloir\Desktop\vintApp\storage\app\firebase-service-account.json`

---

## 🔒 Sécurité

### ⚠️ NE JAMAIS:

-   ❌ Commit ce fichier dans Git
-   ❌ Partager ce fichier publiquement
-   ❌ Copier la clé privée dans du code
-   ❌ L'envoyer par email/Slack/Discord

### ✅ Bonnes pratiques:

-   ✅ Garder le fichier uniquement sur le serveur
-   ✅ Utiliser `.gitignore` pour l'exclure
-   ✅ Régénérer la clé si compromise
-   ✅ Limiter l'accès au fichier (chmod 600 sur Linux)

### Vérifier .gitignore:

Ouvrir `.gitignore` et vérifier que cette ligne est présente:

```
storage/app/firebase-service-account.json
```

---

## ✅ Vérification de l'installation

### 1. Vérifier que le fichier existe:

```powershell
Test-Path "C:\Users\gloir\Desktop\vintApp\storage\app\firebase-service-account.json"
```

✅ Résultat attendu: `True`

### 2. Vérifier le contenu (afficher les 5 premières lignes):

```powershell
Get-Content "C:\Users\gloir\Desktop\vintApp\storage\app\firebase-service-account.json" | Select-Object -First 5
```

✅ Résultat attendu:

```json
{
  "type": "service_account",
  "project_id": "vintapp-e6fa7",
  "private_key_id": "...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...
```

### 3. Tester l'envoi d'une notification:

```powershell
php artisan tinker
```

Puis dans Tinker:

```php
$user = User::first();
$service = app(\App\Services\PushNotificationService::class);
$service->sendToUser($user, [
    'title' => 'Test',
    'body' => 'Notification de test'
], ['url' => '/']);
```

---

## 🧪 Structure attendue du fichier

Le fichier `firebase-service-account.json` doit contenir:

```json
{
    "type": "service_account",
    "project_id": "vintapp-e6fa7",
    "private_key_id": "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
    "private_key": "-----BEGIN PRIVATE KEY-----\nXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\n...\n-----END PRIVATE KEY-----\n",
    "client_email": "firebase-adminsdk-xxxxx@vintapp-e6fa7.iam.gserviceaccount.com",
    "client_id": "XXXXXXXXXXXXXXXXXXX",
    "auth_uri": "https://accounts.google.com/o/oauth2/auth",
    "token_uri": "https://oauth2.googleapis.com/token",
    "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
    "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-xxxxx%40vintapp-e6fa7.iam.gserviceaccount.com",
    "universe_domain": "googleapis.com"
}
```

**Points clés à vérifier:**

-   ✅ `project_id` doit être `"vintapp-e6fa7"`
-   ✅ `private_key` commence par `"-----BEGIN PRIVATE KEY-----"`
-   ✅ `client_email` contient `firebase-adminsdk`
-   ✅ Toutes les URLs sont bien présentes

---

## 🆘 Troubleshooting

### Erreur: "Service Account Firebase introuvable"

**Cause**: Le fichier n'existe pas  
**Solution**: Suivre les étapes 1-3 ci-dessus

### Erreur: "Erreur obtention access token Firebase"

**Cause**: Le fichier JSON est invalide ou corrompu  
**Solutions**:

1. Vérifier que le JSON est bien formaté (pas de caractères manquants)
2. Re-télécharger une nouvelle clé depuis Firebase Console
3. Vérifier les permissions du fichier

### Erreur: "SQLSTATE[42S22]: Column not found: fcm_token"

**Cause**: Migration non exécutée  
**Solution**:

```powershell
php artisan migrate
```

### La notification n'arrive pas

**Vérifier**:

1. ✅ Permission accordée dans le navigateur
2. ✅ Token FCM enregistré en base de données
3. ✅ Service Account correctement configuré
4. ✅ Logs Laravel: `tail -f storage/logs/laravel.log`

---

## 📚 Ressources

-   **Firebase Console**: https://console.firebase.google.com
-   **Documentation Service Account**: https://firebase.google.com/docs/admin/setup
-   **Documentation FCM**: https://firebase.google.com/docs/cloud-messaging

---

**Date**: 2025-11-28  
**Auteur**: VintApp Dev Team
