<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'latitude',
        'longitude',
        'city',
        'country',
        'last_activity',
        'login_at',
        'logout_at',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'last_activity' => 'datetime',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Créer ou mettre à jour une session
     */
    public static function trackSession($userId, $sessionId, $request = null)
    {
        $agent = new Agent();
        $agent->setUserAgent($request ? $request->userAgent() : request()->userAgent());

        $ip = $request ? $request->ip() : request()->ip();

        $data = [
            'user_id' => $userId,
            'session_id' => $sessionId,
            'ip_address' => $ip,
            'user_agent' => $agent->getUserAgent(),
            'device_type' => static::getDeviceType($agent),
            'browser' => $agent->browser(),
            'os' => $agent->platform(),
            'last_activity' => now(),
            'is_active' => true,
        ];

        $session = static::updateOrCreate(
            ['session_id' => $sessionId],
            $data
        );

        // Mettre à jour last_seen pour alimenter User::isOnline() et les stats
        static::updateUserLastSeen($userId);

        // Géolocaliser si latitude/longitude vide (une seule fois par session)
        if (empty($session->latitude) && !static::isLocalIp($ip)) {
            static::geolocateSession($session, $ip);
        }

        return $session;
    }

    /**
     * Mettre à jour last_seen de l'utilisateur (2 min pour être "en ligne").
     */
    protected static function updateUserLastSeen($userId)
    {
        User::whereKey($userId)->update(['last_seen' => now()]);
    }

    /**
     * Géolocaliser une session via l'IP (api gratuite ip-api.com)
     */
    protected static function geolocateSession($session, $ip)
    {
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 2]]);
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,lat,lon,city,country", false, $ctx);

            if ($response) {
                $geo = json_decode($response, true);
                if (isset($geo['status']) && $geo['status'] === 'success') {
                    $session->update([
                        'latitude' => $geo['lat'] ?? null,
                        'longitude' => $geo['lon'] ?? null,
                        'city' => $geo['city'] ?? null,
                        'country' => $geo['country'] ?? null,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Silencieux: la géolocalisation est optionnelle
        }
    }

    /**
     * Vérifier si l'IP est locale (pas géolocalisable)
     */
    protected static function isLocalIp($ip)
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost'])
            || str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '172.');
    }

    /**
     * Déterminer le type d'appareil
     */
    protected static function getDeviceType(Agent $agent)
    {
        if ($agent->isMobile()) {
            return 'mobile';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } elseif ($agent->isDesktop()) {
            return 'desktop';
        }
        return 'unknown';
    }

    /**
     * Marquer une session comme inactive
     */
    public function markAsInactive()
    {
        $this->update([
            'is_active' => false,
            'logout_at' => now(),
        ]);
    }

    /**
     * Obtenir toutes les sessions actives
     */
    public static function getActiveSessions()
    {
        // Considérer une session active si last_activity < 5 minutes
        $threshold = now()->subMinutes(5);
        
        return static::with('user')
            ->where('is_active', true)
            ->where('last_activity', '>=', $threshold)
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    /**
     * Obtenir le nombre d'utilisateurs connectés
     */
    public static function getOnlineUsersCount()
    {
        $threshold = now()->subMinutes(5);
        
        return static::where('is_active', true)
            ->where('last_activity', '>=', $threshold)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Nettoyer les anciennes sessions
     */
    public static function cleanupOldSessions($days = 30)
    {
        $threshold = now()->subDays($days);
        
        return static::where('last_activity', '<', $threshold)
            ->where('is_active', false)
            ->delete();
    }

    /**
     * Obtenir l'icône de l'appareil
     */
    public function getDeviceIconAttribute()
    {
        return match($this->device_type) {
            'mobile' => 'fa-mobile-alt',
            'tablet' => 'fa-tablet-alt',
            'desktop' => 'fa-desktop',
            default => 'fa-question-circle'
        };
    }

    /**
     * Obtenir l'icône du navigateur
     */
    public function getBrowserIconAttribute()
    {
        $browser = strtolower($this->browser ?? '');
        
        if (str_contains($browser, 'chrome')) return 'fa-chrome';
        if (str_contains($browser, 'firefox')) return 'fa-firefox';
        if (str_contains($browser, 'safari')) return 'fa-safari';
        if (str_contains($browser, 'edge')) return 'fa-edge';
        if (str_contains($browser, 'opera')) return 'fa-opera';
        
        return 'fa-globe';
    }

    /**
     * Obtenir le texte de durée depuis la dernière activité
     */
    public function getLastActivityTextAttribute()
    {
        if (!$this->last_activity) {
            return 'Jamais';
        }

        $diff = now()->diffInMinutes($this->last_activity);
        
        if ($diff < 1) {
            return 'À l\'instant';
        } elseif ($diff < 60) {
            return "Il y a {$diff} min";
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return "Il y a {$hours}h";
        } else {
            $days = floor($diff / 1440);
            return "Il y a {$days}j";
        }
    }

    /**
     * Vérifier si la session est vraiment active (< 5 min)
     */
    public function getIsReallyActiveAttribute()
    {
        if (!$this->is_active || !$this->last_activity) {
            return false;
        }

        return now()->diffInMinutes($this->last_activity) < 5;
    }

    /**
     * Obtenir la localisation formatée
     */
    public function getLocationTextAttribute()
    {
        $parts = array_filter([$this->city, $this->country]);
        return !empty($parts) ? implode(', ', $parts) : 'Localisation inconnue';
    }
}
