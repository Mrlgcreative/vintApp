<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthController extends ApiController
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Vérifier le code 2FA lors du login mobile.
     * Attend le pending_token émis par POST /api/login quand la 2FA est active.
     *
     * POST /api/two-factor/verify
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user || !$user->google2fa_enabled) {
            return $this->errorResponse('Authentification à deux facteurs non configurée.', 422);
        }

        $token = $user->currentAccessToken();
        if (!$token || !$token->can('2fa:pending')) {
            return $this->errorResponse('Token 2FA invalide. Reconnectez-vous.', 422);
        }

        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            $valid = $this->verifyRecoveryCode($user, $request->code);
        }

        if (!$valid) {
            return $this->errorResponse('Code invalide.', 422);
        }

        $token->delete();

        $accessToken = $user->createToken('auth_token');

        return response()->json([
            'success' => true,
            'message' => 'Code vérifié avec succès.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'email_verified_at' => $user->email_verified_at,
                'role' => $user->role ?? 'user',
                'two_factor_enabled' => (bool) $user->google2fa_enabled,
            ],
            'token' => $accessToken->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Activer la 2FA : génère le secret + QR code + codes de récupération.
     *
     * POST /api/two-factor/enable
     */
    public function enable(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        return $this->successResponse([
            'qrCode' => $this->getQRCodeInline($user),
            'secret' => $user->google2fa_secret,
            'recoveryCodes' => $this->generateRecoveryCodes(),
        ], 'Scannez le QR code dans Google Authenticator.');
    }

    /**
     * Confirmer l'activation de la 2FA avec le code.
     *
     * POST /api/two-factor/confirm
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        $user = $request->user();

        if (!$user->google2fa_secret) {
            return $this->errorResponse('Activez d\'abord la 2FA pour obtenir un secret.', 422);
        }

        if (!$this->google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            return $this->errorResponse('Code invalide. Veuillez réessayer.', 422);
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->google2fa_enabled = true;
        $user->save();

        return $this->successResponse([
            'recoveryCodes' => $recoveryCodes,
        ], 'Authentification à deux facteurs activée avec succès!');
    }

    /**
     * Désactiver la 2FA (mot de passe requis).
     *
     * POST /api/two-factor/disable
     */
    public function disable(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!password_verify($request->password, $user->password)) {
            return $this->errorResponse('Mot de passe incorrect.', 422);
        }

        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return $this->successResponse(null, 'Authentification à deux facteurs désactivée.');
    }

    /**
     * Régénérer les codes de récupération (mot de passe requis).
     *
     * POST /api/two-factor/regenerate-codes
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!password_verify($request->password, $user->password)) {
            return $this->errorResponse('Mot de passe incorrect.', 422);
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return $this->successResponse([
            'recoveryCodes' => $recoveryCodes,
        ], 'Codes de récupération régénérés.');
    }

    protected function getQRCodeInline($user)
    {
        $companyName = config('app.name');
        $companyEmail = $user->email;

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $user->google2fa_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeImage = $writer->writeString($qrCodeUrl);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCodeImage);
    }

    protected function generateRecoveryCodes()
    {
        $recoveryCodes = [];

        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10) . '-' . Str::random(10);
        }

        return $recoveryCodes;
    }

    protected function verifyRecoveryCode($user, $code)
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        $key = array_search($code, $recoveryCodes);

        if ($key !== false) {
            unset($recoveryCodes[$key]);
            $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
            $user->save();

            return true;
        }

        return false;
    }
}
