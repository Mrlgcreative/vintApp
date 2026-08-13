<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends ApiController
{
    /**
     * Inscription (token Sanctum)
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Connexion (token Sanctum)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Les informations de connexion fournies sont incorrectes.',
            ], 401);
        }

        // Si la 2FA est active, émettre un pending_token pour le challenge au lieu du token complet
        if ($user->google2fa_enabled) {
            $pendingToken = $user->createToken('2fa_pending', ['2fa:pending']);

            return response()->json([
                'success' => true,
                'message' => 'Code 2FA requis.',
                'two_factor_required' => true,
                'pending_token' => $pendingToken->plainTextToken,
                'token_type' => 'Bearer',
            ], 200);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('API Login réussi', ['user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'email_verified_at' => $user->email_verified_at,
                'role' => $user->role ?? 'user',
                'two_factor_enabled' => (bool) $user->google2fa_enabled,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Déconnexion (révoque le token courant)
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            \App\Models\UserSession::where('session_id', 'sanctum-' . $token->id)
                ->where('is_active', true)
                ->update(['is_active' => false, 'logout_at' => now()]);

            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Utilisateur authentifié
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'email_verified_at' => $user->email_verified_at,
                'role' => $user->role ?? 'user',
                'two_factor_enabled' => (bool) $user->google2fa_enabled,
            ]
        ]);
    }

    /**
     * Envoi du lien de réinitialisation de mot de passe.
     *
     * POST /api/password/email
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => trans($status),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => trans($status),
            'errors' => ['email' => [trans($status)]],
        ], 422);
    }

    /**
     * Réinitialisation du mot de passe.
     *
     * POST /api/password/reset
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
                Log::info('API Password reset successful', ['user_id' => $user->id, 'email' => $user->email]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => trans($status),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => trans($status),
            'errors' => ['email' => [trans($status)]],
        ], 422);
    }
}
