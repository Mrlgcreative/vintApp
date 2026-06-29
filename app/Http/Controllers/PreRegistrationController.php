<?php

namespace App\Http\Controllers;

use App\Models\UserWaiting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class PreRegistrationController extends Controller
{
    /**
     * Afficher le formulaire de pré-inscription
     */
    public function index()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        Log::info('🔄 Préinscription refusée (désactivée)', $request->all());
        return response()->json([
            'success' => false,
            'message' => 'Les pré-inscriptions sont actuellement fermées.'
        ], 403);
    }

    /**
     * Page de succès après inscription
     */
    public function success()
    {
        return view('preregistration.success');
    }

    /**
     * Confirmer l'email via le token
     */
    public function confirm($token)
    {
        $userWaiting = UserWaiting::where('confirmation_token', $token)->first();

        if (!$userWaiting) {
            return redirect()->route('preregistration.index')
                ->with('error', 'Lien de confirmation invalide.');
        }

        if ($userWaiting->isConfirmed() || $userWaiting->isApproved()) {
            return redirect()->route('preregistration.already-confirmed')
                ->with('info', 'Votre email a déjà été confirmé !');
        }

        // Confirmer l'email
        $userWaiting->confirmEmail();

        return view('preregistration.confirmed', compact('userWaiting'));
    }

    /**
     * Page email déjà confirmé
     */
    public function alreadyConfirmed()
    {
        return view('preregistration.already-confirmed');
    }

    /**
     * Statistiques publiques (optionnel)
     */
    public function stats()
    {
        $stats = [
            'total' => UserWaiting::count(),
            'confirmed' => UserWaiting::confirmed()->count(),
            'approved' => UserWaiting::approved()->count(),
            'converted' => UserWaiting::converted()->count(),
        ];

        return view('preregistration.stats', compact('stats'));
    }
}
