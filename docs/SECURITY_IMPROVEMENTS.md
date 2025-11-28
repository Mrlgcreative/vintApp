# 🔐 Amélioration Sécurité VintApp

## ✅ Mesures Implémentées

### 1. **Validation Stricte des Entrées**

#### Form Requests Créés

-   **`StoreItemRequest.php`** : Validation création produits

    -   Nom (3-255 caractères, sans HTML)
    -   Prix (positif, max 999M)
    -   Images (JPEG/PNG/WEBP, max 5MB, 1-10 images)
    -   Catégorie/Marque (vérification existence)
    -   Sanitization automatique avec `strip_tags()`

-   **`UpdateItemRequest.php`** : Validation mise à jour produits

    -   Vérification propriétaire via `authorize()`
    -   Validation partielle (champs optionnels)

-   **`CreateOrderRequest.php`** : Validation commandes

    -   Quantité (1-100)
    -   Téléphone (regex `/^[+]?[0-9]{8,15}$/`)
    -   Adresse (10-500 caractères)
    -   Méthode paiement (whitelist)

-   **`UpdateProfileRequest.php`** : Validation profil utilisateur
    -   Email unique
    -   Téléphone unique et format validé
    -   Avatar (JPEG/PNG, max 2MB)
    -   Bio (max 1000 caractères)

### 2. **Authentification & Rate Limiting**

#### Configuration Sanctum (`config/sanctum.php`)

```php
'expiration' => 60 * 24 * 7, // Tokens expirent après 7 jours
'stateful' => [...], // Domaines autorisés
```

#### Middleware `ThrottleLogin.php`

-   **5 tentatives max/minute** par email + IP
-   Auto-reset après succès
-   Réponse 429 avec `retry_after`

### 3. **CORS Sécurisé** (`config/cors.php`)

```php
'allowed_origins' => env('CORS_ALLOWED_ORIGINS')
    ? explode(',', env('CORS_ALLOWED_ORIGINS'))
    : ['http://localhost:3000', 'http://localhost:5173'],
'supports_credentials' => true,
'exposed_headers' => ['X-Cache', 'X-RateLimit-Limit'],
```

**En production** : Ajouter dans `.env`

```env
CORS_ALLOWED_ORIGINS=https://vintapp.com,https://www.vintapp.com
```

### 4. **Security Logging** (`SecurityLogging.php`)

#### Actions Loggées

-   Tentatives login/logout
-   Création commandes et paiements
-   Accès admin/expert
-   Achats de boosts

#### Logs Séparés

-   **`storage/logs/security.log`** (retention 30 jours)
-   Format : JSON avec user_id, IP, user-agent, durée

#### Types d'Événements

-   ✅ **INFO** : Actions sensibles réussies
-   ⚠️ **WARNING** : Échecs authentification (401)
-   ⚠️ **WARNING** : Accès non autorisés (403)
-   ℹ️ **NOTICE** : Erreurs validation (422)

### 5. **Chiffrement Données Sensibles**

#### Service `DataEncryptionService.php`

**Méthodes de Chiffrement**

```php
encryptPhone($phone)    // Chiffre avec Laravel Crypt
decryptPhone($encrypted) // Déchiffre (try-catch)
encryptAddress($address)
decryptAddress($encrypted)
```

**Méthodes de Masquage** (pour affichage)

```php
maskPhone('+237612345678') → '+237******78'
maskEmail('user@example.com') → 'u***@example.com'
```

**Hashage Tokens** (non réversible)

```php
hashToken($token) // HMAC-SHA256 avec APP_KEY
```

### 6. **Protection Injection SQL**

✅ **Audit effectué** : Toutes les requêtes utilisent :

-   Eloquent ORM
-   Query Builder avec bindings
-   Pas de concaténation SQL brute

**Exceptions sécurisées** :

-   Migrations (ALTER TABLE via `DB::statement`)
-   Fichiers de test (environnement dev uniquement)

### 7. **Headers de Sécurité** (Déjà actifs)

Via `SecurityHeaders.php` :

