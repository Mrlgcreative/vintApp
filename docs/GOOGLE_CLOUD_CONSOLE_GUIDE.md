# 🎯 Guide Visuel : Configuration Google Cloud Console

## 📍 Navigation Google Cloud Console

### Étape 1 : Accéder à Google Cloud Console
**URL** : https://console.cloud.google.com/

### Étape 2 : Créer un projet
```
1. Cliquez sur le sélecteur de projet en haut (à côté de "Google Cloud")
2. Cliquez sur "NOUVEAU PROJET"
3. Nom du projet : VintApp
4. Organisation : Laisser "Aucune organisation"
5. Cliquez sur "CRÉER"
6. Attendez 10-15 secondes
7. Sélectionnez le projet créé
```

### Étape 3 : Menu de navigation
```
☰ Menu hamburger (en haut à gauche)
└── APIs et services
    ├── Écran de consentement OAuth
    ├── Identifiants
    └── Bibliothèque
```

### Étape 4 : Configurer l'écran de consentement OAuth
```
☰ Menu → APIs et services → Écran de consentement OAuth

1. Type d'utilisateur
   ○ Interne (pour Google Workspace uniquement)
   ● Externe (pour tout compte Google) ← SÉLECTIONNER CELUI-CI
   [CRÉER]

2. Informations de l'application
   Nom de l'application* : VintApp
   E-mail pour l'assistance utilisateur* : votre.email@example.com
   Logo de l'application : [Ignorer pour l'instant]
   
   Domaines de l'application :
   - Domaine de l'application : [Laisser vide en développement]
   - Page d'accueil de l'application : http://localhost:8000
   - Lien vers les règles de confidentialité : [Optionnel]
   - Lien vers les conditions d'utilisation : [Optionnel]
   
   Domaines autorisés : [Laisser vide]
   
   Informations de contact du développeur* : votre.email@example.com
   
   [ENREGISTRER ET CONTINUER]

3. Champs d'application (Scopes)
   [AJOUTER OU SUPPRIMER DES CHAMPS D'APPLICATION]
   
   Sélectionner ces 3 scopes :
   ☑ .../auth/userinfo.email (Voir l'adresse e-mail de votre compte Google)
   ☑ .../auth/userinfo.profile (Voir vos informations personnelles)
   ☑ openid (Associer à vos informations personnelles sur Google)
   
   [METTRE À JOUR]
   [ENREGISTRER ET CONTINUER]

4. Utilisateurs test
   [+ AJOUTER DES UTILISATEURS]
   
   Saisissez votre email : votre.email@example.com
   [AJOUTER]
   
   [ENREGISTRER ET CONTINUER]

5. Résumé
   Vérifiez toutes les informations
   [RETOUR AU TABLEAU DE BORD]
```

### Étape 5 : Créer les identifiants OAuth
```
☰ Menu → APIs et services → Identifiants

[+ CRÉER DES IDENTIFIANTS] → [ID client OAuth]

1. Type d'application
   ● Application Web ← SÉLECTIONNER

2. Nom*
   VintApp Web Client

3. Origines JavaScript autorisées
   [+ AJOUTER UN URI]
   URI 1 : http://localhost:8000
   [Optionnel] URI 2 : http://127.0.0.1:8000

4. URI de redirection autorisés*
   [+ AJOUTER UN URI]
   URI 1 : http://localhost:8000/auth/google/callback
   [Optionnel] URI 2 : http://127.0.0.1:8000/auth/google/callback

5. [CRÉER]

6. Popup "Client OAuth créé"
   📋 Votre ID client : 123456789-abc123def456.apps.googleusercontent.com
   📋 Votre code secret client : GOCSPX-AbCdEfGhIjKlMnOpQrStUvWx
   
   ⚠️ IMPORTANT : COPIEZ CES DEUX VALEURS IMMÉDIATEMENT !
   
   [TÉLÉCHARGER JSON] (optionnel, pour sauvegarde)
   [OK]
```

