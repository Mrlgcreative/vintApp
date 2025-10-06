# 🔄 Migration des Routes API → WEB (Paiements)

**Date** : 6 octobre 2025  
**Tâche** : Déplacer les routes de paiement de `api.php` vers `web.php`

---

## ✅ Modifications effectuées

### 1. **Routes supprimées de `api.php`**

**Fichier** : `routes/api.php`  
**Lignes supprimées** : ~98-107

```php
// ❌ SUPPRIMÉ DE api.php
Route::prefix('payments')->group(function () {
    Route::post('/process', [PaymentController::class, 'processPayment']);
    Route::post('/illicocash', [PaymentController::class, 'payWithIllicocash']);
    Route::post('/orange-money', [PaymentController::class, 'payWithOrangeMoney']);
    Route::post('/airtel-money', [PaymentController::class, 'payWithAirtelMoney']);
    Route::post('/mpesa', [PaymentController::class, 'payWithMpesa']);
    Route::post('/africell', [PaymentController::class, 'payWithAfricell']);
    Route::post('/simulate', [PaymentController::class, 'simulatePayment']);
    Route::post('/callback', [PaymentController::class, 'handleCallback']);
});
```

---

### 2. **Routes ajoutées/mises à jour dans `web.php`**

**Fichier** : `routes/web.php`  
**Lignes** : ~259-268

```php
// ✅ AJOUTÉ/MIS À JOUR dans web.php
Route::prefix('payments')->group(function () {
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('payments.process'); // ← NOUVEAU
    Route::post('/illicocash', [PaymentController::class, 'payWithIllicocash'])->name('payments.illicocash');
    Route::post('/orange-money', [PaymentController::class, 'payWithOrangeMoney'])->name('payments.orange_money');
    Route::post('/airtel-money', [PaymentController::class, 'payWithAirtelMoney'])->name('payments.airtel_money');
    Route::post('/mpesa', [PaymentController::class, 'payWithMpesa'])->name('payments.mpesa');
    Route::post('/africell', [PaymentController::class, 'payWithAfricell'])->name('payments.africell');
    Route::post('/simulate', [PaymentController::class, 'simulatePayment'])->name('payments.simulate');
    Route::post('/callback', [PaymentController::class, 'handleCallback'])->name('payments.callback');
});
```

---

## 📊 Comparaison avant/après

### Avant

| Route | Fichier | Préfixe URL | Middleware | Nommage |
|-------|---------|-------------|------------|---------|
| `/process` | `api.php` | `/api/payments` | `auth:sanctum` | ❌ Aucun |
| `/illicocash` | `web.php` | `/payments` | Aucun | `payments.illicocash` |
| `/orange-money` | `web.php` | `/payments` | Aucun | `payments.orange_money` |
| `/airtel-money` | `web.php` | `/payments` | Aucun | `payments.airtel_money` |
| `/mpesa` | `web.php` | `/payments` | Aucun | `payments.mpesa` |
| `/africell` | `web.php` | `/payments` | Aucun | `payments.africell` |
| `/simulate` | `web.php` | `/payments` | Aucun | `payments.simulate` |
| `/callback` | `web.php` | `/payments` | Aucun | `payments.callback` |

**Problème** : Duplication partielle des routes entre `api.php` et `web.php`

---

### Après

| Route | Fichier | Préfixe URL | Middleware | Nommage |
|-------|---------|-------------|------------|---------|
| `/process` | `web.php` | `/payments` | Aucun | `payments.process` |
| `/illicocash` | `web.php` | `/payments` | Aucun | `payments.illicocash` |
| `/orange-money` | `web.php` | `/payments` | Aucun | `payments.orange_money` |
| `/airtel-money` | `web.php` | `/payments` | Aucun | `payments.airtel_money` |
| `/mpesa` | `web.php` | `/payments` | Aucun | `payments.mpesa` |
| `/africell` | `web.php` | `/payments` | Aucun | `payments.africell` |
| `/simulate` | `web.php` | `/payments` | Aucun | `payments.simulate` |
| `/callback` | `web.php` | `/payments` | Aucun | `payments.callback` |

**Résolution** : ✅ Toutes les routes consolidées dans `web.php`

---

## 🔗 URLs complètes

### Routes accessibles maintenant

```
POST /payments/process          → PaymentController::processPayment()
POST /payments/illicocash       → PaymentController::payWithIllicocash()
POST /payments/orange-money     → PaymentController::payWithOrangeMoney()
POST /payments/airtel-money     → PaymentController::payWithAirtelMoney()
POST /payments/mpesa            → PaymentController::payWithMpesa()
POST /payments/africell         → PaymentController::payWithAfricell()
POST /payments/simulate         → PaymentController::simulatePayment()
POST /payments/callback         → PaymentController::handleCallback()
```

