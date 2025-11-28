# 📧 Guide de Configuration Email - VintApp

## ❌ **Problème Actuel**

Vous n'avez pas reçu l'email de vérification après l'inscription.

## 🔍 **Diagnostic**

### Configuration actuelle (`.env`) :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=gloirelumingu10@gmail.com
MAIL_PASSWORD="jbkf pvwt gzeo usel"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=gloirelumingu10@gmail.com
MAIL_FROM_NAME=Vintapp
```

**Problème** : Gmail SMTP a des restrictions strictes et peut bloquer l'envoi d'emails.

---

## ✅ **Solutions**

### **Solution 1 : Utiliser Mailtrap (RECOMMANDÉ pour le développement)**

Mailtrap est un service de test d'emails qui capture tous les emails envoyés sans les envoyer réellement.

#### Étapes :

1. **Créer un compte Mailtrap** :
   - Aller sur https://mailtrap.io
   - S'inscrire gratuitement
   - Créer un "Inbox"

2. **Copier les identifiants SMTP** dans votre `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username_mailtrap
MAIL_PASSWORD=votre_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vintapp.com
MAIL_FROM_NAME="VintApp"
```

3. **Vider les caches Laravel** :
```bash
php artisan config:clear
php artisan cache:clear
```

4. **Tester l'envoi** :
```bash
php artisan email:test-verification skyboard250@gmail.com
```

5. **Vérifier dans Mailtrap** :
   - Aller dans votre inbox Mailtrap
   - L'email apparaîtra immédiatement

---

### **Solution 2 : Corriger Gmail SMTP (pour production)**

Si vous voulez vraiment utiliser Gmail :

#### Étapes :

1. **Activer l'authentification à 2 facteurs** sur votre compte Gmail `gloirelumingu10@gmail.com`

2. **Créer un "Mot de passe d'application"** :
   - Aller sur https://myaccount.google.com/security
   - Cliquer sur "Mots de passe d'application"
   - Générer un nouveau mot de passe pour "Mail"
   - Copier le mot de passe généré (16 caractères)

3. **Mettre à jour votre `.env`** :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=gloirelumingu10@gmail.com
MAIL_PASSWORD="votre_nouveau_mot_de_passe_application"  # ⚠️ 16 caractères sans espaces
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=gloirelumingu10@gmail.com
MAIL_FROM_NAME="VintApp"
```

4. **Vider les caches** :
```bash
php artisan config:clear
php artisan cache:clear
```

5. **Tester** :
```bash
php artisan email:test-verification skyboard250@gmail.com
```

---

### **Solution 3 : Utiliser Log (pour déboguer)**

Temporairement, enregistrez les emails dans les logs au lieu de les envoyer :

```env
MAIL_MAILER=log
```

Les emails seront visibles dans `storage/logs/laravel.log`.

---

## 🧪 **Tester l'Envoi d'Email**

### Commande créée pour vous :

```bash
# Tester avec le dernier utilisateur non vérifié
php artisan email:test-verification

# Tester avec un email spécifique
php artisan email:test-verification skyboard250@gmail.com
```

---

## 📋 **Checklist de Vérification**

Après avoir changé la configuration :

- [ ] Modifier le `.env` avec les bons identifiants
- [ ] Exécuter `php artisan config:clear`
- [ ] Exécuter `php artisan cache:clear`
- [ ] Tester avec `php artisan email:test-verification`
- [ ] Vérifier l'inbox Mailtrap ou votre boîte Gmail
- [ ] Vérifier les logs : `storage/logs/laravel.log`

---

## 🔧 **Vérifier la Configuration Actuelle**

```bash
php artisan tinker

# Dans Tinker :
config('mail.mailers.smtp.host')
config('mail.mailers.smtp.port')
config('mail.from.address')
```

---

## 📝 **Logs à Vérifier**

```bash
# Windows PowerShell
Get-Content storage\logs\laravel.log -Tail 50

# Rechercher les erreurs d'email
Select-String -Path storage\logs\laravel.log -Pattern "mail|email|smtp" -CaseSensitive:$false | Select-Object -Last 10
```

---

## 🎯 **Test Final**

Une fois configuré correctement :

1. **S'inscrire** : Aller sur `/register`
2. **Remplir le formulaire** avec un nouvel email
3. **Soumettre** le formulaire
4. **Vérifier** :
   - ✅ Redirigé vers `/verify-email`
   - ✅ Email reçu dans Mailtrap/Gmail
   - ✅ Cliquer sur le lien dans l'email
   - ✅ Redirigé vers `/dashboard`

---

## ❓ **Questions Fréquentes**

### **Q: Pourquoi utiliser Mailtrap et pas Gmail directement ?**
**R:** Mailtrap est fait pour le développement. Il capture tous les emails sans les envoyer réellement, ce qui évite d'inonder les vraies boîtes email.

### **Q: L'email est envoyé mais je ne le reçois pas ?**
**R:** Vérifiez :
- Votre dossier Spam/Courrier indésirable
- Les logs Laravel : `storage/logs/laravel.log`
- La configuration SMTP est correcte

### **Q: Comment savoir si l'email a été envoyé ?**
**R:** Regardez les logs avec `php artisan email:test-verification`. Si "✅ Email envoyé avec succès !" apparaît, l'email a été envoyé côté serveur.

---

**✅ Je recommande d'utiliser Mailtrap pour le développement !**
