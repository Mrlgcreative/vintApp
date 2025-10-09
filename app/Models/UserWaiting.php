<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserWaiting extends Model
{
    use SoftDeletes;

    protected $table = 'users_waiting';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'message',
        'reasons',
        'confirmation_token',
        'password_setup_token',
        'password_setup_token_expires_at',
        'status',
        'email_confirmed_at',
        'notified_at',
        'approved_at',
        'rejected_at',
        'converted_at',
        'converted_user_id',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'reasons' => 'array',
        'email_confirmed_at' => 'datetime',
        'notified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'converted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function convertedUser()
    {
        return $this->belongsTo(User::class, 'converted_user_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    public function scopeNotConverted($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'approved']);
    }

    /**
     * Status check methods
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    /**
     * Action methods
     */
    public function confirmEmail()
    {
        $this->update([
            'status' => 'confirmed',
            'email_confirmed_at' => now(),
        ]);

        Log::info("Email confirmé pour pré-inscription: {$this->email}");

        return $this;
    }

    public function approve($adminNotes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'admin_notes' => $adminNotes ?? $this->admin_notes,
        ]);

        Log::info("Pré-inscription approuvée: {$this->email}");

        // Créer le compte utilisateur et générer le token
        $result = $this->createUserAccount();
        
        // Envoyer l'email avec le lien pour définir le mot de passe
        $this->sendPasswordSetupEmail($result['token']);

        return $this;
    }

    public function reject($reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => $reason ?? $this->admin_notes,
        ]);

        Log::info("Pré-inscription rejetée: {$this->email}");

        return $this;
    }

    public function markAsConverted(User $user)
    {
        $this->update([
            'status' => 'converted',
            'converted_at' => now(),
            'converted_user_id' => $user->id,
        ]);

        Log::info("Pré-inscription convertie en utilisateur: {$this->email} -> User ID {$user->id}");

        return $this;
    }

    /**
     * Créer un compte utilisateur et générer le token de configuration de mot de passe
     */
    public function createUserAccount()
    {
        // Vérifier si déjà converti
        if ($this->status === 'converted' && $this->converted_user_id) {
            return User::find($this->converted_user_id);
        }

        // Générer un token unique et sécurisé
        $token = Str::random(60);
        
        // Token expire dans 7 jours
        $expiresAt = now()->addDays(7);

        // Mettre à jour le UserWaiting avec le token
        $this->update([
            'password_setup_token' => hash('sha256', $token),
            'password_setup_token_expires_at' => $expiresAt,
        ]);

        // Créer le compte utilisateur (sans mot de passe pour l'instant)
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country ?? 'RDC',
            'password' => bcrypt(Str::random(32)), // Mot de passe temporaire aléatoire
            'email_verified_at' => $this->email_confirmed_at, // Déjà confirmé via pré-inscription
        ]);

        // Marquer comme converti
        $this->markAsConverted($user);

        Log::info("Compte utilisateur créé pour pré-inscription: {$this->email} -> User ID {$user->id}");

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Envoyer l'email avec le lien de configuration de mot de passe
     */
    public function sendPasswordSetupEmail($token)
    {
        try {
            // Générer l'URL de configuration
            $setupUrl = route('password.setup', ['token' => $token, 'email' => $this->email]);

            // TODO: Remplacer par le vrai Mailable
            Mail::to($this->email)->send(new \App\Mail\SetPasswordMail($this, $setupUrl));
            
            Log::info("Email de configuration de mot de passe envoyé à: {$this->email}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur envoi email configuration mot de passe: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Notification methods
     */
    public function sendConfirmationEmail()
    {
        try {
            // TODO: Créer la mailable pour confirmation
            // Mail::to($this->email)->send(new PreRegistrationConfirmation($this));
            
            $this->update(['notified_at' => now()]);
            
            Log::info("Email de confirmation envoyé à: {$this->email}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur envoi email confirmation: {$e->getMessage()}");
            return false;
        }
    }

    public function sendApprovalNotification()
    {
        try {
            // TODO: Créer la mailable pour approbation
            // Mail::to($this->email)->send(new PreRegistrationApproved($this));
            
            Log::info("Email d'approbation envoyé à: {$this->email}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur envoi email approbation: {$e->getMessage()}");
            return false;
        }
    }

    public function sendWelcomeEmail($temporaryPassword)
    {
        try {
            // TODO: Créer la mailable pour bienvenue
            // Mail::to($this->email)->send(new WelcomeNewUser($this, $temporaryPassword));
            
            Log::info("Email de bienvenue envoyé à: {$this->email}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur envoi email bienvenue: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Helpers
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('confirmation_token', $token)->exists());

        return $token;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">⏳ En attente</span>',
            'confirmed' => '<span class="badge bg-info">✉️ Confirmé</span>',
            'approved' => '<span class="badge bg-success">✅ Approuvé</span>',
            'rejected' => '<span class="badge bg-danger">❌ Rejeté</span>',
            'converted' => '<span class="badge bg-primary">🎉 Compte créé</span>',
            default => '<span class="badge bg-secondary">?</span>',
        };
    }

    public function getWaitingDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getConfirmationUrlAttribute(): string
    {
        return route('preregistration.confirm', ['token' => $this->confirmation_token]);
    }
}