### Étape 6 : Vérifier les identifiants créés
```
☰ Menu → APIs et services → Identifiants

Vous devriez voir :

ID clients OAuth 2.0
┌─────────────────────────────────────────────────────────┐
│ Nom                    │ Type            │ Date création │
├─────────────────────────────────────────────────────────┤
│ VintApp Web Client     │ Application Web │ Aujourd'hui   │
└─────────────────────────────────────────────────────────┘

Cliquez sur "VintApp Web Client" pour voir/modifier :
- ID client : 123456789-abc123def456.apps.googleusercontent.com
- Code secret client : GOCSPX-... (caché, cliquez sur l'icône 👁 pour voir)
- Origines JavaScript autorisées : http://localhost:8000
- URI de redirection autorisés : http://localhost:8000/auth/google/callback
```

## 📝 Copier les identifiants dans .env

```env
# Dans votre fichier .env de Laravel
GOOGLE_CLIENT_ID=123456789-abc123def456.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-AbCdEfGhIjKlMnOpQrStUvWx
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

**⚠️ Remplacez les valeurs d'exemple par VOS vraies valeurs !**

## ✅ Vérification rapide

### Checklist de configuration
```
Google Cloud Console :
☑ Projet créé (VintApp)
☑ Écran de consentement configuré (Externe)
☑ Scopes ajoutés (email, profile, openid)
☑ Utilisateur test ajouté (votre email)
☑ Identifiants OAuth créés (Application Web)
☑ URI de redirection ajouté (http://localhost:8000/auth/google/callback)

Fichier .env :
☑ GOOGLE_CLIENT_ID rempli
☑ GOOGLE_CLIENT_SECRET rempli
☑ GOOGLE_REDIRECT_URI défini

Cache Laravel :
☑ php artisan config:clear exécuté
☑ php artisan cache:clear exécuté
```

## 🧪 Test de connexion

### Test manuel
```
1. Ouvrez : http://localhost:8000/login
2. Vérifiez : Bouton "Se connecter avec Google" visible
3. Cliquez : Sur le bouton Google
4. Attendu : Redirection vers accounts.google.com
5. Sélectionnez : Votre compte test
6. Acceptez : Les permissions demandées
7. Résultat : Redirection vers /dashboard avec succès
```

### Vérification en base de données
```sql
-- Dans votre base MySQL/SQLite
SELECT 
    id, 
    name, 
    email, 
    google_id, 
    avatar_url, 
    email_verified_at,
    created_at
FROM users 
WHERE google_id IS NOT NULL
ORDER BY created_at DESC
LIMIT 1;
```

**Résultat attendu :**
```
id: 1
name: Votre Nom Google
email: votre.email@example.com
google_id: 1234567890123456789 (nombre long)
avatar_url: https://lh3.googleusercontent.com/a/... (URL photo)
email_verified_at: 2025-01-10 10:30:00 (date actuelle)
created_at: 2025-01-10 10:30:00
```

## 🚨 Messages d'erreur courants

### 1. "Error 400: redirect_uri_mismatch"
```
❌ Problème : L'URI de redirection n'est pas autorisée

✅ Solution :
1. Allez dans Google Cloud Console → Identifiants
2. Cliquez sur "VintApp Web Client"
3. Vérifiez "URI de redirection autorisés"
4. Doit contenir : http://localhost:8000/auth/google/callback
5. Si absent, ajoutez-le et cliquez [ENREGISTRER]
6. Réessayez immédiatement (pas de délai)
```

### 2. "Error 403: access_denied"
```
❌ Problème : L'email de test n'est pas autorisé

✅ Solution :
1. Allez dans Écran de consentement OAuth
2. Cliquez sur "Utilisateurs test"
3. [+ AJOUTER DES UTILISATEURS]
4. Ajoutez votre email
5. [ENREGISTRER]
6. Réessayez immédiatement
```

### 3. "Error 401: invalid_client"
```
❌ Problème : Client ID ou Client Secret incorrect

✅ Solution :
1. Vérifiez votre .env :
   - GOOGLE_CLIENT_ID est bien copié (sans espaces)
   - GOOGLE_CLIENT_SECRET est bien copié (sans espaces)
2. Lancez : php artisan config:clear
3. Réessayez
```

### 4. "Error 400: admin_policy_enforced"
```
❌ Problème : Politique d'administration Google Workspace

✅ Solution :
Si vous utilisez un compte Google Workspace :
1. Contactez votre administrateur Google Workspace
2. Demandez d'autoriser les applications OAuth tierces
OU
3. Utilisez un compte Gmail personnel pour les tests
```

## 📊 Statut de l'écran de consentement

### Mode "Test" (par défaut)
```
État : 🟡 EN TEST
Limitations :
- Maximum 100 utilisateurs test
- Seuls les utilisateurs test peuvent se connecter
- Pas de vérification Google nécessaire
- Parfait pour le développement

Comment ajouter des utilisateurs :
Écran de consentement OAuth → Utilisateurs test → + AJOUTER
```

### Mode "Production" (après vérification)
```
État : 🟢 PRODUCTION
Avantages :
- Aucune limite d'utilisateurs
- Tout compte Google peut se connecter
- Application publique

Prérequis :
- Application entièrement fonctionnelle
- Politique de confidentialité publiée
- Conditions d'utilisation publiées
- Vérification par Google (1-2 semaines)
```

## 🔄 Rotation des secrets (sécurité)

### Renouveler le Client Secret
```
1. Allez dans Identifiants
2. Cliquez sur "VintApp Web Client"
3. En haut : [RÉINITIALISER LE CODE SECRET]
4. Confirmez
5. Copiez le nouveau secret
6. Mettez à jour .env immédiatement
7. Lancez : php artisan config:clear
8. Tous les utilisateurs devront se reconnecter
```

## 🌐 Déploiement en production

### Ajouter le domaine de production
```
☰ Menu → APIs et services → Identifiants
→ Cliquez sur "VintApp Web Client"

1. Origines JavaScript autorisées
   [+ AJOUTER UN URI]
   URI : https://votre-domaine.com

2. URI de redirection autorisés
   [+ AJOUTER UN URI]
   URI : https://votre-domaine.com/auth/google/callback

3. [ENREGISTRER]
```

### Mise à jour .env en production
```env
APP_URL=https://votre-domaine.com
GOOGLE_CLIENT_ID=123456789-abc123def456.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-AbCdEfGhIjKlMnOpQrStUvWx
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

## 📞 Support Google

### Ressources officielles
- Console : https://console.cloud.google.com/
- Documentation OAuth : https://developers.google.com/identity/protocols/oauth2
- Support : https://cloud.google.com/support

### Centre d'aide
```
Dans Google Cloud Console :
? (icône aide en haut à droite) → Centre d'aide → OAuth
```

## 🎯 Résumé visuel du flux

```
┌─────────────────────────────────────────────────────────┐
│                    GOOGLE CLOUD CONSOLE                  │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  1. Créer Projet : VintApp                              │
│                                                          │
│  2. Écran de consentement OAuth                         │
│     └─ Type : Externe                                   │
│     └─ Scopes : email, profile, openid                  │
│     └─ Utilisateurs test : votre.email@example.com      │
│                                                          │
│  3. Identifiants OAuth 2.0                              │
│     └─ Type : Application Web                           │
│     └─ Redirect URI : localhost:8000/auth/google/callback│
│     └─ Résultat :                                        │
│        ├─ Client ID : 123456789-abc...                  │
│        └─ Client Secret : GOCSPX-...                    │
│                                                          │
└─────────────────────────────────────────────────────────┘
                           ↓
                    COPIER dans .env
                           ↓
┌─────────────────────────────────────────────────────────┐
│                    LARAVEL APPLICATION                   │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  GOOGLE_CLIENT_ID=123456789-abc...                      │
│  GOOGLE_CLIENT_SECRET=GOCSPX-...                        │
│  GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"  │
│                                                          │
└─────────────────────────────────────────────────────────┘
                           ↓
              php artisan config:clear
                           ↓
                    TESTER SUR /login
```

---

**💡 Astuce** : Gardez l'onglet Google Cloud Console ouvert pendant le développement pour modifier rapidement les URI de redirection si besoin.

**🔖 Marque-page recommandé** : https://console.cloud.google.com/apis/credentials?project=vintapp

**⏱️ Temps de configuration estimé** : 15 minutes pour la première fois, 5 minutes pour les suivantes.
