# 🚀 VintApp PWA - Guide Rapide

## ⚡ Démarrage Rapide

### 1️⃣ Générer les Icônes (REQUIS)

```powershell
# Option 1 : Automatique (recommandé)
.\generate-pwa-icons.ps1 -LogoPath "C:\path\to\logo.png"

# Option 2 : Générateur en ligne
.\generate-pwa-icons.ps1 -UseOnlineGenerator
```

### 2️⃣ Tester en Local

```bash
php artisan serve
```

Ouvrir http://localhost:8000 dans Chrome

### 3️⃣ Vérifier PWA

**Chrome DevTools** (F12) :

1. Onglet **Application** → **Service Workers**

    - ✅ Doit afficher : `sw.js (activated and is running)`

2. Onglet **Application** → **Manifest**

    - ✅ Vérifier nom, icônes, couleur

3. Onglet **Lighthouse** → **Generate report**
    - ✅ PWA score doit être > 90/100

### 4️⃣ Installer l'App

**Desktop** :

-   Cliquer sur le bouton violet flottant "Installer l'app"
-   Ou barre d'adresse → Icône ⊕

**Android** :

-   Menu ⋮ → "Installer l'application"

**iOS** :

-   Bouton Partage → "Ajouter à l'écran d'accueil"

---

## 📁 Fichiers Importants

| Fichier                             | Description                     |
| ----------------------------------- | ------------------------------- |
| `public/manifest.json`              | Configuration PWA               |
| `public/sw.js`                      | Service Worker (cache, offline) |
| `public/js/pwa.js`                  | Gestionnaire PWA                |
| `resources/views/offline.blade.php` | Page hors ligne                 |
| `public/icons/`                     | **Icônes PWA (à générer)**      |

---

## 🎯 Fonctionnalités

-   ✅ **Installation native** (comme une vraie app)
-   ✅ **Mode offline** (fonctionne sans connexion)
-   ✅ **Mises à jour auto** (notification quand nouvelle version)
-   ✅ **Raccourcis** : Vendre / Commandes / Wallet
-   ✅ **Partage images** (Android uniquement)
-   ✅ **Cache intelligent** (7 jours)

---

## 🔍 Tests Offline

1. **DevTools** → **Network** → **Throttling** → **Offline**
2. **Actualiser** (F5)
3. → Page `/offline` s'affiche avec compteur auto-reconnexion
4. **Network** → **Online**
5. → Reconnexion automatique + rechargement

---

## 📚 Documentation Complète

-   **PWA_DOCUMENTATION.md** : Guide complet (800+ lignes)
-   **GENERATE_PWA_ICONS.md** : Génération icônes détaillée
-   **PWA_IMPLEMENTATION_RECAP.md** : Récapitulatif technique

---

## 🐛 Problème?

### Service Worker ne s'active pas

```javascript
// Console DevTools
navigator.serviceWorker.getRegistrations().then((r) => {
    r.forEach((reg) => reg.unregister());
});
location.reload();
```

### Bouton Install invisible

```javascript
// Désinstaller puis recharger
chrome://apps → VintApp → Supprimer
```

### Icônes manquantes

```powershell
# Vérifier présence
ls public\icons\*.png

# Attendu : 8 fichiers (72x72 à 512x512)
```

---

## ✅ Checklist

-   [ ] Icônes générées (8 fichiers dans `public/icons/`)
-   [ ] Service Worker actif (DevTools → Application)
-   [ ] Manifest valide (DevTools → Application)
-   [ ] Installation testée (Desktop/Mobile)
-   [ ] Mode offline testé
-   [ ] Lighthouse PWA > 90/100

---

## 🚀 Déploiement Production

### Prérequis

-   ✅ **HTTPS activé** (obligatoire pour PWA)
-   ✅ Icônes optimisées
-   ✅ Tests complets réussis

### Commandes

```bash
# 1. Compiler assets
npm run build

# 2. Deploy
git push production main

# 3. Sur le serveur
php artisan optimize:clear
php artisan config:cache
```

### Vérification

```bash
# Lighthouse CLI
lighthouse https://votre-domaine.com --view
```

---

**🎉 Voilà! VintApp est maintenant une PWA complète!**

**Dernière étape** : Générer les icônes avec `.\generate-pwa-icons.ps1`
