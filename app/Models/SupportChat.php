<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'admin_id',
        'subject',
        'status',
        'priority',
        'category',
        'last_message_at',
        'closed_at',
        'metadata'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Utilisateur qui a créé la conversation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin assigné à la conversation
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Messages de la conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Dernier message de la conversation
     */
    public function lastMessage()
    {
        return $this->hasOne(SupportMessage::class)->latest();
    }

    /**
     * Messages non lus pour l'utilisateur
     */
    public function unreadMessagesForUser()
    {
        return $this->messages()->where('is_admin', true)->where('is_read', false);
    }

    /**
     * Messages non lus pour l'admin
     */
    public function unreadMessagesForAdmin()
    {
        return $this->messages()->where('is_admin', false)->where('is_read', false);
    }

    /**
     * Générer une référence unique
     */
    public static function generateReference()
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'SUP-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Créer une nouvelle conversation
     */
    public static function createNew($userId, $subject = null, $category = 'general', $metadata = null)
    {
        return self::create([
            'reference' => self::generateReference(),
            'user_id' => $userId,
            'subject' => $subject,
            'category' => $category,
            'status' => 'open',
            'priority' => 'normal',
            'metadata' => $metadata,
            'last_message_at' => now()
        ]);
    }

    /**
     * Marquer comme fermé
     */
    public function close()
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);
    }

    /**
     * Marquer comme en cours
     */
    public function markInProgress($adminId = null)
    {
        $data = ['status' => 'in_progress'];
        if ($adminId) {
            $data['admin_id'] = $adminId;
        }
        $this->update($data);
    }

    /**
     * Assigner à un admin
     */
    public function assignToAdmin($adminId)
    {
        $this->update([
            'admin_id' => $adminId,
            'status' => 'in_progress'
        ]);
    }

    /**
     * Obtenir le nombre de messages non lus
     */
    public function getUnreadCountForUserAttribute()
    {
        return $this->unreadMessagesForUser()->count();
    }

    /**
     * Obtenir le nombre de messages non lus pour admin
     */
    public function getUnreadCountForAdminAttribute()
    {
        return $this->unreadMessagesForAdmin()->count();
    }

    /**
     * Obtenir le statut formaté
     */
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            'open' => 'Ouvert',
            'in_progress' => 'En cours',
            'waiting_user' => 'En attente utilisateur',
            'closed' => 'Fermé',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir la priorité formatée
     */
    public function getFormattedPriorityAttribute()
    {
        return match($this->priority) {
            'low' => 'Faible',
            'normal' => 'Normale',
            'high' => 'Élevée',
            'urgent' => 'Urgente',
            default => 'Normale'
        };
    }

    /**
     * Obtenir la catégorie formatée
     */
    public function getFormattedCategoryAttribute()
    {
        return match($this->category) {
            'technical' => 'Technique',
            'account' => 'Compte',
            'payment' => 'Paiement',
            'order' => 'Commande',
            'general' => 'Général',
            default => 'Général'
        };
    }

    /**
     * Scopes pour les requêtes
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('admin_id');
    }
}