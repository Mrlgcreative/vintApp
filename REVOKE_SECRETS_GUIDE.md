# 🚨 GUIDE URGENT : RÉVOQUER LES SECRETS EXPOSÉS

**Date** : 10 octobre 2025  
**Priorité** : 🔴 CRITIQUE - À FAIRE IMMÉDIATEMENT  
**Temps estimé** : 30 minutes

---

## ⚠️ POURQUOI C'EST URGENT ?

Vos clés API et secrets sont actuellement **EXPOSÉS** dans le fichier `.env`. Si ce fichier a été commité dans Git ou partagé, ces clés peuvent être utilisées par des attaquants pour :
- ✉️ Envoyer des emails depuis votre compte Gmail
- 🔐 Créer de faux comptes avec Google OAuth
- 💳 Effectuer des transactions M-Pesa frauduleuses
- 🤖 Utiliser votre quota OpenAI (coûts $$)

---

## 🔑 SECRETS À RÉVOQUER

### 1. Google OAuth Client Secret
```
Clé exposée : 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
```

### 2. Gmail App Password
```
Mot de passe exposé : jbkf pvwt gzeo usel
```

### 3. M-Pesa API Key
```
Clé exposée : azo6gOxne9fgKzTwnahiX5ppUQGKRBsE
```

### 4. OpenAI API Key
```
Clé exposée : sk-proj-eVp-p3Q178NusHANSgdKyA2...
```

---

## 📋 ACTIONS IMMÉDIATES

### ✅ ÉTAPE 1 : Google OAuth Client Secret (10 min)

1. **Ouvrir Google Cloud Console**
   ```
   https://console.cloud.google.com/apis/credentials
   ```

2. **Se connecter** avec le compte du projet VintApp

3. **Localiser l'identifiant OAuth 2.0**
   - Chercher "VintApp" ou le nom de votre projet
   - Ou chercher l'ID client qui commence par votre Client ID

4. **Supprimer l'identifiant actuel**
   - Cliquer sur les 3 points (⋮) à droite
   - Sélectionner "Supprimer"
   - Confirmer la suppression
   
   ⚠️ **ATTENTION** : Cela cassera temporairement la connexion Google OAuth

5. **Créer un nouvel identifiant**
   - Cliquer sur "+ CRÉER DES IDENTIFIANTS"
   - Sélectionner "ID client OAuth"
   - Type d'application : "Application Web"
   - Nom : "VintApp OAuth 2.0"
   - URI de redirection autorisés :
     ```
     https://uncomely-uneffusing-averie.ngrok-free.dev/auth/google/callback
     http://localhost:8000/auth/google/callback
     ```

6. **Copier les nouvelles credentials**
   - Client ID : `XXXXX.apps.googleusercontent.com`
   - Client Secret : `GOCSPX-XXXXXXXXXXXXXXX`

7. **Mettre à jour `.env`**
   ```env
   GOOGLE_CLIENT_ID=VOTRE_NOUVEAU_CLIENT_ID
   GOOGLE_CLIENT_SECRET=VOTRE_NOUVEAU_CLIENT_SECRET
   ```

8. **Redémarrer le serveur**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

---

### ✅ ÉTAPE 2 : Gmail App Password (5 min)

1. **Ouvrir Google Account**
   ```
   https://myaccount.google.com/apppasswords
   ```

2. **Se connecter** avec gloirelumingu10@gmail.com

3. **Révoquer le mot de passe actuel**
   - Chercher "jbkf pvwt gzeo usel" dans la liste
   - Cliquer sur "Supprimer" ou l'icône de corbeille
   - Confirmer la suppression

4. **Créer un nouveau mot de passe d'application**
   - Cliquer sur "Générer"
   - Nom : "VintApp Laravel"
   - Cliquer sur "Créer"
   
5. **Copier le nouveau mot de passe**
   - Format : `xxxx xxxx xxxx xxxx` (16 caractères avec espaces)
   - ⚠️ Notez-le immédiatement, il ne sera plus visible

