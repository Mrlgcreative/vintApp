# 📊 MONITORING, BACKUP & MOBILE - GUIDE COMPLET

_Date : 27 novembre 2025_

---

## ✅ **COMPOSANTS INSTALLÉS**

### 1. **Laravel Telescope** 🔭

**Outil de debugging et monitoring pour développement**

#### Installation

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

#### Accès

-   URL: `http://localhost:8000/telescope`
-   Middleware: `dev` (désactivé en production par défaut)

#### Fonctionnalités

-   ✅ Requêtes HTTP et temps de réponse
-   ✅ Requêtes BDD avec query time
-   ✅ Jobs, Events, Listeners
-   ✅ Emails envoyés
-   ✅ Cache hits/misses
-   ✅ Exceptions et erreurs
-   ✅ Logs en temps réel

---

### 2. **Spatie Laravel Backup** 💾

**Système de sauvegarde automatique**

#### Installation

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

#### Configuration (`config/backup.php`)

```php
'name' => 'VintApp',
'source' => [
    'databases' => ['mysql'],
    'exclude' => [
        'vendor', 'node_modules', '.git',
        'storage/framework/cache',
        'storage/logs',
    ],
],
```

#### Commandes

```bash
# Créer un backup manuel
php artisan backup:run

# Créer backup BDD seulement
php artisan backup:run --only-db

# Lister les backups
php artisan backup:list

# Nettoyer les anciens backups
php artisan backup:clean
```

#### Automatisation (Cron)

Ajouter dans `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Backup quotidien à 2h du matin
    $schedule->command('backup:run --only-db')
        ->dailyAt('02:00');

    // Nettoyage hebdomadaire
    $schedule->command('backup:clean')
        ->weekly();
}
```

#### Stockage

Par défaut: `storage/app/[APP_NAME]`

-   Configurer S3/Cloud dans `.env` pour production:

```env
BACKUP_DISK=s3
AWS_ACCESS_KEY_ID=xxx
AWS_SECRET_ACCESS_KEY=xxx
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=vintapp-backups
```

---

### 3. **MonitoringService** 📈

**Service centralisé de métriques**

#### Localisation

`app/Services/MonitoringService.php`

#### Méthodes Principales

##### Enregistrer Performance

```php
use App\Services\MonitoringService;

$monitoring = app(MonitoringService::class);

$start = microtime(true);
// ... opération ...
$duration = microtime(true) - $start;

$monitoring->recordPerformance('operation_name', $duration, [
    'user_id' => auth()->id(),
    'context' => 'additional_data',
]);
```

##### Enregistrer Événement Business

```php
$monitoring->recordBusinessMetric('order_created', $order->total_amount, [
    'order_id' => $order->id,
    'user_id' => $order->user_id,
]);
```

##### Enregistrer Erreur

```php
try {
    // code...
} catch (\Exception $e) {
    $monitoring->recordError($e, [
        'operation' => 'payment_processing',
        'user_id' => auth()->id(),
    ]);
}
```

##### Health Check

```php
$health = $monitoring->healthCheck();
// {
//   "status": "healthy|degraded|unhealthy",
//   "checks": {
//     "database": {"status": "ok"},
//     "cache": {"status": "ok"},
//     "disk": {"status": "ok", "usage_percent": 45.2, "free_gb": 125.5}
//   }
// }
```

#### Canaux de Logging

| Canal         | Fichier                        | Rétention | Usage                           |
| ------------- | ------------------------------ | --------- | ------------------------------- |
| `security`    | `storage/logs/security.log`    | 30 jours  | Authentification, autorisations |
| `performance` | `storage/logs/performance.log` | 14 jours  | Opérations lentes (>1s)         |
| `business`    | `storage/logs/business.log`    | 90 jours  | Événements métier importants    |
| `errors`      | `storage/logs/errors.log`      | 30 jours  | Erreurs applicatives            |

---

### 4. **Dashboard Monitoring** 📊

**Interface admin en temps réel**

#### Accès

-   URL: `http://localhost:8000/admin/monitoring`
-   Middleware: `auth`, `admin`, `security.log`

#### Fonctionnalités

-   ✅ **Auto-refresh** toutes les 5 secondes
-   ✅ **Health Status** en temps réel
-   ✅ **Statistiques BDD** (utilisateurs, commandes, revenus)
-   ✅ **Cache Metrics** (hit rate, hits/misses)
-   ✅ **Performance** (temps de réponse moyen, opérations lentes)
-   ✅ **Événements Business** récents
-   ✅ **Erreurs** avec stack trace
-   ✅ Accès rapide à Telescope

#### Endpoints API

