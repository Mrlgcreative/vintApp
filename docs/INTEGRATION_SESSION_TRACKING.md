# 📝 Guide d'Intégration du Tracking de Sessions

## 🔧 Tracking Automatique des Sessions Utilisateurs

Pour que le système de tracking des utilisateurs connectés fonctionne automatiquement, vous devez intégrer le tracking dans votre système d'authentification.

### Option 1 : Middleware de Tracking (Recommandé)

Créez un middleware qui track automatiquement chaque requête :

```bash
php artisan make:middleware TrackUserSession
```

**app/Http/Middleware/TrackUserSession.php** :
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;

class TrackUserSession
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            UserSession::trackSession(
                Auth::id(),
                session()->getId(),
                $request
            );
        }

        return $next($request);
    }
}
```

**Enregistrer le middleware dans `app/Http/Kernel.php`** :
```php
protected $middlewareGroups = [
    'web' => [
        // ... autres middlewares
        \App\Http\Middleware\TrackUserSession::class,
    ],
];
```

---

### Option 2 : Event Listener

Créez un listener qui écoute l'événement de connexion :

```bash
php artisan make:listener TrackUserLogin
```

**app/Listeners/TrackUserLogin.php** :
```php
<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserSession;

class TrackUserLogin
{
    public function handle(Login $event)
    {
        UserSession::trackSession(
            $event->user->id,
            session()->getId(),
            request()
        );
    }
}
```

**Enregistrer dans `app/Providers/EventServiceProvider.php`** :
```php
protected $listen = [
    \Illuminate\Auth\Events\Login::class => [
        \App\Listeners\TrackUserLogin::class,
    ],
    
    \Illuminate\Auth\Events\Logout::class => [
        function ($event) {
            UserSession::where('user_id', $event->user->id)
                ->where('session_id', session()->getId())
                ->first()
                ?->markAsInactive();
        },
    ],
];
```

---

### Option 3 : Dans le LoginController

Si vous avez un contrôleur de login personnalisé :

**app/Http/Controllers/Auth/LoginController.php** :
```php
use App\Models\UserSession;

protected function authenticated(Request $request, $user)
{
    UserSession::trackSession(
        $user->id,
        session()->getId(),
        $request
    );
}
```

---

## 🧹 Nettoyage Automatique des Vieilles Sessions

### Créer une Commande Artisan

```bash
php artisan make:command CleanupOldSessions
```

**app/Console/Commands/CleanupOldSessions.php** :
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSession;

class CleanupOldSessions extends Command
{
    protected $signature = 'sessions:cleanup {--days=30}';
    protected $description = 'Nettoyer les anciennes sessions utilisateurs';

    public function handle()
    {
        $days = $this->option('days');
        $deleted = UserSession::cleanupOldSessions($days);
        
        $this->info("✅ {$deleted} session(s) supprimée(s) (plus de {$days} jours)");
    }
}
```

### Planifier l'Exécution

**app/Console/Kernel.php** :
```php
protected function schedule(Schedule $schedule)
{
    // Nettoyer les sessions tous les jours à 2h du matin
    $schedule->command('sessions:cleanup')->dailyAt('02:00');
}
```

---

## 🌍 Ajouter la Géolocalisation (Optionnel)

Pour obtenir la localisation approximative à partir de l'IP :

### Installation
```bash
composer require geoip2/geoip2:~2.0
```

### Télécharger la base GeoLite2

1. Créer un compte sur https://www.maxmind.com/
2. Télécharger `GeoLite2-City.mmdb`
3. Placer dans `storage/app/geoip/`

### Mise à jour du Modèle

**app/Models/UserSession.php** :
```php
use GeoIp2\Database\Reader;

public static function trackSession($userId, $sessionId, $request = null)
{
    $agent = new Agent();
    $agent->setUserAgent($request ? $request->userAgent() : request()->userAgent());
    
    $ipAddress = $request ? $request->ip() : request()->ip();
    
    // Géolocalisation
    $location = static::getLocationFromIp($ipAddress);

    $data = [
        'user_id' => $userId,
        'session_id' => $sessionId,
        'ip_address' => $ipAddress,
        'user_agent' => $agent->getUserAgent(),
        'device_type' => static::getDeviceType($agent),
        'browser' => $agent->browser(),
        'os' => $agent->platform(),
        'latitude' => $location['latitude'] ?? null,
        'longitude' => $location['longitude'] ?? null,
        'city' => $location['city'] ?? null,
        'country' => $location['country'] ?? null,
        'last_activity' => now(),
        'is_active' => true,
    ];

    return static::updateOrCreate(
        ['session_id' => $sessionId],
        $data
    );
}

protected static function getLocationFromIp($ip)
{
    try {
        // Ignorer les IP locales
        if ($ip === '127.0.0.1' || str_starts_with($ip, '192.168.')) {
            return [];
        }

        $reader = new Reader(storage_path('app/geoip/GeoLite2-City.mmdb'));
        $record = $reader->city($ip);

        return [
            'latitude' => $record->location->latitude,
            'longitude' => $record->location->longitude,
            'city' => $record->city->name,
            'country' => $record->country->isoCode,
        ];
    } catch (\Exception $e) {
        \Log::warning('GeoIP lookup failed: ' . $e->getMessage());
        return [];
    }
}
```

