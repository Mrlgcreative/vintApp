<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

abstract class ApiController
{
    protected function successResponse(mixed $data = null, string $message = 'OK', array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

    protected function errorResponse(string $message = 'Erreur', int $status = 400, array $errors = [], mixed $data = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'meta' => (object) [],
        ];

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    /**
     * Représentation publique d'un utilisateur. Partagée par /me et
     * l'introspection, pour que les autres services voient la même forme.
     */
    protected function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'public_id' => $user->public_id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'phone' => $user->phone,
            'locale' => $user->locale,
            'email_verified_at' => $user->email_verified_at,
            'roles' => $user->roleSlugs(),
            'two_factor_enabled' => (bool) $user->google2fa_enabled,
        ];
    }
}