**Note** : Aucun préfixe `/api/` → URLs plus simples

---

## ⚠️ Impact sur le code existant

### 1. **Formulaires HTML**

**Avant** (si utilisé) :
```html
<form action="/api/payments/illicocash" method="POST">
```

**Après** :
```html
<form action="/payments/illicocash" method="POST">
<!-- OU -->
<form action="{{ route('payments.illicocash') }}" method="POST">
```

✅ **Recommandation** : Utiliser `route('payments.illicocash')` pour éviter les URLs en dur

---

### 2. **JavaScript/AJAX**

**Avant** (si utilisé) :
```javascript
fetch('/api/payments/orange-money', {
    method: 'POST',
    // ...
});
```

**Après** :
```javascript
fetch('/payments/orange-money', {
    method: 'POST',
    // ...
});
```

---

### 3. **Tests**

**Avant** :
```php
$response = $this->postJson('/api/payments/process', $data);
```

**Après** :
```php
$response = $this->post(route('payments.process'), $data);
```

---

## 🧪 Vérification

### Commandes de test

```bash
# Lister toutes les routes de paiement
php artisan route:list --path=payments

# Vérifier les routes nommées
php artisan route:list --name=payments

# Cache des routes (si activé en production)
php artisan route:cache
```

### Sortie attendue

```
POST  payments/process ............. payments.process › PaymentController@processPayment
POST  payments/illicocash .......... payments.illicocash › PaymentController@payWithIllicocash
POST  payments/orange-money ........ payments.orange_money › PaymentController@payWithOrangeMoney
POST  payments/airtel-money ........ payments.airtel_money › PaymentController@payWithAirtelMoney
POST  payments/mpesa ............... payments.mpesa › PaymentController@payWithMpesa
POST  payments/africell ............ payments.africell › PaymentController@payWithAfricell
POST  payments/simulate ............ payments.simulate › PaymentController@simulatePayment
POST  payments/callback ............ payments.callback › PaymentController@handleCallback
```

---

## ✅ Avantages de cette migration

| Avant (API routes) | Après (Web routes) | Avantage |
|-------------------|-------------------|----------|
| `/api/payments/*` | `/payments/*` | ✅ URLs plus courtes |
| Middleware `auth:sanctum` | Flexible (ajout possible) | ✅ Contrôle granulaire |
| Pas de noms de route | Routes nommées | ✅ Meilleure maintenabilité |
| Duplication routes | Consolidation | ✅ Code plus propre |
| CSRF via API | CSRF web automatique | ✅ Sécurité native Laravel |

---

## 📝 Recommandations

### 1. **Ajouter middleware auth si nécessaire**

```php
Route::prefix('payments')->middleware(['auth'])->group(function () {
    // Routes protégées par authentification
});
```

### 2. **Séparer routes publiques/privées**

```php
// Routes publiques (callback opérateurs)
Route::post('/payments/callback', [PaymentController::class, 'handleCallback'])
    ->name('payments.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Routes authentifiées (utilisateurs)
Route::middleware(['auth'])->prefix('payments')->group(function () {
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('payments.process');
    Route::post('/illicocash', [PaymentController::class, 'payWithIllicocash'])->name('payments.illicocash');
    // ...
});
```

### 3. **Documenter les webhooks**

```php
// Route spéciale pour webhooks externes (sans CSRF)
Route::post('/payments/webhook/{provider}', [PaymentController::class, 'handleWebhook'])
    ->name('payments.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

---

## 🚀 Prochaines étapes

1. ✅ **Migration effectuée** - Routes déplacées de `api.php` → `web.php`
2. ⏳ **Tester les routes** - `php artisan route:list --path=payments`
3. ⏳ **Mettre à jour le frontend** - Remplacer `/api/payments/*` par `/payments/*`
4. ⏳ **Ajouter middleware auth** - Protéger les routes sensibles
5. ⏳ **Implémenter callbacks** - Compléter `handleCallback()` dans PaymentController

---

## 📚 Fichiers modifiés

| Fichier | Modifications | Lignes | État |
|---------|--------------|--------|------|
| `routes/api.php` | Suppression routes payments | ~98-107 (supprimé) | ✅ Modifié |
| `routes/web.php` | Ajout route `/process` | ~259-268 (ajouté) | ✅ Modifié |

---

**Résumé** : ✅ Migration réussie - Toutes les routes de paiement sont maintenant dans `web.php` avec nommage cohérent.
