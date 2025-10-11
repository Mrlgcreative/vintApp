# 🍎 Apple Sign In - Guide Rapide (5 minutes)

## 🚀 Démarrage Ultra Rapide

### ✅ Ce qui est DÉJÀ fait

```bash
✅ Package installé (socialiteproviders/apple v5.7.0)
✅ Migration exécutée (colonne apple_id)
✅ Contrôleur créé (AppleAuthController)
✅ Routes configurées
✅ Boutons ajoutés (login + register)
✅ Model User mis à jour
✅ Event listener configuré
```

### ⏰ Ce qu'il reste à faire (15 minutes)

---

## 📝 Étape 1 : Apple Developer (10 min)

### A. Créer un App ID

1. **Ouvrir** : https://developer.apple.com/account/resources/identifiers/list
2. **Cliquer** : `+` bouton
3. **Sélectionner** : `App IDs`
4. **Remplir** :
   ```
   Description: VintApp
   Bundle ID: com.vintapp.app
   Capabilities: ✅ Sign in with Apple
   ```
5. **Cliquer** : `Continue` → `Register`

### B. Créer un Service ID

1. **Cliquer** : `+` bouton
2. **Sélectionner** : `Services IDs`
3. **Remplir** :
   ```
   Description: VintApp Sign In
   Identifier: com.vintapp.app.signin
   ```
4. **Cocher** : `Sign in with Apple`
5. **Cliquer** : `Configure`
6. **Ajouter** :
   ```
   Primary App ID: com.vintapp.app
   Website URLs:
   - Domain: localhost
   - Return URL: http://localhost:8000/auth/apple/callback
   ```
7. **Cliquer** : `Save` → `Continue` → `Register`

### C. Générer une Private Key

1. **Aller sur** : https://developer.apple.com/account/resources/authkeys/list
2. **Cliquer** : `+` bouton
3. **Remplir** :
   ```
   Key Name: VintApp Sign In Key
   ✅ Sign in with Apple
   Configure: com.vintapp.app
   ```
4. **Cliquer** : `Continue` → `Register`
5. **Télécharger** le fichier `.p8` (⚠️ une seule fois possible !)
6. **Noter** le **Key ID** (10 caractères, ex: `A1B2C3D4E5`)

### D. Trouver votre Team ID

1. **Coin supérieur droit** du Apple Developer Portal
2. **Noter** le **Team ID** (10 caractères, ex: `X1Y2Z3W4V5`)

---

## 🔧 Étape 2 : Configuration Laravel (5 min)

### A. Placer la Private Key

```bash
# Créer le dossier
mkdir storage\app\apple

# Copier le fichier .p8 téléchargé dans :
# storage/app/apple/AuthKey_XXXXXXXXXX.p8
```

### B. Modifier .env

Ouvrir `.env` et ajouter à la fin :

```env
# === Apple OAuth ===
APPLE_CLIENT_ID=com.vintapp.app.signin
APPLE_TEAM_ID=XXXXXXXXXX
APPLE_KEY_ID=YYYYYYYYYY
APPLE_PRIVATE_KEY=storage/app/apple/AuthKey_ZZZZZZZZZZ.p8
APPLE_REDIRECT_URI="${APP_URL}/auth/apple/callback"
```

**Remplacer** :
- `XXXXXXXXXX` → Votre Team ID (10 caractères)
- `YYYYYYYYYY` → Votre Key ID (10 caractères)
- `ZZZZZZZZZZ` → Le nom de votre fichier .p8

### C. Nettoyer le cache

```bash
php artisan config:clear
```

---

## ✅ Étape 3 : Test (2 min)

### 1. Démarrer le serveur

```bash
php artisan serve
```

### 2. Ouvrir le navigateur

```
http://localhost:8000/login
```

### 3. Cliquer sur "Se connecter avec Apple"

**Résultat attendu** :
- ✅ Redirection vers Apple ID
- ✅ Page de connexion Apple
- ✅ Retour vers `/dashboard` après connexion
- ✅ Message : "Connexion réussie avec Apple !"

---

## 🎯 Exemple de .env complet

```env
APP_URL=http://localhost:8000

# Google OAuth (exemple - remplacez par vos vraies valeurs)
GOOGLE_CLIENT_ID=123456789012-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxx
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Apple OAuth (à configurer)
APPLE_CLIENT_ID=com.vintapp.app.signin
APPLE_TEAM_ID=A1B2C3D4E5
APPLE_KEY_ID=X1Y2Z3W4V5
APPLE_PRIVATE_KEY=storage/app/apple/AuthKey_X1Y2Z3W4V5.p8
APPLE_REDIRECT_URI="${APP_URL}/auth/apple/callback"
```

---

## 🐛 Problèmes courants

### "Invalid client"

**Solution** : Vérifier `APPLE_CLIENT_ID` correspond au Service ID créé

```bash
php artisan config:clear
```

### "Invalid redirect URI"

**Solution** : Vérifier le Return URL dans Service ID configuration :
- Development : `http://localhost:8000/auth/apple/callback`
- Production : `https://votredomaine.com/auth/apple/callback`

### "Invalid JWT"

**Solution** : Vérifier les 3 variables :
1. `APPLE_TEAM_ID` (10 caractères)
2. `APPLE_KEY_ID` (10 caractères)
3. `APPLE_PRIVATE_KEY` (chemin correct vers .p8)

---

## 📊 Vérification

### Afficher les utilisateurs Apple

```bash
php artisan tinker
```

```php
User::whereNotNull('apple_id')->get(['id', 'name', 'email', 'apple_id']);
```

### Compter les utilisateurs OAuth

```php
$stats = [
    'total' => User::count(),
    'google' => User::whereNotNull('google_id')->count(),
    'apple' => User::whereNotNull('apple_id')->count(),
    'both' => User::whereNotNull('google_id')->whereNotNull('apple_id')->count(),
];
print_r($stats);
```

---

## 🎉 C'est fait !

Vous avez maintenant :
- ✅ Google OAuth (déjà fonctionnel)
- ✅ Apple OAuth (nouveau)
- ✅ Double authentification possible
- ✅ Email auto-vérifié pour OAuth
- ✅ Liaison automatique de comptes

---

## 📚 Documentation complète

Pour plus de détails, consulter :
- `APPLE_OAUTH_SETUP.md` - Guide complet
- `GOOGLE_OAUTH_SETUP.md` - Configuration Google

---

**Temps total estimé : 15-20 minutes** ⏱️

**Difficulté : Moyenne** 🟡

**Support : support@vintapp.com** 📧
