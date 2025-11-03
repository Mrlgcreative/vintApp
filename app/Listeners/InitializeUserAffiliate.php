<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\AffiliateService;
use Illuminate\Support\Facades\Log;

class InitializeUserAffiliate
{
    protected AffiliateService $affiliateService;

    /**
     * Create the event listener.
     */
    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        try {
            $result = $this->affiliateService->initializeUserAffiliate(
                $event->user, 
                $event->referralCode
            );
            
            if ($result['success']) {
                Log::info('Système d\'affiliation initialisé pour nouvel utilisateur', [
                    'user_id' => $event->user->id,
                    'referral_code_used' => $event->referralCode,
                    'signup_bonus' => $result['signup_bonus']
                ]);
            } else {
                Log::warning('Échec d\'initialisation du système d\'affiliation', [
                    'user_id' => $event->user->id,
                    'error' => $result['error']
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'initialisation de l\'affiliation', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}