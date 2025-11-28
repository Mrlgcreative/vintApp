# 📱 GUIDE TEST PWA SUR ANDROID

## 🚨 PROBLÈME ACTUEL

Sur Android, le bouton d'installation PWA n'apparaît PAS car :

**❌ Votre site est en HTTP (localhost:8000)**  
**✅ Android nécessite HTTPS pour PWA**

---

## ✅ SOLUTION : Utiliser ngrok

ngrok crée un tunnel HTTPS vers votre localhost.

### 1. Installer ngrok

#### Option A : Télécharger

1. Aller sur https://ngrok.com/download
2. Télécharger ngrok pour Windows
3. Extraire dans `C:\ngrok\`
4. Ajouter au PATH ou utiliser le chemin complet

#### Option B : Chocolatey (recommandé)

```powershell
choco install ngrok
```

### 2. Créer un compte (gratuit)

1. Aller sur https://ngrok.com/signup
2. S'inscrire (gratuit)
3. Copier votre authtoken

### 3. Configurer ngrok

```powershell
ngrok config add-authtoken VOTRE_TOKEN_ICI
```

### 4. Démarrer Laravel

```powershell
# Terminal 1 : Serveur Laravel
php artisan serve
```

### 5. Démarrer ngrok

```powershell
# Terminal 2 : ngrok
ngrok http 8000
```

### 6. Résultat

```
ngrok
Session Status                online
Account                       Votre email
Forwarding                    https://abc123.ngrok-free.app -> http://localhost:8000
```

**Copier l'URL HTTPS** : `https://abc123.ngrok-free.app`

---

## 📱 TESTER SUR ANDROID

### 1. Ouvrir Chrome sur Android

-   Lancer Chrome (pas Samsung Internet, pas Firefox)

### 2. Accéder à l'URL ngrok

-   Taper l'URL HTTPS dans Chrome : `https://abc123.ngrok-free.app`
-   Cliquer sur "Visit Site" si warning ngrok

### 3. Première visite

-   Naviguer sur le site
-   **Le bouton n'apparaîtra PAS immédiatement** (normal)

### 4. Attendre les critères d'installation

Chrome Android affiche le prompt SEULEMENT si :

✅ **Au moins 2 visites** (espacées de 5 minutes)  
✅ **Service Worker actif**  
✅ **Manifest.json valide**  
✅ **HTTPS actif**  
✅ **User a interagi avec le site** (scroll, click)

### 5. Forcer l'installation (ne pas attendre)

#### Méthode 1 : Menu Chrome

1. Cliquer sur **Menu** (3 points verticaux en haut à droite)
2. Chercher **"Installer l'application"** ou **"Add to Home screen"**
3. Cliquer dessus
4. Confirmer

#### Méthode 2 : Settings

1. Menu → **Settings**
2. **Site settings**
3. Chercher votre site
4. **Add to Home screen**

---

## 🔍 VÉRIFIER QUE ÇA MARCHE

### Sur Desktop (avant de tester Android)

1. **Ouvrir Chrome DevTools** (F12)

2. **Onglet Application → Service Workers**

    - ✅ Doit montrer : `sw.js (activated and is running)`
    - ❌ Si vide : Problème Service Worker

3. **Onglet Application → Manifest**

    - ✅ Doit afficher : "VintApp - Marketplace de Confiance"
    - ✅ Doit montrer 8 icônes
    - ❌ Si erreurs : Problème manifest

4. **Onglet Lighthouse**
    - Cliquer **"Generate report"**
    - Section **PWA** doit être > 90/100
    - ✅ Si 100/100 : Parfait!
    - ❌ Si < 90 : Voir les erreurs

### Console Browser Android

1. Sur PC : **chrome://inspect** dans Chrome
2. Connecter Android en USB (debug USB activé)
3. Autoriser le debug sur Android
4. Dans chrome://inspect → Cliquer **"Inspect"** sur votre page
5. Voir la console pour erreurs

---

## 🐛 DÉPANNAGE

### "ERR_TUNNEL_CONNECTION_FAILED"

**Cause** : ngrok pas démarré ou expiré  
**Solution** : Relancer `ngrok http 8000`

### "Visit Site" warning ngrok

