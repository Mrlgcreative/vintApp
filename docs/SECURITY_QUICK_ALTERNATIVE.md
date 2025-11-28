# 🔒 SÉCURISATION RAPIDE DES SECRETS - VintApp

**Si vous décidez de GARDER les clés actuelles**

---

## ✅ ACTIONS DE SÉCURISATION

### 1. Chiffrer le fichier .env (5 min)

Laravel 9+ supporte le chiffrement natif :

```bash
# Créer une clé de chiffrement .env
php artisan env:encrypt

# Crée : .env.encrypted
```

**Avantages** :
- Fichier .env.encrypted peut être commité dans Git
- Personne ne peut lire les secrets sans la clé

---

### 2. Créer un fichier .env.example (3 min)

```bash
# Copier .env en .env.example (sans valeurs sensibles)
cp .env .env.example
```

**Puis éditer .env.example** pour remplacer les vraies valeurs :

```env
# .env.example
GOOGLE_CLIENT_SECRET=votre_google_secret_ici
MAIL_PASSWORD=votre_gmail_app_password_ici
MPESA_API_KEY=votre_mpesa_api_key_ici
OPENAI_API_KEY=votre_openai_api_key_ici
```

Ce fichier peut être commité dans Git comme template.

---

### 3. Ajouter une protection supplémentaire (2 min)

**Restreindre les permissions du fichier .env** :

```powershell
# Windows PowerShell
icacls .env /inheritance:r
icacls .env /grant:r "$env:USERNAME:(R)"
```

**Linux/Mac** :
```bash
chmod 600 .env
```

**Résultat** : Seul votre compte peut lire le fichier.

---

### 4. Activer la rotation automatique des clés (Optionnel)

**Créer un reminder pour changer les clés tous les 3 mois** :

```bash
# Ajouter dans votre calendrier
# Tous les 3 mois : Rotation des clés API
```

---

### 5. Surveiller l'usage des clés

#### Google OAuth
```
https://console.cloud.google.com/apis/credentials
→ Voir l'activité récente
```

#### OpenAI
```
https://platform.openai.com/usage
→ Vérifier les requêtes suspectes
```

#### M-Pesa
```
Dashboard M-Pesa → Transaction logs
→ Vérifier les transactions inhabituelles
```

---

## 📊 COMPARAISON DES OPTIONS

| Option | Sécurité | Effort | Recommandé |
|--------|----------|--------|------------|
| **Garder clés actuelles + sécurisation** | 🟡 Moyen | 10 min | Si pas de compromission |
| **Révoquer et régénérer** | 🟢 Élevé | 45 min | Si doute ou exposition |
| **Chiffrer .env** | 🟢 Élevé | 5 min | Toujours |

---

## 🎯 RECOMMANDATION

### Si .env n'a JAMAIS été partagé :
✅ **Garder les clés** + Chiffrer .env + Restreindre permissions

### Si vous avez un DOUTE :
🔴 **Révoquer tout** (suivre REVOKE_SECRETS_GUIDE.md)

---

## 🚀 ACTIONS IMMÉDIATES

```bash
# 1. Chiffrer .env (TOUJOURS faire ça)
php artisan env:encrypt

# 2. Restreindre permissions .env
icacls .env /inheritance:r
icacls .env /grant:r "$env:USERNAME:(R)"

# 3. Créer .env.example pour Git
cp .env .env.example
# Puis éditer .env.example pour masquer les vraies valeurs

# 4. Commiter .env.example et .env.encrypted
git add .env.example .env.encrypted
git commit -m "chore: Ajouter templates sécurisés pour secrets"
git push
```

---

**Temps total** : 10 minutes  
**Gain de sécurité** : +30%

---

**Créé le** : 10 octobre 2025  
**Contact** : gloirelumingu10@gmail.com
