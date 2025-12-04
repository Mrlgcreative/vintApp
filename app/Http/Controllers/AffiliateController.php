<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPoints;
use App\Models\ReferralCode;
use App\Models\Referral;
use App\Models\PointTransaction;
use App\Models\PointRedemption;
use App\Models\PointConversionRate;
use App\Services\AffiliateService;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
{
    use ApiResponses;
    
    protected AffiliateService $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Obtient le dashboard d'affiliation de l'utilisateur (API)
     */
    public function dashboard(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $points = $user->getOrCreatePoints();
            $stats = $user->getAffiliateStats();
            $recentTransactions = $user->pointTransactions()
                                      ->with('user')
                                      ->orderBy('created_at', 'desc')
                                      ->limit(10)
                                      ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'referral_code' => $user->referral_code
                    ],
                    'points' => $points->getStats(),
                    'stats' => $stats,
                    'recent_transactions' => $recentTransactions->map(fn($t) => $t->getDetails()),
                    'conversion_rates' => $this->getAvailableRates()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée un nouveau code de parrainage
     */
    public function createReferralCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'max_uses' => 'nullable|integer|min:1|max:10000',
            'bonus_points' => 'nullable|numeric|min:0|max:1000',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            
            // Limiter le nombre de codes par utilisateur
            $activeCodesCount = $user->referralCodes()->active()->count();
            if ($activeCodesCount >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez avoir que 5 codes actifs maximum'
                ], 400);
            }

            $referralCode = $user->referralCodes()->create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Code de parrainage créé avec succès',
                'data' => [
                    'code' => $referralCode->code,
                    'share_url' => $referralCode->share_url,
                    'stats' => $referralCode->getUsageStats()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste les codes de parrainage de l'utilisateur
     */
    public function getReferralCodes(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $codes = $user->referralCodes()
                         ->orderBy('created_at', 'desc')
                         ->get()
                         ->map(function ($code) {
                             return [
                                 'id' => $code->id,
                                 'code' => $code->code,
                                 'title' => $code->title,
                                 'description' => $code->description,
                                 'is_active' => $code->is_active,
                                 'status' => $code->status,
                                 'share_url' => $code->share_url,
                                 'stats' => $code->getUsageStats(),
                                 'created_at' => $code->created_at->format('d/m/Y H:i')
                             ];
                         });

            return response()->json([
                'success' => true,
                'data' => $codes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des codes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des codes de parrainage
     */
    public function getCodesStats(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            $codes = $user->referralCodes;
            $totalCodes = $codes->count();
            $activeCodes = $codes->where('is_active', true)->count();
            $totalUses = $codes->sum(function($code) {
                return $code->referrals()->count();
            });
            
            // Trouver le code le plus performant
            $bestPerforming = $codes->sortByDesc(function($code) {
                return $code->referrals()->count();
            })->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_codes' => $totalCodes,
                    'active_codes' => $activeCodes,
                    'total_uses' => $totalUses,
                    'best_performing' => $bestPerforming ? $bestPerforming->code : '-'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste les parrainages de l'utilisateur
     */
    public function getReferrals(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $referrals = $user->referrals()
                             ->with(['referred', 'referralCode'])
                             ->orderBy('created_at', 'desc')
                             ->get()
                             ->map(fn($r) => $r->getDetails());

            return response()->json([
                'success' => true,
                'data' => $referrals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des parrainages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Historique des transactions de points
     */
    public function getPointsHistory(Request $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $perPage = $request->get('per_page', 15);
            $type = $request->get('type', 'all');

            $query = $user->pointTransactions()->orderBy('created_at', 'desc');

            if ($type !== 'all') {
                $query->where('type', $type);
            }

            $transactions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => $transactions->items(),
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'last_page' => $transactions->lastPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total()
                    ],
                    'stats' => PointTransaction::getStatsForUser($user->id, [
                        'period' => $request->get('period', 'all')
                    ])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement de l\'historique',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convertit des points en argent
     */
    public function convertPointsToCash(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required|numeric|min:1',
            'currency' => 'required|string|in:USD,CDF'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            $result = $this->affiliateService->convertPointsToCash(
                $user,
                $validator->validated()['points'],
                $validator->validated()['currency']
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande de conversion créée avec succès',
                'data' => [
                    'redemption_id' => $result['redemption']->redemption_id,
                    'points_used' => $result['redemption']->points_used,
                    'cash_amount' => $result['redemption']->cash_amount,
                    'currency' => $result['redemption']->currency,
                    'fees_charged' => $result['redemption']->fees_charged,
                    'status' => $result['redemption']->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la conversion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère un code de réduction
     */
    public function generateDiscountCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required|numeric|min:100|max:5000',
            'expires_days' => 'nullable|integer|min:1|max:365'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            $options = [];

            if ($request->has('expires_days')) {
                $options['expires_at'] = now()->addDays($request->expires_days);
            }

            $result = $this->affiliateService->generateDiscountCode(
                $user,
                $validator->validated()['points'],
                $options
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Code de réduction généré avec succès',
                'data' => [
                    'discount_code' => $result['discount_code'],
                    'discount_percentage' => $result['discount_percentage'],
                    'points_used' => $result['redemption']->points_used,
                    'expires_at' => $result['redemption']->expires_at?->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtient les rachats de l'utilisateur
     */
    public function getRedemptions(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $redemptions = $user->pointRedemptions()
                               ->orderBy('created_at', 'desc')
                               ->get()
                               ->map(fn($r) => $r->getFullDetails());

            return response()->json([
                'success' => true,
                'data' => $redemptions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des rachats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcule la conversion prévisionnelle
     */
    public function calculateConversion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'points' => 'required|numeric|min:1',
            'currency' => 'required|string|in:USD,CDF'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            $points = $validator->validated()['points'];
            $currency = $validator->validated()['currency'];

            $conversionRate = PointConversionRate::getCurrentRate($currency);
            if (!$conversionRate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taux de conversion non disponible'
                ], 400);
            }

            $canConvert = $conversionRate->canConvert($points, $user->id);
            if (!$canConvert['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversion non autorisée',
                    'errors' => $canConvert['errors']
                ], 400);
            }

            $calculation = $conversionRate->calculateCashAmount($points);

            return response()->json([
                'success' => true,
                'data' => $calculation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtient le classement des utilisateurs
     */
    public function getLeaderboard(): JsonResponse
    {
        try {
            $leaderboard = $this->affiliateService->getLeaderboard(50);

            return response()->json([
                'success' => true,
                'data' => $leaderboard
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du classement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtient les taux de conversion disponibles
     */
    private function getAvailableRates(): array
    {
        return PointConversionRate::active()
                                 ->get()
                                 ->groupBy('currency')
                                 ->map(function ($rates, $currency) {
                                     return $rates->map(fn($rate) => $rate->getDetails())->toArray();
                                 })
                                 ->toArray();
    }

    /**
     * Applique un code de parrainage (pour les nouveaux utilisateurs)
     */
    public function applyReferralCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string|exists:referral_codes,code'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Code de parrainage invalide',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user->canBeReferred()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà été parrainé ou avez un parrain'
                ], 400);
            }

            $referral = $user->applyReferralCode($validator->validated()['referral_code']);
            
            if (!$referral) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'appliquer ce code de parrainage'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Code de parrainage appliqué avec succès',
                'data' => [
                    'referrer_name' => $referral->referrer->name,
                    'bonus_points' => $referral->bonus_points,
                    'status' => $referral->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'application du code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche la page du dashboard d'affiliation
     */
    public function showDashboard()
    {
        return view('affiliate.dashboard');
    }

    /**
     * Génère un lien de parrainage personnalisé
     */
    public function generateReferralLink(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Récupérer le code de parrainage principal ou en créer un
        $referralCode = $user->referralCodes()->active()->first();
        
        if (!$referralCode) {
            $referralCode = $user->createReferralCode();
        }
        
        $link = route('referral.link', ['code' => $referralCode->code]);
        
        return response()->json([
            'success' => true,
            'link' => $link,
            'code' => $referralCode->code
        ]);
    }

    /**
     * Valide un code de parrainage
     */
    public function validateReferralCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => 'Code requis'
            ], 422);
        }

        try {
            $code = $request->referral_code;
            $referralCode = ReferralCode::where('code', $code)
                                      ->active()
                                      ->with('user')
                                      ->first();

            if ($referralCode) {
                return response()->json([
                    'valid' => true,
                    'referrer_name' => $referralCode->user->name,
                    'message' => 'Code valide'
                ]);
            }

            return response()->json([
                'valid' => false,
                'message' => 'Code invalide ou expiré'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Erreur de validation'
            ], 500);
        }
    }
}