6. **Mettre à jour `.env`**
   ```env
   MAIL_PASSWORD="xxxx xxxx xxxx xxxx"
   ```

7. **Tester l'envoi d'email**
   ```bash
   php artisan tinker
   ```
   ```php
   Mail::raw('Test email', function($msg) {
       $msg->to('gloirelumingu10@gmail.com')->subject('Test');
   });
   ```

---

### ✅ ÉTAPE 3 : M-Pesa API Key (10 min)

1. **Se connecter au portail M-Pesa Developer**
   ```
   https://developer.mpesa.vm.co.mz/
   ou
   https://developer.safaricom.co.ke/ (si Kenya)
   ```

2. **Aller dans "API Keys" ou "Applications"**

3. **Révoquer la clé actuelle**
   - Chercher `azo6gOxne9fgKzTwnahiX5ppUQGKRBsE`
   - Cliquer sur "Revoke" ou "Delete"
   - Confirmer

4. **Générer une nouvelle clé**
   - Cliquer sur "Generate API Key" ou "Create Application"
   - Nom : "VintApp Production"
   - Type : "Payment Gateway" ou "Web Application"
   - Callback URL : `https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/mpesa`

5. **Copier les nouvelles credentials**
   - API Key : `NOUVELLE_CLE_ICI`
   - API Secret : `NOUVEAU_SECRET_ICI`
   - Consumer Key (si applicable)
   - Consumer Secret (si applicable)

6. **Mettre à jour `.env`**
   ```env
   MPESA_API_KEY=NOUVELLE_CLE_ICI
   MPESA_API_SECRET=NOUVEAU_SECRET_ICI
   MPESA_CONSUMER_KEY=SI_APPLICABLE
   MPESA_CONSUMER_SECRET=SI_APPLICABLE
   ```

---

### ✅ ÉTAPE 4 : OpenAI API Key (5 min)

1. **Ouvrir OpenAI Platform**
   ```
   https://platform.openai.com/api-keys
   ```

2. **Se connecter** avec votre compte OpenAI

3. **Révoquer la clé actuelle**
   - Chercher la clé qui commence par `sk-proj-eVp-p3Q...`
   - Cliquer sur l'icône de corbeille (🗑️)
   - Confirmer "Revoke key"

4. **Créer une nouvelle clé**
   - Cliquer sur "+ Create new secret key"
   - Nom : "VintApp Production"
   - Permissions : "All" ou "Restricted" selon vos besoins
   - Cliquer sur "Create secret key"

5. **Copier la nouvelle clé**
   - Format : `sk-proj-XXXXXXXXXXXXXXXXXX`
   - ⚠️ Copier immédiatement, elle ne sera plus visible

6. **Mettre à jour `.env`**
   ```env
   OPENAI_API_KEY=sk-proj-VOTRE_NOUVELLE_CLE_ICI
   ```

7. **Tester la connexion** (optionnel)
   ```bash
   curl https://api.openai.com/v1/models \
     -H "Authorization: Bearer sk-proj-VOTRE_NOUVELLE_CLE"
   ```

---

## 🔒 ÉTAPE 5 : VÉRIFIER QUE .ENV N'EST PAS DANS GIT (CRITIQUE)

```bash
# Vérifier si .env a déjà été commité
git log --all --full-history -- .env
```

**Si la commande affiche des commits** :

```bash
# 🚨 DANGER : .env est dans l'historique Git !
# Le supprimer de TOUT l'historique

# Méthode 1 : BFG Repo-Cleaner (recommandé)
# Télécharger : https://reclaimtheweb.org/download-bfg-repo-cleaner/
java -jar bfg.jar --delete-files .env
git reflog expire --expire=now --all
git gc --prune=now --aggressive

# Méthode 2 : git filter-branch
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all

git reflog expire --expire=now --all
git gc --prune=now --aggressive

# Forcer le push (⚠️ ATTENTION : réécrit l'historique)
git push origin --force --all
git push origin --force --tags
```

**Si aucun commit trouvé** : ✅ Bon, .env n'a jamais été commité

---

