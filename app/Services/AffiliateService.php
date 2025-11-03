<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralCode;
use App\Models\Referral;
use App\Models\UserPoints;
use App\Models\PointTransaction;
use App\Models\PointRedemption;
use App\Models\PointConversionRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliateService
{
    /**
     * Initialise le système d'affiliation pour un nouvel utilisateur
     */
    public function initializeUserAffiliate(User $user, string $referralCode = null): array
    {
        try {
            DB::beginTransaction();

            // 1. Créer le système de points
            $points = UserPoints::createForUser($user->id);

            // 2. Générer le code de parrainage principal
            $userReferralCode = $user->generateReferralCode('Mon code principal');

            // 3. Appliquer le code de parrainage s'il y en a un
            $referral = null;
            if ($referralCode && $user->canBeReferred()) {
                $referral = $user->applyReferralCode($referralCode);
            }

            // 4. Ajouter le bonus d'inscription
            $user->addSignupBonus(100);

            // 5. Mettre à jour le code personnel dans la table users
            $user->update(['referral_code' => $userReferralCode->code]);

            DB::commit();

            return [
                'success' => true,
                'points' => $points,
                'referral_code' => $userReferralCode,
                'applied_referral' => $referral,
                'signup_bonus' => 100
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'initialisation de l\'affiliation', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Active un parrainage quand l'utilisateur valide son email
     */
    public function activateUserReferral(User $user): bool
    {
        try {
            if (!$user->email_verified_at) {
                return false;
            }

            $user->activateReferral();

            // Marquer comme activé
            $user->update(['referral_activated_at' => now()]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'activation du parrainage', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Vérifie et complète les parrainages éligibles
     */
    public function checkReferralCompletion(User $user): array
    {
        $completed = [];

        try {
            // Vérifier les parrainages actifs de cet utilisateur (en tant que filleul)
            $referral = $user->referredBy;
            if ($referral && $referral->status === 'active') {
                if ($referral->checkCompletionConditions()) {
                    $referral->complete();
                    $completed[] = $referral;
                }
            }

            // Vérifier les parrainages où cet utilisateur est le parrain
            $activeReferrals = $user->referrals()->active()->get();
            foreach ($activeReferrals as $referral) {
                if ($referral->checkCompletionConditions()) {
                    $referral->complete();
                    $completed[] = $referral;
                }
            }

            return [
                'success' => true,
                'completed_referrals' => $completed
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification des parrainages', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Ajoute des points pour différentes actions utilisateur
     */
    public function awardPoints(User $user, string $action, array $params = []): ?PointTransaction
    {
        $points = $user->getOrCreatePoints();

        return match($action) {
            'daily_login' => $this->awardDailyLoginPoints($points),
            'profile_complete' => $this->awardProfileCompletePoints($points),
            'first_purchase' => $this->awardFirstPurchasePoints($points, $params),
            'purchase' => $this->awardPurchasePoints($points, $params),
            'sale' => $this->awardSalePoints($points, $params),
            'review_given' => $this->awardReviewPoints($points),
            'social_share' => $this->awardSocialSharePoints($points, $params),
            default => null
        };
    }

    /**
     * Points de connexion quotidienne
     */
    private function awardDailyLoginPoints(UserPoints $points): ?PointTransaction
    {
        // Vérifier si déjà reçu aujourd'hui
        $todayLogin = $points->transactions()
                            ->where('type', 'earn_daily_login')
                            ->whereDate('created_at', today())
                            ->exists();

        if ($todayLogin) {
            return null;
        }

        $dailyPoints = 10 * $points->level_multiplier;
        return $points->credit($dailyPoints, 'earn_daily_login', 'Connexion quotidienne');
    }

    /**
     * Points pour profil complété
     */
    private function awardProfileCompletePoints(UserPoints $points): ?PointTransaction
    {
        // Vérifier si déjà reçu
        $alreadyAwarded = $points->transactions()
                                ->where('type', 'earn_profile_complete')
                                ->exists();

        if ($alreadyAwarded) {
            return null;
        }

        return $points->credit(50, 'earn_profile_complete', 'Profil complété');
    }

    /**
     * Points pour premier achat
     */
    private function awardFirstPurchasePoints(UserPoints $points, array $params): ?PointTransaction
    {
        $user = $points->user;
        $isFirstPurchase = $user->ordersAsBuyer()->where('status', 'completed')->count() === 1;

        if (!$isFirstPurchase) {
            return null;
        }

        $bonusPoints = 200 * $points->level_multiplier;
        return $points->credit($bonusPoints, 'earn_purchase', 'Bonus premier achat');
    }

    /**
     * Points pour achats
     */
    private function awardPurchasePoints(UserPoints $points, array $params): ?PointTransaction
    {
        $amount = $params['amount'] ?? 0;
        $percentage = $params['percentage'] ?? 2.0;

        $earnedPoints = ($amount * $percentage / 100) * $points->level_multiplier;
        return $points->credit($earnedPoints, 'earn_purchase', "Points d'achat pour {$amount} USD");
    }

    /**
     * Points pour ventes
     */
    private function awardSalePoints(UserPoints $points, array $params): ?PointTransaction
    {
        $amount = $params['amount'] ?? 0;
        $percentage = $params['percentage'] ?? 1.0;

        $earnedPoints = ($amount * $percentage / 100) * $points->level_multiplier;
        return $points->credit($earnedPoints, 'earn_sale', "Points de vente pour {$amount} USD");
    }

    /**
     * Points pour avis donné
     */
    private function awardReviewPoints(UserPoints $points): ?PointTransaction
    {
        $reviewPoints = 25 * $points->level_multiplier;
        return $points->credit($reviewPoints, 'earn_review', 'Avis donné');
    }

    /**
     * Points pour partage social
     */
    private function awardSocialSharePoints(UserPoints $points, array $params): ?PointTransaction
    {
        $platform = $params['platform'] ?? 'unknown';
        
        // Limite par jour par plateforme
        $todayShares = $points->transactions()
                             ->where('type', 'earn_social_share')
                             ->whereDate('created_at', today())
                             ->whereJsonContains('metadata->platform', $platform)
                             ->count();

        if ($todayShares >= 3) { // Max 3 partages par plateforme par jour
            return null;
        }

        $sharePoints = 15 * $points->level_multiplier;
        return $points->credit(
            $sharePoints, 
            'earn_social_share', 
            "Partage sur {$platform}",
            ['platform' => $platform]
        );
    }

    /**
     * Convertit des points en argent
     */
    public function convertPointsToCash(User $user, float $points, string $currency): array
    {
        try {
            DB::beginTransaction();

            $userPoints = $user->getOrCreatePoints();

            // Vérifier si l'utilisateur a assez de points
            if ($userPoints->available_points < $points) {
                return [
                    'success' => false,
                    'error' => 'Points insuffisants'
                ];
            }

            // Obtenir le taux de conversion
            $conversionRate = PointConversionRate::getCurrentRate($currency);
            if (!$conversionRate) {
                return [
                    'success' => false,
                    'error' => 'Taux de conversion non disponible'
                ];
            }

            // Vérifier les conditions
            $canConvert = $conversionRate->canConvert($points, $user->id);
            if (!$canConvert['valid']) {
                return [
                    'success' => false,
                    'error' => implode(', ', $canConvert['errors'])
                ];
            }

            // Débiter les points
            $transaction = $userPoints->debit($points, 'redeem_cash', "Conversion en {$currency}");
            if (!$transaction) {
                return [
                    'success' => false,
                    'error' => 'Impossible de débiter les points'
                ];
            }

            // Créer la demande de rachat
            $redemption = PointRedemption::createCashRedemption($user->id, $points, $currency);

            DB::commit();

            return [
                'success' => true,
                'redemption' => $redemption,
                'transaction' => $transaction
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la conversion de points', [
                'user_id' => $user->id,
                'points' => $points,
                'currency' => $currency,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la conversion'
            ];
        }
    }

    /**
     * Génère un code de réduction à partir de points
     */
    public function generateDiscountCode(User $user, float $points, array $options = []): array
    {
        try {
            DB::beginTransaction();

            $userPoints = $user->getOrCreatePoints();

            // Vérifier les points disponibles
            if ($userPoints->available_points < $points) {
                return [
                    'success' => false,
                    'error' => 'Points insuffisants'
                ];
            }

            // Calculer la valeur de réduction (100 points = 1% de réduction)
            $discountPercentage = $points / 100;
            $maxDiscount = min($discountPercentage, 50); // Maximum 50%

            // Débiter les points
            $transaction = $userPoints->debit($points, 'redeem_discount', 'Code de réduction généré');
            if (!$transaction) {
                return [
                    'success' => false,
                    'error' => 'Impossible de débiter les points'
                ];
            }

            // Créer le code de réduction
            $redemption = PointRedemption::createDiscountCode($user->id, $points, array_merge($options, [
                'discount_value' => $maxDiscount
            ]));

            DB::commit();

            return [
                'success' => true,
                'redemption' => $redemption,
                'discount_code' => $redemption->redemption_code,
                'discount_percentage' => $maxDiscount,
                'transaction' => $transaction
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la génération du code de réduction', [
                'user_id' => $user->id,
                'points' => $points,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la génération du code'
            ];
        }
    }

    /**
     * Obtient le classement des utilisateurs par points
     */
    public function getLeaderboard(int $limit = 50): array
    {
        return UserPoints::with('user')
                        ->orderBy('total_points', 'desc')
                        ->orderBy('level', 'desc')
                        ->limit($limit)
                        ->get()
                        ->map(function ($points, $index) {
                            return [
                                'rank' => $index + 1,
                                'user_id' => $points->user_id,
                                'user_name' => $points->user->name,
                                'avatar' => $points->user->avatar_url,
                                'total_points' => $points->total_points,
                                'level' => $points->level,
                                'level_name' => $points->getLevelName(),
                                'referrals_count' => $points->user->referrals()->completed()->count()
                            ];
                        })
                        ->toArray();
    }

    /**
     * Obtient les statistiques globales du système d'affiliation
     */
    public function getSystemStats(): array
    {
        return [
            'total_users_with_points' => UserPoints::count(),
            'total_points_distributed' => UserPoints::sum('total_points'),
            'total_points_redeemed' => UserPoints::sum('redeemed_points'),
            'active_referrals' => Referral::active()->count(),
            'completed_referrals' => Referral::completed()->count(),
            'total_conversions' => PointRedemption::completed()->count(),
            'total_conversion_value' => PointRedemption::completed()->sum('cash_amount'),
            'average_user_level' => UserPoints::avg('level'),
            'top_referrer' => $this->getTopReferrer()
        ];
    }

    /**
     * Obtient le meilleur parrain
     */
    private function getTopReferrer(): ?array
    {
        $topReferrer = User::withCount(['referrals as completed_referrals_count' => function ($query) {
                                $query->where('status', 'completed');
                            }])
                           ->orderBy('completed_referrals_count', 'desc')
                           ->first();

        if (!$topReferrer) {
            return null;
        }

        return [
            'user_id' => $topReferrer->id,
            'name' => $topReferrer->name,
            'referrals_count' => $topReferrer->completed_referrals_count,
            'total_points_earned' => $topReferrer->referrals()->sum('points_earned')
        ];
    }
}