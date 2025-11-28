<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Afficher la page de configuration 2FA
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('auth.two-factor', [
            'user' => $user,
            'enabled' => $user->google2fa_enabled,
        ]);
    }

    /**
     * Afficher la page de challenge 2FA après connexion
     */
    public function showChallenge()
    {
        // Vérifier que l'utilisateur doit passer par 2FA
        if (!session('2fa_required') || !session('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Vérifier que c'est bien le bon utilisateur
        if (!$user || $user->id !== session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge', [
            'user' => $user,
        ]);
    }

    /**
     * Activer l'authentification à deux facteurs
     */
    public function enable(Request $request)
    {
        $user = Auth::user();

        // Générer un secret unique si pas déjà généré
        if (!$user->google2fa_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        }

        // Générer le QR code
        $qrCodeUrl = $this->getQRCodeInline($user);

        // Générer les codes de récupération
        $recoveryCodes = $this->generateRecoveryCodes();

        return response()->json([
            'success' => true,
            'qrCode' => $qrCodeUrl,
            'secret' => $user->google2fa_secret,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Confirmer l'activation de 2FA avec le code
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        $user = Auth::user();

        // Vérifier le code
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Code invalide. Veuillez réessayer.',
            ], 422);
        }

        // Générer et sauvegarder les codes de récupération
        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->google2fa_enabled = true;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Authentification à deux facteurs activée avec succès!',
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Désactiver l'authentification à deux facteurs
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        // Vérifier le mot de passe
        if (!password_verify($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect.',
            ], 422);
        }

        // Désactiver 2FA
        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Authentification à deux facteurs désactivée.',
        ]);
    }

    /**
     * Vérifier le code 2FA lors de la connexion
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user || !$user->google2fa_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification à deux facteurs non configurée.',
            ], 422);
        }

        // Vérifier que l'utilisateur est en train de passer le challenge
        if (!session('2fa_required') || session('2fa_user_id') !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Session invalide.',
            ], 422);
        }

        // Vérifier le code normal
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->code);

        // Si le code normal ne fonctionne pas, vérifier les codes de récupération
        if (!$valid) {
            $valid = $this->verifyRecoveryCode($user, $request->code);
        }

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Code invalide.',
            ], 422);
        }

        // Marquer la session comme vérifiée pour 2FA
        session(['2fa_verified' => true]);
        session()->forget(['2fa_required', '2fa_user_id']);

        return response()->json([
            'success' => true,
            'message' => 'Code vérifié avec succès.',
            'redirect' => route('home')
        ]);
    }

    /**
     * Régénérer les codes de récupération
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        // Vérifier le mot de passe
        if (!password_verify($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect.',
            ], 422);
        }

        // Générer de nouveaux codes
        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Codes de récupération régénérés.',
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Générer le QR code pour Google Authenticator
     */
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

    /**
     * Générer des codes de récupération
     */
    protected function generateRecoveryCodes()
    {
        $recoveryCodes = [];
        
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10) . '-' . Str::random(10);
        }

        return $recoveryCodes;
    }

    /**
     * Vérifier un code de récupération
     */
    protected function verifyRecoveryCode($user, $code)
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        $key = array_search($code, $recoveryCodes);

        if ($key !== false) {
            // Supprimer le code utilisé
            unset($recoveryCodes[$key]);
            $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
            $user->save();

            return true;
        }

        return false;
    }
}