```bash
# Récupérer stats en JSON
GET /admin/monitoring/stats

# Health check
GET /admin/monitoring/health
# Returns: 200 (healthy) | 503 (unhealthy)

# Réinitialiser métriques
POST /admin/monitoring/reset
```

#### Intégration dans le Code

```php
use App\Services\MonitoringService;

class ItemController extends Controller
{
    public function __construct(
        protected MonitoringService $monitoring
    ) {}

    public function store(StoreItemRequest $request)
    {
        $start = microtime(true);

        $item = Item::create($request->validated());

        // Enregistrer performance
        $this->monitoring->recordPerformance(
            'item.create',
            microtime(true) - $start,
            ['item_id' => $item->id]
        );

        // Enregistrer événement business
        $this->monitoring->recordBusinessMetric('item_created', 1, [
            'category' => $item->category_id,
            'price' => $item->price,
        ]);

        return redirect()->route('items.show', $item);
    }
}
```

---

### 5. **Optimisations Mobile** 📱

#### Middleware MobileOptimization

**Détecte et optimise pour mobiles**

Fichier: `app/Http/Middleware/MobileOptimization.php`

**Enregistrement:**

```php
// bootstrap/app.php
$middleware->alias([
    'mobile.optimize' => \App\Http\Middleware\MobileOptimization::class,
]);
```

**Utilisation:**

```php
// routes/web.php
Route::get('/items', [ItemController::class, 'index'])
    ->middleware('mobile.optimize');
```

**Headers ajoutés:**

-   `X-Mobile-Detected: true` (si mobile)
-   `X-Data-Saver: active` (si Save-Data ON)

---

#### ImageOptimizationService

**Conversion WebP et images responsive**

Fichier: `app/Services/ImageOptimizationService.php`

##### Méthodes

**1. Convertir en WebP**

```php
$service = app(ImageOptimizationService::class);
$webpPath = $service->convertToWebP($imagePath, 80);
```

**2. Créer versions responsive**

```php
$versions = $service->createResponsiveVersions($imagePath);
// [
//   'small' => 'path_small.jpg',    // 320px
//   'medium' => 'path_medium.jpg',  // 768px
//   'large' => 'path_large.jpg',    // 1024px
//   'xlarge' => 'path_xlarge.jpg',  // 1920px
// ]
```

**3. Traitement complet**

```php
$result = $service->processImage($imagePath);
// {
//   "original": "path.jpg",
//   "webp": "path.webp",
//   "responsive": {...},
//   "placeholder": "data:image/jpeg;base64,..."
// }
```

**4. Blur Placeholder (LQIP)**

```php
$placeholder = $service->generateBlurPlaceholder($imagePath);
// Returns: base64 encoded tiny blurred image
```

---

#### Composant Vue OptimizedImage

**Lazy loading + WebP + Responsive**

Fichier: `resources/js/Components/OptimizedImage.vue`

**Usage:**

```vue
<script setup>
import OptimizedImage from "@/Components/OptimizedImage.vue";
</script>

<template>
    <OptimizedImage
        :src="item.image"
        :alt="item.name"
        :placeholder="item.placeholder"
        sizes="(max-width: 768px) 100vw, 50vw"
        aspect-ratio="16/9"
        object-fit="cover"
        :lazy="true"
    />
</template>
```

**Props:**

-   `src` (required): URL de l'image
-   `alt`: Texte alternatif
-   `placeholder`: Base64 blur placeholder
-   `sizes`: Attribute sizes pour responsive
-   `aspect-ratio`: Ratio (ex: "16/9", "1/1", "4/3")
-   `object-fit`: cover | contain | fill
-   `lazy`: Active lazy loading (défaut: true)

**Fonctionnalités:**

-   ✅ **WebP** automatique avec fallback JPEG/PNG
-   ✅ **Srcset responsive** (320px, 768px, 1024px, 1920px)
-   ✅ **Lazy loading** avec Intersection Observer
-   ✅ **Blur placeholder** pendant chargement
-   ✅ **Transition smooth** après chargement
-   ✅ **Fallback** si image non trouvée

---

## 🚀 **ACTIVATION ET UTILISATION**

### Étape 1: Activer Telescope (Dev seulement)

```bash
# Déjà installé ✅
# Accéder à: http://localhost:8000/telescope
```

### Étape 2: Configurer Backup Automatique

```bash
# Ajouter dans app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:run --only-db')->dailyAt('02:00');
    $schedule->command('backup:clean')->weekly();
}

# Tester manuellement
php artisan backup:run
```

### Étape 3: Activer Monitoring Dashboard

```bash
# Déjà configuré ✅
# Accéder à: http://localhost:8000/admin/monitoring
# (Nécessite compte admin)
```

### Étape 4: Intégrer MonitoringService dans le Code

