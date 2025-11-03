<?php

namespace App\Listeners;

use App\Events\UserRegisteredWithReferral;
use App\Mail\ReferralSuccessNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HandleReferralBonus implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegisteredWithReferral $event): void
    {
        $user = $event->user;
        $referral = $event->referral;

        if ($referral) {
            try {
                // Donner des points bonus au nouveau utilisateur (points d'inscription)
                $user->giveRegistrationPoints();
                
                // Donner des points au parrain
                $referrer = $referral->referrer;
                if ($referrer) {
                    $pointsEarned = $referrer->giveReferralPoints($user);
                    
                    // Envoyer une notification email au parrain
                    $stats = $referrer->getAffiliateStats();
                    Mail::to($referrer->email)->send(new ReferralSuccessNotification(
                        $referrer,
                        $user,
                        $referral->referralCode,
                        $pointsEarned,
                        $stats
                    ));
                }

                Log::info('Bonus de parrainage appliqué', [
                    'new_user_id' => $user->id,
                    'referrer_id' => $referrer?->id,
                    'referral_id' => $referral->id,
                    'points_earned' => $pointsEarned ?? 0
                ]);

            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'application du bonus de parrainage', [
                    'user_id' => $user->id,
                    'referral_id' => $referral->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}