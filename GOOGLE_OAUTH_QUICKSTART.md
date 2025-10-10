# 🚀 Guide Rapide : Authentification Google OAuth

## ⚡ Installation (DÉJÀ FAIT ✅)

Toute l'installation et le code sont déjà en place :
- ✅ Package Socialite installé
- ✅ Migration exécutée
- ✅ Routes configurées
- ✅ Controller créé
- ✅ Boutons ajoutés aux pages login/register

## 🔧 Configuration (À FAIRE)

### 1. Obtenir les identifiants Google (15 minutes)

1. **Créer un projet Google Cloud**
   - Allez sur : https://console.cloud.google.com/
   - Créez un nouveau projet : `VintApp`

2. **Configurer OAuth**
   - Menu : **APIs et services** > **Écran de consentement OAuth**
   - Type : **Externe**
   - Nom : `VintApp`
   - Email : Votre email
   - Scopes : `.../auth/userinfo.email`, `.../auth/userinfo.profile`, `openid`
   - Utilisateurs test : Ajoutez votre email

3. **Créer les identifiants**
   - Menu : **APIs et services** > **Identifiants**
   - **Créer des identifiants** > **ID client OAuth**
   - Type : **Application Web**
   - URI de redirection : `http://localhost:8000/auth/google/callback`
   - **COPIEZ** le Client ID et Client Secret

### 2. Configurer .env (2 minutes)

Ajoutez ces lignes à votre fichier `.env` :

```env
GOOGLE_CLIENT_ID=votre_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=votre_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

**Remplacez** `votre_client_id` et `votre_client_secret` par les valeurs copiées.

### 3. Vider le cache (30 secondes)

```bash
php artisan config:clear
php artisan cache:clear
```

## ✅ Test (2 minutes)

1. Allez sur : http://localhost:8000/login
2. Cliquez sur **"Se connecter avec Google"**
3. Sélectionnez votre compte Google de test
4. Acceptez les permissions
5. Vous devriez être connecté et redirigé vers `/dashboard`

## 🎯 Résumé

**Ce qui fonctionne maintenant :**
- Connexion avec Google depuis `/login`
- Inscription avec Google depuis `/register`
- Création automatique de compte si l'email n'existe pas
- Liaison du compte Google si l'email existe déjà
- Avatar Google récupéré et enregistré
- Email automatiquement vérifié

**Ce qui est créé en base de données :**
- `google_id` : L'ID Google de l'utilisateur
- `google_token` : Token d'accès (caché, sécurisé)
- `google_refresh_token` : Token de renouvellement (caché, sécurisé)
- `avatar_url` : URL de la photo de profil Google
- `email_verified_at` : Auto-rempli (compte vérifié)

## 📚 Documentation complète

Pour plus de détails (troubleshooting, sécurité, déploiement), consultez :
**`GOOGLE_OAUTH_SETUP.md`**

## 🆘 Problèmes fréquents

**Erreur : "redirect_uri_mismatch"**
→ Vérifiez que `http://localhost:8000/auth/google/callback` est bien ajouté dans Google Cloud Console

**Erreur : "Client ID not found"**
→ Vérifiez votre `.env` et lancez `php artisan config:clear`

**Erreur : "Access blocked"**
→ Ajoutez votre email dans les "Utilisateurs test" de l'écran de consentement OAuth

---

**Besoin d'aide ?** Consultez le guide complet `GOOGLE_OAUTH_SETUP.md` ou les logs dans `storage/logs/laravel.log`
