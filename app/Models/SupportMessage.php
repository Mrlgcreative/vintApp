<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_chat_id',
        'user_id',
        'message',
        'attachments',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_admin' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Conversation de support
     */
    public function supportChat(): BelongsTo
    {
        return $this->belongsTo(SupportChat::class);
    }

    /**
     * Utilisateur qui a envoyé le message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Créer un nouveau message
     */
    public static function createNew($supportChatId, $userId, $message, $isAdmin = false, $attachments = null)
    {
        $supportMessage = new self();
        $supportMessage->support_chat_id = $supportChatId;
        $supportMessage->user_id = $userId;
        $supportMessage->message = $message;
        $supportMessage->is_admin = $isAdmin;
        $supportMessage->attachments = $attachments;
        $supportMessage->save();

        // Mettre à jour la conversation avec le timestamp du dernier message
        SupportChat::find($supportChatId)->update([
            'last_message_at' => now()
        ]);

        return $supportMessage;
    }

    /**
     * Obtenir le nom de l'expéditeur
     */
    public function getSenderNameAttribute()
    {
        return $this->user->name ?? 'Utilisateur supprimé';
    }

    /**
     * Vérifier si le message a des pièces jointes
     */
    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    /**
     * Obtenir le message formaté pour l'affichage
     */
    public function getFormattedMessageAttribute()
    {
        return nl2br(e($this->message));
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeFromAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeFromUser($query)
    {
        return $query->where('is_admin', false);
    }

    public function scopeForChat($query, $chatId)
    {
        return $query->where('support_chat_id', $chatId);
    }
}