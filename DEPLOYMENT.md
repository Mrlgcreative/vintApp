ls -la public/ | grep storage# 🚀 Guide de Déploiement VintApp sur Hostinger

## ⚠️ NE PAS TOUT SUPPRIMER !

**Avant de supprimer quoi que ce soit**, sauvegardez :
- `.env` (configuration serveur)
- `storage/firebase/serviceAccountKey.json`
- `storage/app/public/` (fichiers uploadés)

---

## 📋 Étapes de Déploiement

### 1️⃣ Connexion SSH

```bash
ssh -p 65002 u282969929@141.136.39.194
```

### 2️⃣ Navigation vers le projet

```bash
cd domains/vitapp.mykenyastudentprocess.com/public_html
# OU
cd public_html
```

### 3️⃣ Initialiser Git (si pas encore fait)

```bash
# Vérifier si Git est déjà configuré
if [ ! -d ".git" ]; then
    echo "Initialisation de Git..."
    git init
    git remote add origin https://github.com/Mrlgcreative/vintApp.git
fi
```

### 4️⃣ Créer un token GitHub

1. Allez sur : https://github.com/settings/tokens
2. Cliquez **"Generate new token"** → **"Classic"**
3. Nom : `Hostinger Deploy`
4. Cochez : ☑️ `repo` (Full control of private repositories)
5. Cliquez **"Generate token"**
6. **COPIEZ LE TOKEN** (ex: `ghp_xxxxxxxxxxxxxxxxxxxx`)

### 5️⃣ Configurer le remote avec token

```bash
# Remplacer VOTRE_TOKEN par le token copié
git remote set-url origin https://VOTRE_TOKEN@github.com/Mrlgcreative/vintApp.git

# Exemple avec un vrai token :
# git remote set-url origin https://ghp_1A2B3C4D5E6F7G8H9I0J@github.com/Mrlgcreative/vintApp.git
```

### 6️⃣ Sauvegarde du .env (IMPORTANT !)

```bash
cp .env .env.backup
```

### 7️⃣ Déploiement automatique

```bash
# Récupérer la branche sky
git fetch origin sky

# Basculer sur sky (première fois)
git checkout -b sky origin/sky

# OU mettre à jour (si déjà sur sky)
git pull origin sky

# Rendre le script exécutable
chmod +x deploy.sh

# Exécuter le script de déploiement
./deploy.sh
```

### 8️⃣ Configuration admin_role

```bash
# Mettre à jour votre compte en admin
mysql -u u282969929 -p u282969929_vintapp -e "UPDATE users SET admin_role = 1 WHERE email = 'gloirelumingu10@gmail.com';"
```

### 9️⃣ Vérifications

```bash
# Vérifier que Firebase existe
ls -la storage/firebase/serviceAccountKey.json

# Vérifier les permissions
ls -la storage/

# Vérifier que Git est bien configuré
git remote -v
git branch
```

---

## 🔧 Commandes utiles

### Vider les caches manuellement
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Vérifier la base de données
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

### Consulter les logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📁 Structure des fichiers importants

```
public_html/
├── .env                    # ⚠️ Configuration production (NE PAS COMMITER)
├── deploy.sh              # Script de déploiement
├── artisan                # CLI Laravel
├── storage/
│   ├── firebase/
│   │   └── serviceAccountKey.json  # ⚠️ Clés Firebase
│   └── app/public/        # Fichiers uploadés
└── public/
    └── storage -> ../storage/app/public  # Lien symbolique
```

---

## ❌ En cas de problème

### Erreur 500
1. Vérifier `.env` (DB_PASSWORD, APP_KEY, etc.)
2. Vider les caches: `./deploy.sh`
3. Consulter les logs: `tail storage/logs/laravel.log`

### Firebase ne fonctionne pas
1. Vérifier que `storage/firebase/serviceAccountKey.json` existe
2. Vérifier les variables FIREBASE_* dans `.env`

### Admin_role ne fonctionne pas
```bash
mysql -u u282969929 -p u282969929_vintapp
SELECT id, name, email, admin_role FROM users WHERE email = 'gloirelumingu10@gmail.com';
UPDATE users SET admin_role = 1 WHERE email = 'gloirelumingu10@gmail.com';
exit
```

---

## 🆘 Support

En cas de problème persistant :
1. Consultez `storage/logs/laravel.log`
2. Vérifiez les logs Hostinger dans le panel
3. Testez en local d'abord

---

**Dernière mise à jour:** 1 décembre 2025
