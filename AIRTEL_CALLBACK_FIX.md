# 🔧 Résolution : "Not able to update callback data"

## ❌ Problème

Vous recevez : **"Not able to update callback data. Please try again"**

### Cause
ngrok (version gratuite) affiche une page d'avertissement HTML qui bloque la validation automatique de l'URL par Airtel.

---

## ✅ Solutions

### Solution 1 : Authentifier ngrok (GRATUIT - RECOMMANDÉ)

#### Étape 1 : Créer un compte ngrok

1. Aller sur : https://dashboard.ngrok.com/signup
2. Créer un compte gratuit (email + mot de passe)
3. Vérifier votre email

#### Étape 2 : Obtenir votre authtoken

1. Connexion : https://dashboard.ngrok.com/get-started/your-authtoken
2. Copier votre authtoken (exemple : `2abc...xyz`)

#### Étape 3 : Configurer ngrok avec l'authtoken

```powershell
# Arrêter ngrok actuel
# Ctrl+C dans le terminal ngrok

# Configurer l'authtoken
ngrok config add-authtoken VOTRE_TOKEN_ICI

# Redémarrer ngrok
ngrok http 8000
```

#### Résultat
✅ Plus de page d'avertissement  
✅ Accès direct à l'API  
✅ Airtel peut valider l'URL

---

### Solution 2 : Utiliser localtunnel (Alternative gratuite)

Si ngrok pose toujours problème :

#### Installation
```powershell
npm install -g localtunnel
```

#### Démarrage
```powershell
lt --port 8000 --subdomain vintapp
```

#### Nouvelle URL
```
https://vintapp.loca.lt/api/payment-callbacks/airtel_money
```

---

### Solution 3 : Utiliser serveo.net (Sans installation)

```powershell
ssh -R 80:localhost:8000 serveo.net
```

Vous obtiendrez une URL comme : `https://abc123.serveo.net`

**URL de callback** :
```
https://abc123.serveo.net/api/payment-callbacks/airtel_money
```

---

## 🧪 Test de validation

Après avoir appliqué une solution, testez :

```powershell
# Test 1 : Vérifier l'API
Invoke-WebRequest -Uri "VOTRE_NOUVELLE_URL/api/health" -Method GET

# Résultat attendu :
# {"status":"success","message":"VintApp API is running","version":"1.0.0"}

# Test 2 : Vérifier le callback
Invoke-WebRequest -Uri "VOTRE_NOUVELLE_URL/api/payment-callbacks/airtel_money" -Method POST -ContentType "application/json" -Body '{"test":"data"}'

# Résultat attendu (pas d'erreur 404)
```

---

## 📝 Mettre à jour .env

Une fois la nouvelle URL obtenue :

```env
# Ancienne URL (avec problème)
# APP_URL=https://uncomely-uneffusing-averie.ngrok-free.dev

# Nouvelle URL (après authentification ngrok)
APP_URL=https://NOUVELLE-URL.ngrok-free.app
```

```powershell
php artisan config:clear
```

---

## 🎯 Comparaison des solutions

| Solution | Gratuit | Stable | Facile |
|----------|---------|--------|--------|
| **ngrok authentifié** | ✅ | ✅ | ✅ (Recommandé) |
| **localtunnel** | ✅ | ⚠️ | ✅ |
| **serveo.net** | ✅ | ⚠️ | ⚠️ |

---

## 🚀 Procédure rapide (5 minutes)

### Étape 1 : Créer compte ngrok
https://dashboard.ngrok.com/signup

### Étape 2 : Copier authtoken
https://dashboard.ngrok.com/get-started/your-authtoken

### Étape 3 : Configurer
```powershell
ngrok config add-authtoken VOTRE_TOKEN
```

### Étape 4 : Redémarrer
```powershell
ngrok http 8000
```

### Étape 5 : Nouvelle URL
Copier la nouvelle URL (exemple : `https://abc-def.ngrok-free.app`)

### Étape 6 : Mettre à jour .env
```env
APP_URL=https://abc-def.ngrok-free.app
```

### Étape 7 : Clear cache
```powershell
php artisan config:clear
```

### Étape 8 : Nouvelle URL pour Airtel
```
https://abc-def.ngrok-free.app/api/payment-callbacks/airtel_money
```

---

## 🐛 Si le problème persiste

### Vérifier que Laravel est bien démarré

```powershell
php artisan serve
```

### Vérifier que ngrok pointe vers le bon port

```powershell
ngrok http 8000
# Vérifier dans le terminal que "Forwarding" pointe bien vers localhost:8000
```

### Vérifier que la route existe

```powershell
php artisan route:list | Select-String "payment-callbacks"
```

Résultat attendu :
```
POST   api/payment-callbacks/{provider}
```

---

## 📞 Support

- **ngrok Documentation** : https://ngrok.com/docs
- **ngrok Dashboard** : https://dashboard.ngrok.com
- **VintApp Support** : gloirelumingu10@gmail.com

---

## ✅ Checklist finale

Avant de soumettre à Airtel :

- [ ] Compte ngrok créé et vérifié
- [ ] Authtoken configuré
- [ ] ngrok redémarré avec authentification
- [ ] Test API réussi (pas de page d'avertissement)
- [ ] APP_URL mis à jour dans .env
- [ ] Cache Laravel nettoyé
- [ ] Route de callback testée
- [ ] Nouvelle URL prête pour Airtel

---

**Temps estimé : 5 minutes** ⏱️

**Difficulté : Facile** 🟢