**Cause** : Écran d'avertissement ngrok gratuit  
**Solution** : Cliquer "Visit Site" - normal en version gratuite

### Bouton "Installer" n'apparaît toujours pas

**Vérifier critères** :

```javascript
// Console DevTools sur Android (via chrome://inspect)

// 1. Service Worker ?
navigator.serviceWorker
    .getRegistrations()
    .then((r) => console.log("SW:", r.length > 0 ? "OK" : "MANQUANT"));

// 2. Manifest ?
fetch("/manifest.json")
    .then((r) => r.json())
    .then((m) => console.log("Manifest:", m));

// 3. HTTPS ?
console.log("HTTPS:", location.protocol === "https:" ? "OK" : "MANQUANT");

// 4. beforeinstallprompt reçu ?
window.addEventListener("beforeinstallprompt", (e) => {
    console.log("beforeinstallprompt REÇU!", e);
});
```

### Service Worker ne s'active pas sur Android

**Clear cache** :

1. Chrome Android → Menu → Settings
2. Site settings → Storage
3. Chercher votre site
4. Clear storage
5. Recharger la page

---

## 📊 CHECKLIST COMPLÈTE

### Avant de tester sur Android

-   [x] Icônes générées (✅ FAIT - 8 fichiers créés)
-   [x] manifest.json existe (✅ FAIT)
-   [x] Service Worker existe (✅ FAIT - sw.js)
-   [x] PWA Manager chargé (✅ FAIT - pwa.js)
-   [ ] Laravel server lancé (`php artisan serve`)
-   [ ] ngrok installé et configuré
-   [ ] ngrok lancé (`ngrok http 8000`)
-   [ ] URL HTTPS récupérée

### Pendant le test Android

-   [ ] Chrome Android ouvert
-   [ ] URL HTTPS ngrok tapée
-   [ ] "Visit Site" cliqué (si warning)
-   [ ] Site chargé complètement
-   [ ] DevTools inspecté (via chrome://inspect)
-   [ ] Service Worker actif vérifié
-   [ ] Manifest valide vérifié
-   [ ] Au moins 2 visites effectuées (ou forcer via menu)
-   [ ] Menu Chrome → "Installer l'application" cherché

### Après installation

-   [ ] Icône sur écran d'accueil visible
-   [ ] Icône couleur violette avec "V" blanc
-   [ ] Nom "VintApp" sous l'icône
-   [ ] App lance en plein écran (pas de barre URL)
-   [ ] Barre de statut violette (#6366F1)
-   [ ] Appui long sur icône → Raccourcis visibles (Vendre, Commandes, Wallet)

---

## 🎯 ALTERNATIVE : localtunnel

Si ngrok pose problème :

```powershell
# Installer localtunnel
npm install -g localtunnel

# Lancer
lt --port 8000

# Résultat
your url is: https://random-name.loca.lt
```

Même principe que ngrok mais sans inscription.

---

## 🚀 PRODUCTION

Une fois en production (domaine réel) :

1. **Configurer HTTPS** (Let's Encrypt gratuit)

    ```bash
    sudo certbot --nginx -d vintapp.com
    ```

2. **Déployer le code**

    ```bash
    git push production main
    ```

3. **Tester sur Android**
    - Ouvrir `https://vintapp.com`
    - Chrome détectera automatiquement la PWA
    - Bannière d'installation après 2-3 visites
    - Ou Menu → "Installer l'application"

---

## 📞 AIDE RAPIDE

### Commandes essentielles

```powershell
# Terminal 1 : Laravel
php artisan serve

# Terminal 2 : ngrok
ngrok http 8000

# Vérifier icônes
ls public\images\icons\*.png

# Clear cache Laravel
php artisan optimize:clear
```

### URLs importantes

-   **ngrok dashboard** : http://localhost:4040 (quand ngrok actif)
-   **Chrome inspect** : chrome://inspect (sur PC, Android connecté)
-   **Service Worker test** : chrome://serviceworker-internals

---

**🎉 Maintenant testez sur Android avec ngrok!**

1. `php artisan serve`
2. `ngrok http 8000`
3. Copier URL HTTPS
4. Ouvrir sur Android Chrome
5. Menu → Installer l'application