## 🛡️ ÉTAPE 6 : VÉRIFIER .GITIGNORE

```bash
# Vérifier que .env est bien exclu
cat .gitignore | grep .env
```

**Devrait afficher** :
```
.env
.env.backup
.env.production
```

**Si .env n'est PAS dans .gitignore** :

```bash
# Ajouter .env à .gitignore
echo ".env" >> .gitignore
echo ".env.backup" >> .gitignore
echo ".env.production" >> .gitignore

# Supprimer .env du cache Git
git rm --cached .env

# Commiter
git add .gitignore
git commit -m "fix: Exclure .env de Git pour sécurité"
git push
```

---

## 📝 CHECKLIST DE VALIDATION

Après avoir terminé toutes les étapes :

- [ ] Google Client Secret révoqué et nouveau créé
- [ ] Gmail App Password révoqué et nouveau créé
- [ ] M-Pesa API Key révoquée et nouvelle créée
- [ ] OpenAI API Key révoquée et nouvelle créée
- [ ] `.env` mis à jour avec toutes les nouvelles clés
- [ ] `.env` n'est PAS dans l'historique Git
- [ ] `.env` est bien dans `.gitignore`
- [ ] Serveur redémarré (`php artisan config:clear`)
- [ ] Google OAuth testé (connexion fonctionne)
- [ ] Email testé (envoi fonctionne)
- [ ] M-Pesa testé (callback reçu)
- [ ] OpenAI testé (API répond)

---

## 🧪 TESTER TOUTES LES INTÉGRATIONS

### Test Google OAuth
```
1. Aller sur http://localhost:8000/login
2. Cliquer sur "Se connecter avec Google"
3. Vérifier que la connexion fonctionne
```

### Test Gmail
```bash
php artisan tinker

Mail::raw('Test après changement de mot de passe', function($msg) {
    $msg->to('gloirelumingu10@gmail.com')->subject('Test VintApp');
});
```

### Test M-Pesa
```
1. Initier un paiement test
2. Vérifier que le callback arrive
3. Vérifier les logs : tail -f storage/logs/laravel.log
```

### Test OpenAI
```bash
curl https://api.openai.com/v1/models \
  -H "Authorization: Bearer VOTRE_NOUVELLE_CLE"
```

---

## 🔔 NOTIFICATIONS DE SÉCURITÉ

Après avoir révoqué les clés :

1. **Surveiller les logs des services**
   - Google Cloud Console : Logs → Audit Logs
   - Gmail : Activité du compte
   - M-Pesa Dashboard : Transaction logs
   - OpenAI Platform : Usage logs

2. **Configurer des alertes**
   - Google : Alertes de sécurité activées
   - Gmail : Notifications de connexion suspecte
   - OpenAI : Limite de dépenses configurée

---

## 📚 RESSOURCES

- [Google Cloud Console](https://console.cloud.google.com/)
- [Google App Passwords](https://myaccount.google.com/apppasswords)
- [M-Pesa Developer Portal](https://developer.mpesa.vm.co.mz/)
- [OpenAI Platform](https://platform.openai.com/)
- [BFG Repo-Cleaner](https://reclaimtheweb.org/download-bfg-repo-cleaner/)

---

## ⏱️ TEMPS ESTIMÉ TOTAL

| Tâche | Durée |
|-------|-------|
| Google OAuth | 10 min |
| Gmail | 5 min |
| M-Pesa | 10 min |
| OpenAI | 5 min |
| Vérifier Git | 5 min |
| Tests | 10 min |
| **TOTAL** | **45 min** |

---

## 🆘 BESOIN D'AIDE ?

Si vous rencontrez des problèmes :
- Email : gloirelumingu10@gmail.com
- Vérifier les logs : `tail -f storage/logs/laravel.log`

---

**✅ UNE FOIS TERMINÉ** :
1. Cocher tous les items de la checklist
2. Tester toutes les intégrations
3. Surveiller les logs pendant 24h
4. Configurer des alertes de sécurité

**⏰ À FAIRE MAINTENANT - NE PAS REPORTER !**
