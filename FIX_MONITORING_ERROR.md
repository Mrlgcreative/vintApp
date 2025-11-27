# 🔧 FIX: Erreur "Unexpected token '<'" - Dashboard Monitoring

## ❌ Problème

Erreur: `Unexpected token '<'` lors du rafraîchissement automatique du dashboard monitoring.

## 🔍 Cause

L'appel AJAX vers `/admin/monitoring/stats` retourne du HTML (page de login) au lieu de JSON car :

1. La session utilisateur a expiré
2. L'utilisateur n'est pas connecté en tant qu'admin
3. Laravel redirige vers la page de login qui renvoie du HTML
4. JavaScript tente de parser du HTML comme JSON → Erreur

## ✅ Solutions Appliquées

### 1. **Gestion des erreurs d'authentification**

```vue
// Détection 401/419 (session expirée) if (response.status === 401 ||
response.status === 419) { window.location.reload(); // Rediriger vers login
return; }
```

### 2. **Vérification du Content-Type**

```vue
const contentType = response.headers.get('content-type'); if (!contentType ||
!contentType.includes('application/json')) { window.location.reload(); // Pas du
JSON = probablement login return; }
```

### 3. **Credentials dans fetch**

```vue
fetch('/admin/monitoring/stats', { credentials: 'same-origin', // Inclure
cookies de session headers: { 'X-CSRF-TOKEN':
document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
'X-Requested-With': 'XMLHttpRequest' } });
```

### 4. **Auto-refresh désactivé en cas d'erreur**

```vue
autoRefresh.value = false; // Stop les appels répétés si erreur
```

## 🎯 Comment Tester

### 1. Connectez-vous en tant qu'admin

```bash
# Créer un admin si nécessaire
php artisan tinker
>>> $user = User::find(1);
>>> $user->role = 'admin';
>>> $user->save();
```

### 2. Accédez au dashboard

```
http://localhost:8000/admin/monitoring
```

### 3. Vérifiez dans la console

-   Ouvrez DevTools (F12)
-   Onglet Console
-   Onglet Network
-   Vérifiez que `/admin/monitoring/stats` retourne du JSON (pas du HTML)

### 4. Testez l'auto-refresh

-   Le dashboard devrait se rafraîchir toutes les 5 secondes
-   Aucune erreur dans la console
-   Les métriques se mettent à jour

## 📋 Checklist de Debugging

Si l'erreur persiste :

### ✅ Vérifier l'authentification

```bash
# Dans la console du navigateur
console.log(document.cookie); // Vérifier session Laravel
```

### ✅ Vérifier les routes

```bash
php artisan route:list --name=admin.monitoring
# Toutes les routes doivent être protégées par auth + admin
```

### ✅ Vérifier les middlewares

```php
// routes/web.php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/stats', ...); // ✅ Hérite de auth + admin
    });
});
```

### ✅ Tester la route directement

```bash
# PowerShell (avec session active)
Invoke-WebRequest -Uri "http://localhost:8000/admin/monitoring/stats" `
    -Headers @{"Accept"="application/json"} `
    -WebSession $session

# Si retourne 401 → Pas connecté
# Si retourne HTML → Middleware redirige
# Si retourne JSON → ✅ OK
```

### ✅ Vérifier MonitoringService

```bash
php artisan tinker
>>> app(App\Services\MonitoringService::class)->getRealTimeStats();
# Doit retourner un tableau avec stats
```

## 🔐 Sécurité

Les routes `/admin/monitoring/*` sont protégées par :

-   `auth` : Utilisateur connecté
-   `admin` : Rôle admin
-   `throttle:60,1` : Rate limiting (60 req/min)
-   `security.log` : Logging des accès

**C'est normal que l'accès soit refusé si vous n'êtes pas admin !**

## ✅ Résolution Finale

**L'erreur "Unexpected token '<'" est maintenant gérée correctement :**

1. ✅ Détection des redirections (HTML au lieu de JSON)
2. ✅ Rechargement automatique de la page si session expirée
3. ✅ Auto-refresh désactivé si erreur
4. ✅ Logs dans console pour debugging

**Action requise :** Connectez-vous en tant qu'admin pour accéder au dashboard monitoring.
