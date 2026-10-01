<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class TokenIntrospectionService
{
    /**
     * Résout un token Sanctum en identité, sans exposer la table users.
     *
     * Retourne null si le token est inconnu, expiré, révoqué, ou s'il
     * s'agit d'un token 2FA en attente qui n'a jamais été validé.
     */
    public function resolve(string $plainTextToken): ?array
    {
        $token = PersonalAccessToken::findToken($plainTextToken);

        if (! $token || ! $token->can('*')) {
            return null;
        }

        if ($token->expires_at && Carbon::parse($token->expires_at)->isPast()) {
            return null;
        }

        $user = $token->tokenable;

        if (! $user instanceof User) {
            return null;
        }

        if (! $this->sessionStillActive($token)) {
            return null;
        }

        $token->forceFill(['last_used_at' => now()])->save();

        return [
            'user_id' => $user->id,
            'public_id' => $user->public_id,
            'email' => $user->email,
            'roles' => $user->roleSlugs(),
            'two_factor_enabled' => (bool) $user->google2fa_enabled,
            'token_id' => $token->id,
            'abilities' => $token->abilities ?? [],
            'issued_at' => $token->created_at,
            'expires_at' => $token->expires_at,
        ];
    }

    /**
     * Une session révoquée (déconnexion) invalide aussi les tokens
     * correspondants, même si le token existe encore en base.
     */
    private function sessionStillActive(PersonalAccessToken $token): bool
    {
        $revoked = $token->tokenable
            ?->sessions()
            ->where('session_id', 'sanctum-'.$token->id)
            ->where('is_active', false)
            ->exists();

        return ! $revoked;
    }
}
