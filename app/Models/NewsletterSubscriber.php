<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'unsubscribe_token',
        'is_active',
        'email_verified',
        'verified_at',
        'receive_welcome',
        'receive_new_items',
        'receive_promotions',
        'receive_newsletters',
        'emails_sent',
        'emails_opened',
        'emails_clicked',
        'last_email_sent_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified' => 'boolean',
        'verified_at' => 'datetime',
        'receive_welcome' => 'boolean',
        'receive_new_items' => 'boolean',
        'receive_promotions' => 'boolean',
        'receive_newsletters' => 'boolean',
        'emails_sent' => 'integer',
        'emails_opened' => 'integer',
        'emails_clicked' => 'integer',
        'last_email_sent_at' => 'datetime',
    ];

    /**
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($subscriber) {
            if (empty($subscriber->unsubscribe_token)) {
                $subscriber->unsubscribe_token = Str::random(64);
            }
        });
    }

    /**
     * Scope pour les abonnés actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les abonnés vérifiés
     */
    public function scopeVerified($query)
    {
        return $query->where('email_verified', true);
    }

    /**
     * Scope pour les abonnés qui reçoivent les nouveaux articles
     */
    public function scopeReceivingNewItems($query)
    {
        return $query->active()->where('receive_new_items', true);
    }

    /**
     * Scope pour les abonnés qui reçoivent les promotions
     */
    public function scopeReceivingPromotions($query)
    {
        return $query->active()->where('receive_promotions', true);
    }

    /**
     * Incrémenter le compteur d'emails envoyés
     */
    public function incrementEmailsSent()
    {
        $this->increment('emails_sent');
        $this->update(['last_email_sent_at' => now()]);
    }

    /**
     * Incrémenter le compteur d'emails ouverts
     */
    public function incrementEmailsOpened()
    {
        $this->increment('emails_opened');
    }

    /**
     * Incrémenter le compteur de clics
     */
    public function incrementEmailsClicked()
    {
        $this->increment('emails_clicked');
    }

    /**
     * Vérifier l'email
     */
    public function verify()
    {
        $this->update([
            'email_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Se désabonner
     */
    public function unsubscribe()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Se réabonner
     */
    public function resubscribe()
    {
        $this->update(['is_active' => true]);
    }
}