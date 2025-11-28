# ⚡ CORRECTIONS RAPIDES DE SÉCURITÉ - VintApp

**Temps total estimé** : 2 heures
**Priorité** : 🔴 CRITIQUE

---

## 🚨 ACTION 1 : RÉVOQUER LES CLÉS EXPOSÉES (30 min)

### 1.1 Google OAuth Client Secret

```
Clé exposée : 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
```

**Étapes** :
1. Aller sur https://console.cloud.google.com/apis/credentials
2. Trouver l'identifiant OAuth 2.0 pour VintApp
3. Cliquer sur "Supprimer" (⚠️ va casser l'authentification Google temporairement)
4. Créer un nouvel identifiant OAuth 2.0
5. Copier le nouveau Client Secret
6. Mettre à jour `.env` :
   ```env
   GOOGLE_CLIENT_SECRET=NOUVEAU_SECRET_ICI
   ```

---

### 1.2 Gmail App Password

```
Mot de passe exposé : jbkf pvwt gzeo usel
```

**Étapes** :
1. Aller sur https://myaccount.google.com/apppasswords
2. Se connecter avec gloirelumingu10@gmail.com
3. Révoquer le mot de passe "jbkf pvwt gzeo usel"
4. Créer un nouveau mot de passe d'application
5. Mettre à jour `.env` :
   ```env
   MAIL_PASSWORD="NOUVEAU_MOT_DE_PASSE_ICI"
   ```

---

### 1.3 M-Pesa API Key

```
Clé exposée : azo6gOxne9fgKzTwnahiX5ppUQGKRBsE
```

**Étapes** :
1. Se connecter au portail M-Pesa Developer
2. Aller dans "API Keys"
3. Révoquer la clé `azo6gOxne9fgKzTwnahiX5ppUQGKRBsE`
4. Générer une nouvelle clé API
5. Mettre à jour `.env` :
   ```env
   MPESA_API_KEY=NOUVELLE_CLE_ICI
   ```

---

### 1.4 OpenAI API Key

```
Clé exposée : sk-proj-eVp-p3Q178NusHANSgdKyA2...
```

**Étapes** :
1. Aller sur https://platform.openai.com/api-keys
2. Trouver la clé `sk-proj-eVp-p3Q178...`
3. Cliquer sur "Revoke"
4. Créer une nouvelle clé API
5. Mettre à jour `.env` :
   ```env
   OPENAI_API_KEY=NOUVELLE_CLE_ICI
   ```

---

## 🔧 ACTION 2 : CORRIGER TRUSTPROXIES (10 min)

**Fichier** : `app/Http/Middleware/TrustProxies.php`

**Remplacer** :
```php
protected $proxies = '*'; // ❌ DANGEREUX
```

**Par** :
```php
// Faire confiance uniquement à des IPs spécifiques
protected $proxies = [
    '127.0.0.1',  // Localhost
    '::1',        // IPv6 localhost
];

// En production, ajouter vos proxies (Cloudflare, ngrok, etc.)
// protected $proxies = env('TRUSTED_PROXIES') 
//     ? explode(',', env('TRUSTED_PROXIES'))
//     : ['127.0.0.1', '::1'];
```

**.env** :
```env
# Développement (ngrok)
TRUSTED_PROXIES=127.0.0.1,::1

# Production (Cloudflare)
# TRUSTED_PROXIES=173.245.48.0/20,103.21.244.0/22,103.22.200.0/22
```

---

## 🔐 ACTION 3 : RENFORCER VALIDATION CALLBACKS (20 min)

**Fichier** : `app/Http/Controllers/PaymentCallbackController.php`

### 3.1 Supprimer le bypass local

**Ligne ~100, SUPPRIMER** :
```php
// ❌ À SUPPRIMER
if (app()->environment('local') && in_array($clientIp, ['127.0.0.1', '::1'])) {
    return true;
}
```

### 3.2 Renforcer validation Airtel Money

**Ligne ~150, MODIFIER** :
```php
protected function verifyCallbackSignature(Request $request, string $provider): bool
{
    Log::info("Vérification signature callback", [
        'provider' => $provider,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);

    // Vérifier l'IP du provider
    if (!$this->isProviderIp($request->ip(), $provider)) {
        Log::warning("IP non autorisée pour callback", [
            'provider' => $provider,
            'ip' => $request->ip()
        ]);
        return false;
    }

    switch ($provider) {
        case 'airtel_money':
            $signature = $request->header('X-Airtel-Signature');
            $payload = $request->getContent();
            $privateKey = config('services.airtel_money.private_key');

            if (!$signature || !$privateKey) {
                Log::error('Airtel callback : signature ou clé manquante');
                return false;
            }

            // Calculer HMAC SHA256
            $expectedSignature = base64_encode(
                hash_hmac('sha256', $payload, $privateKey, true)
            );

            if (!hash_equals($expectedSignature, $signature)) {
                Log::error('Airtel callback : signature invalide', [
                    'expected' => $expectedSignature,
                    'received' => $signature
                ]);
                return false;
            }

            Log::info('Airtel callback : signature valide ✅');
            return true;

        case 'mpesa':
            // Même logique pour M-Pesa
            // ...
            
        default:
            Log::error("Provider inconnu : $provider");
            return false;
    }
}
```

### 3.3 Ajouter protection replay attack

**Ajouter cette méthode** :
```php
protected function preventReplayAttack(Request $request): bool
{
    $signature = $request->header('X-Callback-Signature');
    $cacheKey = 'callback_signature_' . md5($signature);
    
    // Vérifier si la signature a déjà été utilisée (dans les 5 dernières minutes)
    if (Cache::has($cacheKey)) {
        Log::warning('Replay attack détecté', [
            'ip' => $request->ip(),
            'signature' => substr($signature, 0, 20) . '...'
        ]);
        return false;
    }
    
    // Marquer la signature comme utilisée (expire après 5 minutes)
    Cache::put($cacheKey, true, now()->addMinutes(5));
    
    return true;
}
```

---

## 🚦 ACTION 4 : AJOUTER RATE LIMITING (15 min)

### 4.1 Login/Register

**Fichier** : `routes/web.php`

**Ligne ~25-30, MODIFIER** :
```php
// Login avec rate limiting
Route::post('login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1') // 5 tentatives par minute
    ->name('login');

// Register avec rate limiting
Route::post('register', [RegisterController::class, 'register'])
    ->middleware('throttle:3,10') // 3 inscriptions toutes les 10 minutes
    ->name('register');
```

### 4.2 Callbacks paiement

**Fichier** : `routes/api.php`

**Ligne ~40-45, MODIFIER** :
```php
Route::prefix('payment-callbacks')->group(function () {
    Route::post('/{provider}', [PaymentCallbackController::class, 'handleCallback'])
        ->middleware('throttle:100,1') // 100 callbacks par minute max
        ->name('payment.callback');
});
```

### 4.3 Routes admin

**Fichier** : `routes/web.php`

**Ligne ~200-250, MODIFIER** :
```php
Route::prefix('admin')
    ->middleware(['auth', 'admin', 'throttle:60,1']) // 60 requêtes/minute
    ->group(function () {
        // ... routes admin
    });
```

---

## 🛡️ ACTION 5 : AJOUTER HEADERS DE SÉCURITÉ (20 min)

### 5.1 Créer le middleware

```bash
php artisan make:middleware SecurityHeaders
```

### 5.2 Copier le code

**Fichier** : `app/Http/Middleware/SecurityHeaders.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Protection contre Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Protection XSS (navigateurs anciens)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Protection MIME Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // HTTPS strict (seulement en production)
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content Security Policy (CSP)
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://js.stripe.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "img-src 'self' data: https: blob:",
            "font-src 'self' data: https://cdn.jsdelivr.net",
            "connect-src 'self' https://api.openai.com",
            "frame-src 'self' https://js.stripe.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // Permissions Policy (anciennement Feature-Policy)
        $permissions = implode(', ', [
            'geolocation=()',
            'microphone=()',
            'camera=()',
            'payment=(self)',
            'usb=()',
        ]);
        $response->headers->set('Permissions-Policy', $permissions);

        return $response;
    }
}
```

### 5.3 Enregistrer le middleware

**Fichier** : `bootstrap/app.php`

**Ajouter** :
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
})
```

---

## 📁 ACTION 6 : VALIDER LES UPLOADS (15 min)

### 6.1 Vérifier les controllers avec uploads

**Fichiers à vérifier** :
- `app/Http/Controllers/ItemController.php`
- `app/Http/Controllers/ProfileController.php`
- Tous les controllers avec `$request->file()`

### 6.2 Exemple de validation sécurisée

```php
public function store(Request $request)
{
    $request->validate([
        'images.*' => [
            'required',
            'image',
            'mimes:jpeg,png,jpg,gif,webp',
            'max:2048', // 2MB
            'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
        ],
        'avatar' => [
            'nullable',
            'image',
            'mimes:jpeg,png,jpg',
            'max:1024', // 1MB
            'dimensions:ratio=1/1', // Carré
        ],
    ]);

    foreach ($request->file('images') as $image) {
        // Générer nom aléatoire (éviter injection)
        $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
        
        // Stocker dans storage/app/public (pas public/)
        $path = $image->storeAs('items', $filename, 'public');
        
        // Sauvegarder dans la DB
        ItemImage::create([
            'item_id' => $item->id,
            'path' => $path,
        ]);
    }
}
```

---

## 🔍 ACTION 7 : VÉRIFIER .ENV N'EST PAS DANS GIT (5 min)

```bash
# Vérifier si .env a été commité
git log --all --full-history -- .env