```php
// Ajouter dans les contrôleurs critiques
use App\Services\MonitoringService;

public function __construct(protected MonitoringService $monitoring) {}

// Dans les méthodes importantes
$this->monitoring->recordPerformance('operation', $duration);
$this->monitoring->recordBusinessMetric('event', $value);
```

### Étape 5: Utiliser Optimisations Mobile

```php
// Routes importantes
Route::get('/items')->middleware('mobile.optimize');

// Dans les vues Vue
<OptimizedImage :src="image" :lazy="true" />
```

---

## 📊 **MÉTRIQUES DISPONIBLES**

### Dashboard Stats

1. **Base de données**

    - Total utilisateurs
    - Annonces actives
    - Commandes en attente
    - Revenus aujourd'hui
    - Commandes aujourd'hui

2. **Cache**

    - Taux de succès (hit rate %)
    - Nombre de hits
    - Nombre de misses

3. **Performance**

    - Temps de réponse moyen (ms)
    - Opérations lentes (>1s)
    - Total opérations

4. **Business Events**

    - Total événements
    - Dernier événement
    - Events par type

5. **Erreurs**
    - Total erreurs
    - Dernière erreur
    - Types d'erreurs

---

## 🔍 **MONITORING EN PRODUCTION**

### Recommandations

1. **Désactiver Telescope**

```env
TELESCOPE_ENABLED=false
```

2. **Configurer Alertes Email**

```php
// config/backup.php
'notifications' => [
    'mail' => [
        'to' => 'admin@vintapp.com',
    ],
],
```

3. **Utiliser Service Externe**

-   **Sentry** (erreurs): `composer require sentry/sentry-laravel`
-   **New Relic** (APM)
-   **Datadog** (infrastructure)

4. **Health Check externe**

```bash
# Ping toutes les 5 minutes
curl https://vintapp.com/admin/monitoring/health

# UptimeRobot, Pingdom, etc.
```

---

## 📱 **OPTIMISATIONS MOBILE - CHECKLIST**

### Performance

-   [x] Middleware détection mobile
-   [x] Conversion WebP automatique
-   [x] Images responsive (4 tailles)
-   [x] Lazy loading avec Intersection Observer
-   [x] Blur placeholder (LQIP)
-   [ ] Service Worker (PWA) - À faire
-   [ ] Offline cache - À faire

### UX Mobile

-   [ ] Touch gestures (swipe, pinch-zoom)
-   [ ] Bottom navigation
-   [ ] Pull-to-refresh
-   [ ] Haptic feedback
-   [ ] Dark mode auto

---

## 🎯 **PROCHAINES ÉTAPES**

1. **Tests Automatisés** ⭐

    - Tests MonitoringService
    - Tests ImageOptimizationService
    - Tests Dashboard

2. **PWA (Progressive Web App)**

    - Service Worker
    - Manifest.json
    - Offline mode
    - Install prompt

3. **Analytics Avancées**

    - Google Analytics 4
    - Conversion tracking
    - User behavior

4. **Notifications Push**
    - Firebase Cloud Messaging
    - Web Push API

---

## 📚 **DOCUMENTATION TECHNIQUE**

### Logs Disponibles

```bash
storage/logs/
├── laravel.log (général, 14j)
├── security.log (sécurité, 30j)
├── performance.log (lent >1s, 14j)
├── business.log (événements, 90j)
└── errors.log (erreurs, 30j)
```

### Cache Keys

```
monitoring:performance - 100 dernières opérations
monitoring:business - 100 derniers événements
monitoring:errors - 50 dernières erreurs
monitoring:cache_hits - Compteur hits
monitoring:cache_misses - Compteur misses
monitoring:db_stats - Stats BDD (TTL: 60s)
```

### API Endpoints

```
GET  /telescope - Dashboard Telescope
GET  /admin/monitoring - Dashboard Monitoring
GET  /admin/monitoring/stats - JSON stats
GET  /admin/monitoring/health - Health check
POST /admin/monitoring/reset - Reset métriques
```

---

## ✅ **RÉSUMÉ**

**Installé:**

-   ✅ Laravel Telescope (debugging)
-   ✅ Spatie Backup (BDD + fichiers)
-   ✅ MonitoringService (métriques)
-   ✅ Dashboard Monitoring (temps réel)
-   ✅ 4 canaux logging spécialisés
-   ✅ MobileOptimization middleware
-   ✅ ImageOptimizationService (WebP + responsive)
-   ✅ OptimizedImage Vue component

**Prêt pour:**

-   🔭 Debugging avec Telescope
-   💾 Backups automatiques
-   📊 Monitoring temps réel
-   📱 Images optimisées mobile
-   🚀 Déploiement production

**Documentation:** Ce fichier 📄
