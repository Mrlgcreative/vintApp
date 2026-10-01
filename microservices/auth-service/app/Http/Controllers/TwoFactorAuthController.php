<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorAuthController extends ApiController
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA;
    }

    /**
     * POST /v1/two-factor/verify
     *
     * Échange le pending_token émis par /v1/login contre un token complet.
     */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();
        $token = $user?->currentAccessToken();

        if (! $user || ! $user->google2fa_enabled) {
            return $this->errorResponse('Authentification à deux facteurs non configurée.', 422);
        }

        if (! $token || ! $token->can('2fa:pending')) {
            return $this->errorResponse('Token 2FA invalide. Reconnectez-vous.', 422);
        }

        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $data['code'])
            || $this->verifyRecoveryCode($user, $data['code']);

        if (! $valid) {
            return $this->errorResponse('Code invalide.', 422);
        }

        $token->delete();

        $accessToken = $user->createToken(
            'auth_token',
            ['*'],
            now()->addDays((int) config('identity.token_ttl_days', 60))
        );

        return $this->successResponse([
            'user' => $this->userPayload($user->load('roles')),
            'token' => $accessToken->plainTextToken,
            'token_type' => 'Bearer',
        ], 'Code vérifié avec succès.');
    }

    /**
     * POST /v1/two-factor/enable
     *
     * Génère le secret + QR code. La 2FA n'est active qu'après /confirm.
     */
    public function enable(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->google2fa_secret) {
            $user->google2fa_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        return $this->successResponse([
            'qr_code' => $this->qrCodeInline($user),
            'secret' => $user->google2fa_secret,
            'recovery_codes' => $this->generateRecoveryCodes(),
        ], 'Scannez le QR code dans votre application d’authentification.');
    }

    public function confirm(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'numeric', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $user->google2fa_secret) {
            return $this->errorResponse('Activez d’abord la 2FA pour obtenir un secret.', 422);
        }

        if (! $this->google2fa->verifyKey($user->google2fa_secret, $data['code'])) {
            return $this->errorResponse('Code invalide. Veuillez réessayer.', 422);
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'google2fa_enabled' => true,
        ])->save();

        return $this->successResponse([
            'recovery_codes' => $recoveryCodes,
        ], 'Authentification à deux facteurs activée avec succès !');
    }

    public function disable(Request $request): JsonResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('Mot de passe incorrect.', 422);
        }

        $user->forceFill([
            'google2fa_enabled' => false,
            'google2fa_secret' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        return $this->successResponse(null, 'Authentification à deux facteurs désactivée.');
    }

    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('Mot de passe incorrect.', 422);
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();

        return $this->successResponse([
            'recovery_codes' => $recoveryCodes,
        ], 'Codes de récupération régénérés.');
    }

    private function qrCodeInline(User $user): string
    {
        $url = $this->google2fa->getQRCodeUrl(
            (string) config('app.name'),
            (string) $user->email,
            (string) $user->google2fa_secret
        );

        return (string) QrCode::format('svg')
            ->size(400)
            ->generate($url);
    }

    private function generateRecoveryCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10).'-'.Str::random(10);
        }

        return $codes;
    }

    /**
     * Un code de récupération est à usage unique : il est retiré de la liste
     * dès qu'il est accepté.
     */
    private function verifyRecoveryCode(User $user, string $code): bool
    {
        if (! $user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (! is_array($recoveryCodes)) {
            return false;
        }

        $key = array_search($code, $recoveryCodes, true);

        if ($key === false) {
            return false;
        }

        unset($recoveryCodes[$key]);

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
        ])->save();

        return true;
    }
}
