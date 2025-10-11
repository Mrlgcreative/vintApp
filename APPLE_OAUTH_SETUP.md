# 🍎 Configuration Apple Sign In - Guide Complet

## 📋 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Prérequis](#prérequis)
3. [Configuration Apple Developer](#configuration-apple-developer)
4. [Configuration Laravel](#configuration-laravel)
5. [Tests](#tests)
6. [Dépannage](#dépannage)

---

## 🎯 Vue d'ensemble

L'authentification Apple (Sign in with Apple) est maintenant intégrée à VintApp. Les utilisateurs peuvent :
- ✅ Se connecter avec leur Apple ID
- ✅ Créer un compte automatiquement
- ✅ Lier leur compte Apple à un compte existant
- ✅ Email vérifié automatiquement

### 🔧 Technologies installées

```json
{
  "socialiteproviders/apple": "^5.7.0",
  "socialiteproviders/manager": "^4.8.1",
  "lcobucci/jwt": "^5.5.0",
  "lcobucci/clock": "^3.4.0"
}
```

---

## ✅ Prérequis

### 1. Compte Apple Developer

- Compte Apple Developer (99$/an)
- Accès à [Apple Developer Portal](https://developer.apple.com/account)

### 2. Application enregistrée

- App ID configuré
- Service ID pour Sign in with Apple
- Private Key (.p8) téléchargée

---

## 🛠️ Configuration Apple Developer

### Étape 1 : Créer un App ID

1. **Aller sur** : [Certificates, Identifiers & Profiles](https://developer.apple.com/account/resources/identifiers/list)

2. **Cliquer sur** : `+` (nouveau identifiant)

3. **Sélectionner** : `App IDs`

4. **Configuration** :
   ```
   Description: VintApp
   Bundle ID: com.vintapp.app (Explicit)
   
   Capabilities:
   ✅ Sign in with Apple (Enabled)
   ```

5. **Sauvegarder**

### Étape 2 : Créer un Service ID

1. **Cliquer sur** : `+` (nouveau identifiant)

2. **Sélectionner** : `Services IDs`

3. **Configuration** :
   ```
   Description: VintApp Sign In
   Identifier: com.vintapp.app.signin
   ```

4. **Cocher** : `Sign in with Apple`

5. **Cliquer sur** : `Configure`

6. **Ajouter les domaines** :
   ```
   Primary App ID: com.vintapp.app
   
   Domains and Subdomains:
   - localhost (pour développement)
   - votredomaine.com (pour production)
   
   Return URLs:
   - http://localhost:8000/auth/apple/callback
   - https://votredomaine.com/auth/apple/callback
   ```

7. **Sauvegarder**

### Étape 3 : Créer une Private Key

1. **Aller sur** : [Keys](https://developer.apple.com/account/resources/authkeys/list)

2. **Cliquer sur** : `+` (nouvelle clé)

3. **Configuration** :
   ```
   Key Name: VintApp Sign In Key
   ✅ Sign in with Apple (Enabled)
   
   Configure: Sélectionner votre App ID (com.vintapp.app)
   ```

4. **Télécharger la clé** (.p8 file)
   - ⚠️ **IMPORTANT** : Vous ne pouvez télécharger qu'une seule fois !
   - Sauvegarder dans : `storage/app/apple/AuthKey_XXXXXXXXXX.p8`

5. **Noter** :
   - **Key ID** : Affiché après création (10 caractères)
   - **Team ID** : Dans le coin supérieur droit du portail

---

## ⚙️ Configuration Laravel

### 1. Fichier .env

Ajouter les variables suivantes dans `.env` :

```env
# Apple OAuth
APPLE_CLIENT_ID=com.vintapp.app.signin
APPLE_CLIENT_SECRET=
APPLE_REDIRECT_URI="${APP_URL}/auth/apple/callback"
APPLE_TEAM_ID=XXXXXXXXXX
APPLE_KEY_ID=XXXXXXXXXX
APPLE_PRIVATE_KEY=storage/app/apple/AuthKey_XXXXXXXXXX.p8
```

### 2. Générer le Client Secret

Apple nécessite un JWT comme client secret. Vous avez deux options :

#### Option A : Utiliser le chemin du fichier .p8

```env
APPLE_PRIVATE_KEY=storage/app/apple/AuthKey_XXXXXXXXXX.p8
```

#### Option B : Utiliser le contenu de la clé directement

```env
APPLE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----
MIGTAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBHkwdwIBAQQg...
-----END PRIVATE KEY-----"
```

### 3. Variables requises

| Variable | Description | Exemple |
|----------|-------------|---------|
| `APPLE_CLIENT_ID` | Service ID créé | `com.vintapp.app.signin` |
| `APPLE_TEAM_ID` | Team ID (10 caractères) | `A1B2C3D4E5` |
| `APPLE_KEY_ID` | Key ID de la private key | `X1Y2Z3W4V5` |
| `APPLE_PRIVATE_KEY` | Chemin ou contenu de la clé | `storage/app/apple/AuthKey_...` |

---

## 📁 Structure des fichiers

```
vintapp/
├── app/
│   ├── Http/Controllers/Auth/
│   │   ├── GoogleAuthController.php  ✅
│   │   └── AppleAuthController.php   ✅ NOUVEAU
│   ├── Models/
│   │   └── User.php                  ✅ (apple_id ajouté)
│   └── Providers/
│       └── AppServiceProvider.php    ✅ (listener Apple)
├── database/migrations/
│   └── 2025_10_10_140941_add_apple_oauth_to_users_table.php ✅
├── resources/views/auth/
│   ├── login.blade.php               ✅ (bouton Apple)
│   └── register.blade.php            ✅ (bouton Apple)
├── routes/
│   └── auth.php                      ✅ (routes Apple)
├── storage/app/apple/
│   └── AuthKey_XXXXXXXXXX.p8         📝 À ajouter
└── .env                              📝 À configurer
```

---

## 🧪 Tests

### 1. Test visuel

1. Démarrer le serveur :
   ```bash
   php artisan serve
   ```

2. Ouvrir : http://localhost:8000/login

3. **Vérifier** :
   - ✅ Bouton "Se connecter avec Apple" visible (noir)
   - ✅ Logo Apple blanc sur fond noir
   - ✅ Bouton responsive

### 2. Test de connexion

1. **Cliquer** sur "Se connecter avec Apple"

2. **Résultat attendu** :
   - Redirection vers Apple ID
   - Page de connexion Apple
   - Demande de permission (première fois)

3. **Après authentification** :
   - Redirection vers `/dashboard`
   - Message : "Connexion réussie avec Apple !"
   - Utilisateur créé ou lié

### 3. Vérifier la base de données

```sql
SELECT id, name, email, apple_id, email_verified_at 
FROM users 
WHERE apple_id IS NOT NULL;
```

**Résultat attendu** :
```
| id | name           | email                  | apple_id      | email_verified_at    |
|----|----------------|------------------------|---------------|----------------------|
| 5  | John Doe       | john@icloud.com        | 001234.abc... | 2025-01-10 14:30:00 |
```

---

## 🐛 Dépannage

### Erreur : "Invalid client"

**Cause** : `APPLE_CLIENT_ID` incorrect ou Service ID non configuré

**Solution** :
1. Vérifier le Service ID dans Apple Developer
2. Vérifier `APPLE_CLIENT_ID` dans `.env`
3. Exécuter : `php artisan config:clear`

### Erreur : "Invalid redirect URI"

**Cause** : URI de redirection non configurée dans Apple Developer

**Solution** :
1. Aller dans Service ID > Configure
2. Ajouter : `http://localhost:8000/auth/apple/callback`
3. Pour production : `https://votredomaine.com/auth/apple/callback`

### Erreur : "Invalid JWT"

**Cause** : Problème avec la Private Key ou les IDs

**Solution** :
1. Vérifier `APPLE_TEAM_ID` (10 caractères)
2. Vérifier `APPLE_KEY_ID` (10 caractères)
3. Vérifier le chemin vers la clé `.p8`
4. Vérifier que le fichier `.p8` existe et est lisible

### Apple ne retourne pas le nom

**Cause** : Apple envoie le nom uniquement lors de la **première connexion**

**Solution** :
- Le nom est stocké lors de la première connexion
- Connexions suivantes : Le nom n'est plus envoyé
- Fallback dans le code : `'Utilisateur Apple'` si nom absent

### Erreur : "Email already exists"

**Comportement** : Le système lie automatiquement le compte Apple à l'email existant

**Vérification** :
```php
$user = User::where('email', 'john@icloud.com')->first();
echo $user->apple_id; // Doit afficher l'Apple ID
```

---

## 🔒 Sécurité

### Bonnes pratiques

1. **Private Key** :
   - ✅ Stocker dans `storage/app/apple/`
   - ✅ Ajouter `storage/app/apple/` au `.gitignore`
   - ❌ Ne jamais commiter la clé dans Git

2. **Variables d'environnement** :
   - ✅ Utiliser `.env` pour les secrets
   - ✅ Ne jamais exposer les IDs/Keys publiquement
   - ✅ Utiliser des variables différentes pour dev/prod

3. **HTTPS en production** :
   - ✅ Apple requiert HTTPS en production
   - ✅ Configurer SSL/TLS sur votre serveur
   - ✅ Mettre à jour le redirect URI

---

## 📊 Statistiques

### Base de données

```sql
-- Nombre d'utilisateurs Apple
SELECT COUNT(*) as total_apple_users 
FROM users 
WHERE apple_id IS NOT NULL;

-- Utilisateurs avec Google ET Apple
SELECT COUNT(*) as multi_oauth_users 
FROM users 
WHERE google_id IS NOT NULL 
  AND apple_id IS NOT NULL;

-- Taux d'adoption OAuth
SELECT 
  COUNT(*) as total_users,
  SUM(CASE WHEN google_id IS NOT NULL THEN 1 ELSE 0 END) as google_users,
  SUM(CASE WHEN apple_id IS NOT NULL THEN 1 ELSE 0 END) as apple_users,
  SUM(CASE WHEN google_id IS NULL AND apple_id IS NULL THEN 1 ELSE 0 END) as email_users
FROM users;
```

---

## 🚀 Production

### Checklist avant déploiement

- [ ] Private Key (.p8) téléchargée et sécurisée
- [ ] Variables `.env` configurées (production)
- [ ] Service ID configuré avec domaine de production
- [ ] HTTPS activé sur le serveur
- [ ] Redirect URI mis à jour dans Apple Developer
- [ ] Test de connexion en production
- [ ] Vérification des logs Laravel

### Variables de production

```env
APP_URL=https://votredomaine.com

APPLE_CLIENT_ID=com.vintapp.app.signin
APPLE_TEAM_ID=XXXXXXXXXX
APPLE_KEY_ID=XXXXXXXXXX
APPLE_PRIVATE_KEY=storage/app/apple/AuthKey_XXXXXXXXXX.p8
APPLE_REDIRECT_URI="${APP_URL}/auth/apple/callback"
```

---

## 📚 Ressources

- [Apple Sign in Documentation](https://developer.apple.com/sign-in-with-apple/)
- [Socialite Apple Provider](https://github.com/SocialiteProviders/Apple)
- [Laravel Socialite](https://laravel.com/docs/11.x/socialite)
- [Apple Developer Portal](https://developer.apple.com/account)

---

## ✅ Résumé

### Ce qui fonctionne

- ✅ Package Socialite Apple installé (v5.7.0)
- ✅ Migration `apple_id` créée et exécutée
- ✅ AppleAuthController créé avec logique complète
- ✅ Routes configurées (`/auth/apple` et callback)
- ✅ Boutons Apple ajoutés (login + register)
- ✅ Event listener configuré dans AppServiceProvider
- ✅ Model User mis à jour (fillable)
- ✅ Cache nettoyé

### Ce qu'il reste à faire

1. **Configuration Apple Developer** :
   - Créer App ID
   - Créer Service ID
   - Générer et télécharger Private Key (.p8)
   - Configurer les domaines et redirect URIs

2. **Configuration .env** :
   - Ajouter `APPLE_CLIENT_ID`
   - Ajouter `APPLE_TEAM_ID`
   - Ajouter `APPLE_KEY_ID`
   - Ajouter `APPLE_PRIVATE_KEY`

3. **Tests** :
   - Tester la connexion Apple
   - Vérifier la création d'utilisateur
   - Vérifier la liaison de comptes

---

**🎉 L'intégration Apple OAuth est prête côté code ! Il ne reste que la configuration Apple Developer.**

📧 Support : support@vintapp.com