# Si oui, le supprimer de l'historique Git
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all

# Forcer le push
git push origin --force --all
```

---

## 📊 ACTION 8 : CONFIGURER LES LOGS DE SÉCURITÉ (10 min)

**Fichier** : `config/logging.php`

**Ajouter** :
```php
'channels' => [
    // ... channels existants
    
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'warning',
        'days' => 90, // Garder 90 jours d'historique
    ],
    
    'payment_callbacks' => [
        'driver' => 'daily',
        'path' => storage_path('logs/payment-callbacks.log'),
        'level' => 'info',
        'days' => 365, // Garder 1 an pour audits financiers
    ],
],
```

**Utiliser dans le code** :
```php
use Illuminate\Support\Facades\Log;

// Logs de sécurité
Log::channel('security')->warning('Tentative de login suspect', [
    'ip' => $request->ip(),
    'email' => $request->input('email')
]);

// Logs de paiements
Log::channel('payment_callbacks')->info('Callback Airtel reçu', [
    'transaction_id' => $data['transaction_id'],
    'status' => $data['status']
]);
```

---

## ✅ CHECKLIST DE VALIDATION

Après avoir appliqué toutes les corrections :

```bash
# 1. Vérifier .gitignore
cat .gitignore | grep .env

# 2. Tester rate limiting
# Essayer de faire 6 tentatives de login rapides
# Doit bloquer après 5 tentatives

