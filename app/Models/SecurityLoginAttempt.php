<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Journalisation d'une tentative de connexion (web ou API).
 *
 * Utilisée pour :
 *  - l'affichage des tentatives dans le dashboard de monitoring,
 *  - la détection de force brute (agrégats par email et par IP).
 */
class SecurityLoginAttempt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'security_login_attempts';

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'route',
        'guard',
        'success',
        'status_code',
        'attempts',
        'throttle_key',
        'created_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'status_code' => 'integer',
        'attempts' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Enregistre une tentative de connexion de façon fiable (jamais bloquant).
     */
    public static function record(array $data): void
    {
        try {
            static::create([
                'email' => isset($data['email']) ? strtolower((string) $data['email']) : null,
                'ip_address' => (string) ($data['ip_address'] ?? request()->ip()),
                'user_agent' => (string) ($data['user_agent'] ?? request()->userAgent()),
                'route' => (string) ($data['route'] ?? ''),
                'guard' => (string) ($data['guard'] ?? ''),
                'success' => !empty($data['success']),
                'status_code' => $data['status_code'] ?? null,
                'attempts' => (int) ($data['attempts'] ?? 1),
                'throttle_key' => $data['throttle_key'] ?? null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Ne jamais faire échouer le login à cause de la journalisation.
            logger()->error('Échec journalisation tentative de connexion', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Nombre de tentatives échouées depuis une même IP dans une fenêtre.
     */
    public static function failedFromIp(string $ip, int $minutes): int
    {
        return static::where('ip_address', $ip)
            ->where('success', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->sum('attempts');
    }

    /**
     * Nombre de tentatives échouées pour un même email dans une fenêtre.
     */
    public static function failedForEmail(string $email, int $minutes): int
    {
        return static::where('email', strtolower($email))
            ->where('success', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->sum('attempts');
    }

    /**
     * Tentatives récentes (pour le dashboard monitoring).
     */
    public static function recent(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return static::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère les IP avec un nombre anormal d'échecs dans une fenêtre.
     */
    public static function suspiciousIps(int $threshold, int $minutes): \Illuminate\Support\Collection
    {
        return static::select('ip_address', \Illuminate\Support\Facades\DB::raw('SUM(attempts) as attempts'))
            ->where('success', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->groupBy('ip_address')
            ->havingRaw('SUM(attempts) >= ?', [$threshold])
            ->orderByDesc('attempts')
            ->get();
    }

    /**
     * Récupère les emails ciblés par un nombre anormal d'échecs dans une fenêtre.
     */
    public static function suspiciousEmails(int $threshold, int $minutes): \Illuminate\Support\Collection
    {
        return static::select('email', 'ip_address', \Illuminate\Support\Facades\DB::raw('SUM(attempts) as attempts'))
            ->where('success', false)
            ->whereNotNull('email')
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->groupBy('email', 'ip_address')
            ->havingRaw('SUM(attempts) >= ?', [$threshold])
            ->orderByDesc('attempts')
            ->get();
    }
}
