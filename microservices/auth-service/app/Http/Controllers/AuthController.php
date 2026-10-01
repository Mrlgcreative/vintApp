<?php

namespace App\Http\Controllers;

use App\Events\UserAuthenticated;
use App\Events\UserRegistered;
use App\Models\Role;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Laravel\Sanctum\NewAccessToken;

class AuthController extends ApiController
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->assignDefaultRole($user);

        $token = $this->issueToken($user, $request);

        UserRegistered::dispatch($user);

        return $this->successResponse([
            'user' => $this->userPayload($user->load('roles')),
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 'Inscription réussie', [], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $user = User::where('email', $data['email'])->first();

        // Message unique pour email inconnu et mot de passe erroné : ne pas
        // révéler quelles adresses sont enregistrées.
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            Log::info('API Login refusé', ['email' => $data['email']]);

            return $this->errorResponse('Les informations de connexion fournies sont incorrectes.', 401);
        }

        if ($user->google2fa_enabled) {
            $pending = $user->createToken('2fa_pending', ['2fa:pending'], now()->addMinutes(
                (int) config('identity.two_factor_pending_ttl_minutes', 5)
            ));

            return $this->successResponse([
                'two_factor_required' => true,
                'pending_token' => $pending->plainTextToken,
                'token_type' => 'Bearer',
            ], 'Code 2FA requis.');
        }

        $token = $this->issueToken($user, $request, (bool) ($data['remember'] ?? false));

        Log::info('API Login réussi', ['user_id' => $user->id]);

        UserAuthenticated::dispatch($user);

        return $this->successResponse([
            'user' => $this->userPayload($user->load('roles')),
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 'Connexion réussie');
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $request->user()->sessions()
                ->where('session_id', 'sanctum-'.$token->id)
                ->where('is_active', true)
                ->update(['is_active' => false, 'logout_at' => now()]);

            $token->delete();
        }

        return $this->successResponse(null, 'Déconnexion réussie');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => $this->userPayload($request->user()->load('roles')),
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(['email' => $data['email']]);

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(null, trans($status));
        }

        return $this->errorResponse(trans($status), 422, ['email' => [trans($status)]]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'] ?? null,
                'token' => $data['token'],
            ],
            function (User $user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ])->save();

                // Un reset de mot de passe invalide les sessions ouvertes.
                $user->sessions()->where('is_active', true)->update([
                    'is_active' => false,
                    'logout_at' => now(),
                ]);

                event(new PasswordReset($user));

                Log::info('API Password reset réussi', ['user_id' => $user->id]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(null, trans($status));
        }

        return $this->errorResponse(trans($status), 422, ['email' => [trans($status)]]);
    }

    private function issueToken(User $user, Request $request, bool $remember = false): NewAccessToken
    {
        $token = $user->createToken(
            'auth_token',
            ['*'],
            now()->addDays((int) config('identity.token_ttl_days', 60))
        );

        UserSession::updateOrCreate(
            ['session_id' => 'sanctum-'.$token->accessToken->id],
            [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $this->detectDeviceType($request->userAgent()),
                'last_activity' => now(),
                'login_at' => now(),
                'is_active' => true,
            ]
        );

        return $token;
    }

    private function assignDefaultRole(User $user): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'user'],
            ['name' => 'Utilisateur', 'description' => 'Accès standard aux fonctionnalités']
        );

        $user->roles()->syncWithoutDetaching([$role->id]);
    }

    private function detectDeviceType(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'desktop';
        }

        if (preg_match('/mobile|android|iphone|ipod/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/ipad|tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }
}