# 3. Vérifier headers de sécurité
curl -I https://uncomely-uneffusing-averie.ngrok-free.dev

# Doit afficher :
# X-Frame-Options: SAMEORIGIN
# X-Content-Type-Options: nosniff
# X-XSS-Protection: 1; mode=block

# 4. Scanner les dépendances
composer audit
npm audit

# 5. Tester les callbacks (avec signature invalide)
# Doit rejeter avec code 401

# 6. Vérifier les logs
tail -f storage/logs/security.log
tail -f storage/logs/payment-callbacks.log
```

---

## 🎯 RÉSULTAT ATTENDU

Après application de toutes les corrections :

| Critère | Avant | Après |
|---------|-------|-------|
| Score sécurité | 6/10 | 9/10 |
| Secrets exposés | 4 | 0 ✅ |
| Rate limiting | ❌ | ✅ |
| Headers sécurité | ❌ | ✅ |
| TrustProxies | Permissif | Restrictif ✅ |
| Validation uploads | Partiel | Complet ✅ |

---

## 📞 SUPPORT

Si problème durant les corrections :
- Email : gloirelumingu10@gmail.com
- Vérifier les logs : `tail -f storage/logs/laravel.log`

---

**Temps total** : ~2 heures  
**Priorité** : 🔴 CRITIQUE  
**Deadline** : Aujourd'hui

Bon courage ! 🚀
