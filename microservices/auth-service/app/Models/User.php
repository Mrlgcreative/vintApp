<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasPublicId, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'bio',
        'address',
        'location',
        'latitude',
        'longitude',
        'city',
        'commune',
        'location_updated_at',
        'theme_preference',
        'locale',
        'password',
        'newsletter_subscribed',
        'email_verified_at',
        'google2fa_enabled',
        'verification_code',
        'verification_code_expires_at',
        'fcm_token',
        'device_type',
        'fcm_token_updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
        'google2fa_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'newsletter_subscribed' => 'boolean',
            'google2fa_enabled' => 'boolean',
            'last_seen' => 'datetime',
            'location_updated_at' => 'datetime',
            'fcm_token_updated_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() === count($roles);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function isSeller(): bool
    {
        return $this->hasRole('vendeur');
    }

    public function isExpert(): bool
    {
        return $this->hasRole('expert');
    }

    /**
     * Slugs des rôles, pour l'introspection inter-services.
     */
    public function roleSlugs(): array
    {
        return $this->roles->pluck('slug')->all();
    }

    public function generateVerificationCode(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(15),
        ])->save();

        return $code;
    }
}
