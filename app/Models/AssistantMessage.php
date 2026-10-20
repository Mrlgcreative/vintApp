<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Message échangé avec l'assistant IA, conservé pour que la conversation
 * survive à la fermeture du widget ou de l'app.
 *
 * Données personnelles : les messages sont purgés après
 * self::RETENTION_DAYS jours (purge planifiée dans routes/console.php).
 */
class AssistantMessage extends Model
{
    use MassPrunable;

    /** Durée de conservation des conversations, en jours. */
    public const RETENTION_DAYS = 7;

    protected $fillable = ['user_id', 'role', 'content'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Lignes supprimées par `model:prune`. */
    public function prunable()
    {
        return static::where('created_at', '<', now()->subDays(self::RETENTION_DAYS));
    }
}
