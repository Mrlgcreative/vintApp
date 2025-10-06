# 🔍 Checklist : Mise à jour des références après migration routes

**Migration** : Routes paiements `api.php` → `web.php`  
**Date** : 6 octobre 2025

---

## ✅ Étapes de vérification

### 1. **Vérifier les fichiers Blade (vues)**

```bash
# Rechercher les anciennes URLs API
grep -r "/api/payments" resources/views/
```

**À remplacer** :
```blade
<!-- ❌ AVANT -->
<form action="/api/payments/illicocash" method="POST">

<!-- ✅ APRÈS -->
<form action="{{ route('payments.illicocash') }}" method="POST">
```

---

### 2. **Vérifier les contrôleurs**

```bash
# Rechercher les redirections/URLs
grep -r "/api/payments" app/Http/Controllers/
```

**À remplacer** :
```php
// ❌ AVANT
return redirect('/api/payments/process');

// ✅ APRÈS
return redirect()->route('payments.process');
```

---

### 3. **Vérifier les fichiers JavaScript**

```bash
# Rechercher dans les fichiers JS/Vue
grep -r "/api/payments" resources/js/
```

**À remplacer** :
```javascript
// ❌ AVANT
fetch('/api/payments/orange-money', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
});

// ✅ APRÈS
fetch('/payments/orange-money', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
});
```

---

### 4. **Vérifier les tests**

```bash
# Rechercher dans les tests
grep -r "/api/payments" tests/
```

**À remplacer** :
```php
// ❌ AVANT
$response = $this->postJson('/api/payments/process', $data);

// ✅ APRÈS
$response = $this->post(route('payments.process'), $data);
```

---

### 5. **Vérifier la documentation API**

```bash
# Rechercher dans les fichiers markdown
grep -r "/api/payments" *.md
```

**À mettre à jour** : Documentation utilisateur, README, guides d'intégration

---

## 🔧 Commandes de recherche automatique

### Recherche globale dans le projet

```bash
# Windows PowerShell
Select-String -Path "resources/views/*.blade.php" -Pattern "/api/payments" -Recurse
Select-String -Path "resources/js/*.js" -Pattern "/api/payments" -Recurse
Select-String -Path "app/Http/Controllers/*.php" -Pattern "/api/payments" -Recurse
Select-String -Path "tests/*.php" -Pattern "/api/payments" -Recurse
```

---

## 📝 Fichiers à vérifier en priorité

| Fichier | Type | Priorité | Raison |
|---------|------|----------|--------|
| `resources/views/wallet/*.blade.php` | Blade | 🔴 HAUTE | Formulaires de recharge |
| `resources/views/cart/checkout.blade.php` | Blade | 🔴 HAUTE | Paiement commande |
| `resources/js/app.js` | JavaScript | 🟡 MOYENNE | Appels AJAX possibles |
| `app/Http/Controllers/WalletController.php` | PHP | 🟡 MOYENNE | Redirections |
| `app/Http/Controllers/CartController.php` | PHP | 🟡 MOYENNE | Paiement panier |
| `tests/Feature/PaymentTest.php` | Test | 🟢 BASSE | Tests automatisés |

---

## ✅ Résultats de la migration

### Routes vérifiées

```bash
php artisan route:list --path=payments
```

**Résultat** :
```
POST  payments/process ............. payments.process
POST  payments/illicocash .......... payments.illicocash
POST  payments/orange-money ........ payments.orange_money
POST  payments/airtel-money ........ payments.airtel_money
POST  payments/mpesa ............... payments.mpesa
POST  payments/africell ............ payments.africell
POST  payments/simulate ............ payments.simulate
POST  payments/callback ............ payments.callback
```

✅ **8 routes** correctement enregistrées dans `web.php`

---

### Anciennes routes API supprimées

```bash
php artisan route:list --path=api/payments
```

**Résultat** :
```
ERROR  Your application doesn't have any routes matching the given criteria.
```

✅ **Aucune route** API de paiement restante

---

## 🎯 Actions recommandées

### Immédiat

1. ✅ **Migration effectuée** - Routes déplacées
2. ⏳ **Rechercher références** - Utiliser commandes ci-dessus
3. ⏳ **Mettre à jour vues** - Remplacer URLs
4. ⏳ **Tester manuellement** - Parcours utilisateur complet

### Court terme

5. ⏳ **Ajouter middleware auth** - Protéger routes sensibles
6. ⏳ **Exclure CSRF callback** - Pour webhooks opérateurs
7. ⏳ **Mettre à jour documentation** - README, API docs

### Long terme

8. ⏳ **Tests automatisés** - Feature tests complets
9. ⏳ **Monitoring** - Logs des paiements
10. ⏳ **Sécurité** - Rate limiting, validation IP

---

## 🚨 Points d'attention

### 1. **Protection CSRF**

Les routes `web.php` ont la protection CSRF automatique. Les formulaires doivent inclure :

```blade
<form method="POST" action="{{ route('payments.illicocash') }}">
    @csrf
    <!-- Champs du formulaire -->
</form>
```

### 2. **Callback des opérateurs**

La route `/payments/callback` doit probablement exclure le CSRF :

```php
// Dans web.php
Route::post('/payments/callback', [PaymentController::class, 'handleCallback'])
    ->name('payments.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

**Ou** dans `app/Http/Middleware/VerifyCsrfToken.php` :

```php
protected $except = [
    'payments/callback',
];
```

### 3. **Authentication**

Si certaines routes nécessitent l'authentification :

```php
Route::middleware(['auth'])->prefix('payments')->group(function () {
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('payments.process');
    Route::post('/illicocash', [PaymentController::class, 'payWithIllicocash'])->name('payments.illicocash');
    // ... autres routes utilisateur
});

// Route publique pour callback
Route::post('/payments/callback', [PaymentController::class, 'handleCallback'])
    ->name('payments.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

---

## 📊 Impact estimé

| Zone | Impact | Effort correction | Risque |
|------|--------|-------------------|--------|
| Vues Blade | 🟡 Moyen | 1-2h | Faible |
| JavaScript | 🟢 Faible | 30min | Faible |
| Contrôleurs | 🟢 Faible | 30min | Faible |
| Tests | 🟡 Moyen | 1h | Moyen |
| Documentation | 🟢 Faible | 30min | Aucun |

**Total estimé** : 3-4 heures de travail

---

## ✅ Checklist finale

- [x] Routes supprimées de `api.php`
- [x] Routes ajoutées dans `web.php`
- [x] Route `/process` ajoutée (était manquante)
- [x] Vérification `php artisan route:list`
- [x] Documentation de migration créée
- [ ] Recherche références dans le code
- [ ] Mise à jour vues Blade
- [ ] Mise à jour JavaScript
- [ ] Mise à jour contrôleurs
- [ ] Tests manuels
- [ ] Tests automatisés
- [ ] Déploiement

---

**Prochaine étape** : Rechercher et remplacer les références `/api/payments` dans le code.