-   `X-Frame-Options: SAMEORIGIN`
-   `X-XSS-Protection: 1; mode=block`
-   `X-Content-Type-Options: nosniff`
-   `Strict-Transport-Security` (production)
-   `Content-Security-Policy` (CSP strict)
-   `Referrer-Policy: strict-origin-when-cross-origin`

---

## 🚀 Activation

### 1. Enregistrer les Middlewares

✅ Déjà fait dans `bootstrap/app.php` :

```php
'throttle.login' => \App\Http\Middleware\ThrottleLogin::class,
'security.log' => \App\Http\Middleware\SecurityLogging::class,
```

### 2. Appliquer aux Routes

**Routes de Login** (`routes/web.php` ou `routes/api.php`)

```php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['throttle.login', 'security.log']);

Route::post('/register', [AuthController::class, 'register'])
    ->middleware(['security.log']);
```

**Routes Admin**

```php
Route::middleware(['auth', 'admin', 'security.log'])->group(function () {
    // Routes admin sensibles
});
```

### 3. Utiliser Form Requests dans Controllers

**ItemController.php**

```php
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;

public function store(StoreItemRequest $request) {
    // Données déjà validées et sanitisées
    $validated = $request->validated();
}

public function update(UpdateItemRequest $request, Item $item) {
    // Authorization déjà vérifiée
    $validated = $request->validated();
}
```

**OrderController.php**

```php
use App\Http\Requests\CreateOrderRequest;

public function store(CreateOrderRequest $request) {
    $validated = $request->validated();
}
```

**UserController.php**

```php
use App\Http\Requests\UpdateProfileRequest;

public function update(UpdateProfileRequest $request) {
    $validated = $request->validated();
}
```

### 4. Chiffrer Données Sensibles

**Dans Modèles** (optionnel - via Accessors/Mutators)

```php
// app/Models/User.php
use App\Services\DataEncryptionService;

protected function phone(): Attribute
{
    return Attribute::make(
        get: fn ($value) => app(DataEncryptionService::class)->decryptPhone($value),
        set: fn ($value) => app(DataEncryptionService::class)->encryptPhone($value),
    );
}
```

**Ou dans Controllers** (plus flexible)

```php
$encryptionService = app(DataEncryptionService::class);

// Sauvegarder
$user->phone = $encryptionService->encryptPhone($request->phone);

// Afficher (masqué)
$maskedPhone = $encryptionService->maskPhone($user->phone);
```

---

## 📊 Monitoring

### Surveiller les Logs de Sécurité

```bash
tail -f storage/logs/security.log
```

### Alertes Automatiques

Configurer **Slack/Email** pour événements critiques :

```php
// config/logging.php
'security' => [
    'driver' => 'stack',
    'channels' => ['daily', 'slack'],
],
```

---

## 🎯 Checklist Production

-   [ ] Configurer `CORS_ALLOWED_ORIGINS` dans `.env`
-   [ ] Activer `APP_DEBUG=false`
-   [ ] Générer nouvelle `APP_KEY` si jamais exposée
-   [ ] Configurer rotation logs (max 30 jours)
-   [ ] Tester rate limiting login
-   [ ] Vérifier CSP ne bloque pas ressources légitimes
-   [ ] Audit sécurité externe recommandé
-   [ ] Configurer HTTPS strict (certificat SSL)

---

## 🛡️ Résumé des Gains

| Vulnérabilité       | Protection                      | Impact      |
| ------------------- | ------------------------------- | ----------- |
| XSS                 | Validation + CSP + strip_tags() | ✅ Élevé    |
| CSRF                | Tokens Laravel (natif)          | ✅ Élevé    |
| Injection SQL       | Eloquent + Query Builder        | ✅ Critique |
| Brute Force Login   | Rate limiting (5/min)           | ✅ Élevé    |
| Clickjacking        | X-Frame-Options                 | ✅ Moyen    |
| Données exposées    | Chiffrement + Masquage          | ✅ Élevé    |
| Accès non autorisés | Logging + Monitoring            | ✅ Moyen    |

**Niveau de sécurité global : 🔐 ÉLEVÉ**
