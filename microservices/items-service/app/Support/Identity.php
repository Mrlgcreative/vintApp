<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Lecteur de l'identité posée par `RequireServiceIdentity`.
 *
 * Les contrôleurs ne touchent jamais une table users : ils ne font que lire
 * l'identité déjà validée par auth-service.
 */
class Identity
{
    /**
     * @return array<string, mixed>|null
     */
    public static function from(Request $request): ?array
    {
        $identity = $request->attributes->get('vintapp_identity');

        return is_array($identity) ? $identity : null;
    }

    public static function id(Request $request): ?int
    {
        $id = self::from($request)['user_id'] ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    /**
     * @return array<int, string>
     */
    public static function roles(Request $request): array
    {
        $roles = self::from($request)['roles'] ?? [];

        if (! is_array($roles)) {
            return [];
        }

        return array_values(array_filter($roles, 'is_string'));
    }

    public static function isAdmin(Request $request): bool
    {
        return in_array('admin', self::roles($request), true);
    }

    public static function hasRole(Request $request, string ...$roles): bool
    {
        return array_intersect($roles, self::roles($request)) !== [];
    }
}
