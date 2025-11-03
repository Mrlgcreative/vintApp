<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AffiliateService;
use Illuminate\Support\Facades\Log;

class InitializeAffiliateSystem
{
    protected AffiliateService $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $next($request);
        }

        // Vérifier si le système d'affiliation est déjà initialisé
        if ($user->points) {
            // Si l'email vient d'être vérifié, activer les parrainages
            if ($user->email_verified_at && !$user->referral_activated_at) {
                $this->affiliateService->activateUserReferral($user);
                $this->affiliateService->checkReferralCompletion($user);
            }

            return $next($request);
        }

        // Initialiser le système d'affiliation pour le nouvel utilisateur
        try {
            $referralCode = session('referral_code') ?? $request->get('ref');
            $result = $this->affiliateService->initializeUserAffiliate($user, $referralCode);
            
            if ($result['success']) {
                Log::info('Système d\'affiliation initialisé', [
                    'user_id' => $user->id,
                    'referral_code_used' => $referralCode,
                    'signup_bonus' => $result['signup_bonus']
                ]);
                
                // Nettoyer la session
                session()->forget('referral_code');
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur initialisation système d\'affiliation', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return $next($request);
    }
}