---

## 📊 Exemple de Dashboard avec Stats

Ajoutez dans votre **AdminController::dashboard()** :

```php
public function dashboard()
{
    // ... stats existantes

    // Stats sessions
    $stats['online_users'] = \App\Models\UserSession::getOnlineUsersCount();
    
    $stats['sessions_by_device'] = [
        'mobile' => \App\Models\UserSession::where('is_active', true)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->where('device_type', 'mobile')
            ->count(),
        'desktop' => \App\Models\UserSession::where('is_active', true)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->where('device_type', 'desktop')
            ->count(),
        'tablet' => \App\Models\UserSession::where('is_active', true)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->where('device_type', 'tablet')
            ->count(),
    ];

    return view('admin.dashboard', compact('stats', ...));
}
```

---

## 🔔 Notifications en Temps Réel (WebSockets - Avancé)

Pour des notifications instantanées lors de nouvelles connexions :

### Installation Laravel Echo + Pusher

```bash
composer require pusher/pusher-php-server
npm install --save-dev laravel-echo pusher-js
```

### Configuration

**.env** :
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-key
PUSHER_APP_SECRET=your-secret
PUSHER_APP_CLUSTER=eu
```

### Event de Nouvelle Connexion

```bash
php artisan make:event UserConnected
```

**app/Events/UserConnected.php** :
```php
<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UserConnected implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $user;
    public $session;

    public function __construct(User $user, $session)
    {
        $this->user = $user;
        $this->session = $session;
    }

    public function broadcastOn()
    {
        return new Channel('admin-dashboard');
    }

    public function broadcastAs()
    {
        return 'user.connected';
    }
}
```

### Écouter dans la Vue

**resources/views/admin/users/online.blade.php** :
```javascript
// Ajouter avant la fermeture de </body>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Configuration Echo
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env("PUSHER_APP_KEY") }}',
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        encrypted: true
    });

    // Écouter les nouvelles connexions
    window.Echo.channel('admin-dashboard')
        .listen('.user.connected', (e) => {
            console.log('Nouvel utilisateur connecté:', e.user);
            
            // Afficher une notification
            showNotification(`${e.user.name} vient de se connecter`);
            
            // Actualiser automatiquement
            refreshData();
        });

    function showNotification(message) {
        // Afficher un toast ou notification
        alert(message);
    }
</script>
```

---

## 🎯 Checklist d'Intégration

- [ ] Migrations exécutées (`php artisan migrate`)
- [ ] Package `jenssegers/agent` installé
- [ ] Middleware ou Event Listener configuré
- [ ] Commande de nettoyage planifiée
- [ ] Menu admin mis à jour
- [ ] Tests de connexion/déconnexion
- [ ] (Optionnel) GeoIP configuré
- [ ] (Optionnel) WebSockets configuré

---

## 🧪 Tests Manuels

### Tester le Tracking
1. Se connecter avec plusieurs navigateurs/appareils
2. Aller sur `/admin/users/online`
3. Vérifier que tous les utilisateurs apparaissent
4. Tester les filtres et la recherche
5. Attendre 10 secondes pour voir l'actualisation auto

### Tester le Nettoyage
```bash
php artisan sessions:cleanup --days=1
```

### Vérifier la Base de Données
```sql
SELECT * FROM user_sessions WHERE is_active = 1;
SELECT COUNT(*) FROM user_sessions WHERE last_activity >= NOW() - INTERVAL 5 MINUTE;
```

---

## 📞 Dépannage

### Sessions ne s'enregistrent pas
- Vérifier que le middleware est bien enregistré
- Vérifier les logs : `storage/logs/laravel.log`
- Tester manuellement :
  ```php
  UserSession::trackSession(Auth::id(), session()->getId(), request());
  ```

### Actualisation ne fonctionne pas
- Vérifier la console JavaScript pour les erreurs
- S'assurer que la route `/admin/users/online/data` est accessible
- Tester l'URL directement dans le navigateur

### GeoIP ne fonctionne pas
- Vérifier que le fichier `.mmdb` existe
- Vérifier les permissions du dossier `storage/app/geoip/`
- Tester avec une IP publique (pas 127.0.0.1)

---

Vous êtes maintenant prêt à utiliser le système complet ! 🚀
