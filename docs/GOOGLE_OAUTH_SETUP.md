# Guide de Configuration Google OAuth

## 📋 Vue d'ensemble

Ce document explique comment configurer l'authentification Google OAuth pour VintApp.

## ✅ Statut d'implémentation

**Installation et code : 100% ✅**

- ✅ Package Laravel Socialite installé
- ✅ Migration créée et exécutée (google_id, google_token, google_refresh_token, avatar_url)
- ✅ Modèle User mis à jour
- ✅ GoogleAuthController créé avec logique complète
- ✅ Routes configurées (auth/google, auth/google/callback)
- ✅ Boutons Google ajoutés aux pages login et register
- ✅ Configuration services.php ajoutée

**Ce qu'il reste à faire : Configuration des identifiants Google**

## 🔧 Configuration Google Cloud Console

### Étape 1 : Créer un projet Google Cloud

1. Allez sur [Google Cloud Console](https://console.cloud.google.com/)
2. Cliquez sur **"Sélectionner un projet"** en haut
3. Cliquez sur **"Nouveau projet"**
4. Nommez le projet : `VintApp` ou `VintApp OAuth`
5. Cliquez sur **"Créer"**

### Étape 2 : Activer l'API Google+

1. Dans le menu de gauche, allez dans **"APIs et services"** > **"Bibliothèque"**
2. Recherchez **"Google+ API"** ou **"Google People API"**
3. Cliquez sur l'API
4. Cliquez sur **"Activer"**

### Étape 3 : Configurer l'écran de consentement OAuth

1. Allez dans **"APIs et services"** > **"Écran de consentement OAuth"**
2. Sélectionnez **"Externe"** (pour tester avec n'importe quel compte Google)
3. Cliquez sur **"Créer"**
4. Remplissez les informations :
   - **Nom de l'application** : `VintApp`
   - **Email d'assistance** : Votre email
   - **Logo de l'application** : (Optionnel)
   - **Domaine de l'application** : Laissez vide en développement
   - **Domaine autorisé** : Laissez vide en développement
   - **Email du développeur** : Votre email
5. Cliquez sur **"Enregistrer et continuer"**
6. **Scopes** : Cliquez sur **"Ajouter ou supprimer des scopes"**
   - Ajoutez : `.../auth/userinfo.email`
   - Ajoutez : `.../auth/userinfo.profile`
   - Ajoutez : `openid`
7. Cliquez sur **"Enregistrer et continuer"**
8. **Utilisateurs test** : Ajoutez votre email pour tester
9. Cliquez sur **"Enregistrer et continuer"**
10. Vérifiez le résumé et cliquez sur **"Retour au tableau de bord"**

### Étape 4 : Créer les identifiants OAuth 2.0

1. Allez dans **"APIs et services"** > **"Identifiants"**
2. Cliquez sur **"Créer des identifiants"** > **"ID client OAuth"**
3. Type d'application : **"Application Web"**
4. Nommez-le : `VintApp Web Client`
5. **Origines JavaScript autorisées** :
   - Développement : `http://localhost:8000`
   - Production : `https://votre-domaine.com`
6. **URI de redirection autorisés** :
   - Développement : `http://localhost:8000/auth/google/callback`
   - Production : `https://votre-domaine.com/auth/google/callback`
7. Cliquez sur **"Créer"**
8. Une popup s'affiche avec **Client ID** et **Client Secret** → **COPIEZ-LES !**

## 🔐 Configuration de l'application Laravel

### Étape 5 : Ajouter les variables d'environnement

Ouvrez le fichier `.env` et ajoutez ces lignes :

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=votre_client_id_ici.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=votre_client_secret_ici
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

**Exemple avec vraies valeurs :**
```env
GOOGLE_CLIENT_ID=123456789-abc123def456.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-AbCdEfGhIjKlMnOpQrStUvWx
GOOGLE_REDIRECT_URI="http://localhost:8000/auth/google/callback"
```

### Étape 6 : Vider le cache de configuration

```bash
php artisan config:clear
php artisan cache:clear
```

## 🧪 Tests

### Test 1 : Page de connexion

1. Allez sur : `http://localhost:8000/login`
2. Vérifiez que le bouton **"Se connecter avec Google"** apparaît
3. Le bouton doit avoir le logo Google coloré

### Test 2 : Redirection vers Google

1. Cliquez sur **"Se connecter avec Google"**
2. Vous devriez être redirigé vers la page de consentement Google
3. Sélectionnez votre compte Google de test
4. Acceptez les permissions

### Test 3 : Callback et connexion

1. Après avoir accepté, vous devriez être redirigé vers `/dashboard`
2. Un message de succès s'affiche : **"Connexion avec Google réussie !"**
3. Vérifiez que vous êtes connecté (nom d'utilisateur en haut à droite)

### Test 4 : Vérification en base de données

```sql
SELECT id, name, email, google_id, avatar_url, email_verified_at 
FROM users 
WHERE google_id IS NOT NULL;
```

Vous devriez voir :
- `google_id` rempli avec un nombre long (ex: `1234567890123456789`)
- `email_verified_at` avec une date (auto-vérifié)
- `avatar_url` avec l'URL de votre photo Google

### Test 5 : Reconnecter avec le même compte

1. Déconnectez-vous
2. Cliquez à nouveau sur **"Se connecter avec Google"**
3. Sélectionnez le même compte
4. Devrait reconnecter sans créer de nouveau compte

## ❌ Résolution de problèmes

### Erreur : "redirect_uri_mismatch"

**Cause** : L'URI de redirection n'est pas autorisée dans Google Cloud Console.

**Solution** :
1. Allez dans Google Cloud Console > Identifiants
2. Cliquez sur votre client OAuth
3. Vérifiez que `http://localhost:8000/auth/google/callback` est dans la liste
4. Ajoutez-le si absent et sauvegardez

### Erreur : "Access blocked: This app's request is invalid"

**Cause** : Scopes OAuth mal configurés.

**Solution** :
1. Allez dans Écran de consentement OAuth
2. Vérifiez que ces scopes sont ajoutés :
   - `.../auth/userinfo.email`
   - `.../auth/userinfo.profile`
   - `openid`

### Erreur : "Client ID not found"

**Cause** : Variables d'environnement mal copiées.

**Solution** :
1. Vérifiez `.env` → `GOOGLE_CLIENT_ID` et `GOOGLE_CLIENT_SECRET`
2. Pas d'espaces avant/après les valeurs
3. Pas de guillemets autour des valeurs (sauf pour REDIRECT_URI)
4. Lancez : `php artisan config:clear`

### Erreur : "Email already exists"

**Cause** : Un compte existe déjà avec cet email (inscription manuelle).

**Solution** : Le code actuel met à jour l'utilisateur existant et ajoute le `google_id`. Pas de problème.

### Erreur : "CSRF token mismatch" après callback

**Cause** : Session expirée pendant le processus OAuth.

**Solution** :
1. Videz les cookies du navigateur
2. Réessayez
3. Vérifiez `config/session.php` → `'same_site' => 'lax'`

## 🔒 Sécurité

### Bonnes pratiques implémentées

✅ **Tokens sensibles cachés** : `google_token` et `google_refresh_token` sont dans `$hidden` du modèle User

✅ **Email auto-vérifié** : Les comptes Google sont automatiquement vérifiés (`email_verified_at`)

✅ **Mot de passe aléatoire** : Les comptes Google reçoivent un mot de passe aléatoire de 24 caractères

✅ **Remember me** : `Auth::login($user, true)` garde l'utilisateur connecté

✅ **Try-catch** : Toutes les exceptions OAuth sont capturées et l'utilisateur est redirigé avec un message d'erreur

### Recommandations supplémentaires

🔐 **Activer HTTPS en production** :
```env
APP_ENV=production
APP_URL=https://votre-domaine.com
```

🔐 **Restreindre les utilisateurs de test** :
- En mode "Test", seulement les emails ajoutés dans Google Cloud Console peuvent se connecter
- Passez en mode "Production" après vérification complète de l'app

🔐 **Rotation des secrets** :
- Changez `GOOGLE_CLIENT_SECRET` tous les 6 mois
- Révocation : Google Cloud Console > Identifiants > Supprimer l'ancien client

## 📊 Flux OAuth complet

```
1. User clicks "Se connecter avec Google"
   ↓
2. GET /auth/google → GoogleAuthController::redirectToGoogle()
   ↓
3. Redirect to Google OAuth consent screen
   ↓
4. User selects account and grants permissions
   ↓
5. Google redirects to: /auth/google/callback?code=...
   ↓
6. GET /auth/google/callback → GoogleAuthController::handleGoogleCallback()
   ↓
7. Exchange code for access token via Socialite
   ↓
8. Fetch user info from Google (email, name, ID, avatar)
   ↓
9. Check if user exists by email:
   - YES → Update google_id, tokens, avatar
   - NO → Create new user with Google data
   ↓
10. Log in user: Auth::login($user, true)
    ↓
11. Redirect to /dashboard with success message
```

## 🎨 Personnalisation UI

Les boutons Google ont été ajoutés avec :
- Logo Google officiel (SVG coloré)
- Style cohérent avec Bootstrap 5
- Séparateur "OU" pour séparer login classique et OAuth
- Responsive design

**Fichiers modifiés :**
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`

## 📚 Références

- [Laravel Socialite Documentation](https://laravel.com/docs/11.x/socialite)
- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Google Cloud Console](https://console.cloud.google.com/)
- [Google People API](https://developers.google.com/people)

## 🚀 Déploiement en production

### Checklist avant déploiement

- [ ] Changer `GOOGLE_REDIRECT_URI` vers l'URL de production
- [ ] Ajouter le domaine de production dans "Origines JavaScript autorisées"
- [ ] Ajouter l'URI de callback de production dans "URI de redirection autorisés"
- [ ] Passer l'écran de consentement OAuth en mode "Production" (après vérification Google)
- [ ] Activer HTTPS (`APP_URL=https://...`)
- [ ] Vérifier que le certificat SSL est valide
- [ ] Tester le flux complet sur production

### Commandes de déploiement

```bash
# Sur le serveur de production
php artisan config:clear
php artisan cache:clear
php artisan migrate --force
php artisan optimize
```

## 📞 Support

En cas de problème, vérifiez :
1. Les logs Laravel : `storage/logs/laravel.log`
2. Les logs Google Cloud Console : APIs et services > Tableau de bord
3. La console du navigateur (F12) pour les erreurs JavaScript

---

**Dernière mise à jour** : 10 janvier 2025
**Version Laravel** : 11.x
**Version Socialite** : 5.23.0
