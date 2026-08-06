<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\ReferralCode;
use App\Models\Referral;
use App\Models\AffiliateReward;
use App\Models\PointTransaction;
use Carbon\Carbon;
use App\Traits\ApiResponses;

class AffiliateController extends Controller
{
    use ApiResponses;
    /**
     * Afficher la page principale de gestion des affiliations
     */
    public function index()
    {
        return view('admin.affiliate.index');
    }

    /**
     * API: Statistiques générales du dashboard
     */
    public function getDashboardStats()
    {
        try {
            // Statistiques en temps réel pour voir les changements immédiatement
            $stats = [
                'total_referrers' => User::whereExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('referrals')
                          ->whereRaw('referrals.referrer_id = users.id');
                })->count(),
                
                'total_points_earned' => PointTransaction::whereIn('type', ['earn_referral', 'earn_bonus'])->sum('amount'),
                'total_rewards_distributed' => AffiliateReward::sum('value'),
                'active_campaigns' => 1, // Placeholder
                'active_referrals' => Referral::where('status', 'completed')->count(),
                'total_points' => PointTransaction::whereIn('type', ['earn_referral', 'earn_bonus'])->sum('amount') ?: 0,
                'total_rewards' => AffiliateReward::count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur stats dashboard affiliation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques'
            ], 500);
        }
    }

    /**
     * API: Top 10 des meilleurs parrains
     */
    public function getTopPerformers()
    {
        try {
            $topPerformers = User::select([
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
                DB::raw('COALESCE(user_points.level, 1) as level'),
                DB::raw('COUNT(referrals.id) as referrals_count'),
                DB::raw('COALESCE(SUM(point_transactions.amount), 0) as total_points'),
                DB::raw('GREATEST(
                    COALESCE(COUNT(referrals.id) * 10, 0) + 
                    COALESCE(SUM(CASE WHEN referrals.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 20 ELSE 0 END), 0), 
                    0
                ) as performance_score')
            ])
            ->leftJoin('user_points', 'users.id', '=', 'user_points.user_id')
            ->leftJoin('referrals', 'users.id', '=', 'referrals.referrer_id')
            ->leftJoin('point_transactions', function($join) {
                $join->on('users.id', '=', 'point_transactions.user_id')
                     ->whereIn('point_transactions.type', ['earn_referral', 'earn_bonus']);
            })
            ->whereHas('referrals')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.avatar', 'user_points.level')
            ->orderByDesc('referrals_count')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get()
            ->map(function ($user, $index) {
                $user->performance_score = min(100, $user->performance_score);
                return $user;
            });

            return response()->json([
                'success' => true,
                'data' => $topPerformers
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur top performers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des top performers'
            ], 500);
        }
    }

    /**
     * API: Liste simple des parrains pour les sélecteurs
     */
    public function getReferrersList()
    {
        try {
            $referrers = User::select([
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(referrals.id) as referrals_count')
            ])
            ->leftJoin('referrals', 'users.id', '=', 'referrals.referrer_id')
            ->whereHas('referrals')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('users.name')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $referrers
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur liste parrains: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement de la liste des parrains'
            ], 500);
        }
    }

    /**
     * API: Liste paginée de tous les parrains
     */
    public function getReferrers(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search', '');
            $minLevel = $request->get('level', '');
            $minReferrals = $request->get('min_referrals', '');
            $timePeriod = $request->get('period', 'all');
            $status = $request->get('status', 'all');

            $query = User::select([
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
                'users.created_at',
                DB::raw('COALESCE(user_points.level, 1) as level'),
                DB::raw('COALESCE(user_points.available_points, 0) as available_points'),
                DB::raw('user_points.last_activity_at as last_activity_at'),
                DB::raw('COUNT(referrals.id) as referrals_count'),
                DB::raw('COUNT(CASE WHEN referrals.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as referrals_this_month'),
                DB::raw('COALESCE(SUM(point_transactions.amount), 0) as total_points'),
                DB::raw('COUNT(affiliate_rewards.id) as rewards_count'),
                DB::raw('MAX(affiliate_rewards.created_at) as last_reward'),
                DB::raw('CASE 
                    WHEN COUNT(referrals.id) >= 50 THEN "top_performer"
                    WHEN COUNT(referrals.id) >= 10 THEN "active"
                    WHEN COUNT(referrals.id) > 0 THEN "active"
                    ELSE "inactive"
                END as status')
            ])
            ->leftJoin('user_points', 'users.id', '=', 'user_points.user_id')
            ->leftJoin('referrals', 'users.id', '=', 'referrals.referrer_id')
            ->leftJoin('point_transactions', function($join) {
                $join->on('users.id', '=', 'point_transactions.user_id')
                     ->whereIn('point_transactions.type', ['earn_referral', 'earn_bonus']);
            })
            ->leftJoin('affiliate_rewards', 'users.id', '=', 'affiliate_rewards.user_id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.avatar', 'users.created_at', 'user_points.level', 'user_points.available_points', 'user_points.last_activity_at');

            // Filtres
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                      ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($minLevel)) {
                $query->where('user_points.level', '>=', $minLevel);
            }

            if (!empty($minReferrals)) {
                $query->havingRaw('referrals_count >= ?', [$minReferrals]);
            }

            if ($timePeriod !== 'all') {
                $dateFilter = match($timePeriod) {
                    'this_month' => Carbon::now()->startOfMonth(),
                    'last_month' => Carbon::now()->subMonth()->startOfMonth(),
                    'this_year' => Carbon::now()->startOfYear(),
                    default => null
                };

                if ($dateFilter) {
                    $query->whereHas('referrals', function($q) use ($dateFilter) {
                        $q->where('created_at', '>=', $dateFilter);
                    });
                }
            }

            if ($status !== 'all') {
                switch ($status) {
                    case 'top_performers':
                        $query->havingRaw('referrals_count >= 50');
                        break;
                    case 'active':
                        $query->havingRaw('referrals_count > 0');
                        break;
                    case 'eligible_rewards':
                        $query->havingRaw('referrals_count >= 10');
                        break;
                }
            }

            $query->orderByDesc('referrals_count')->orderByDesc('total_points');

            $result = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur liste parrains: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des parrains'
            ], 500);
        }
    }

    /**
     * API: Activités récentes
     */
    public function getRecentActivity()
    {
        try {
            $activities = collect();

            // Nouveaux parrainages
            $recentReferrals = Referral::with(['referrer:id,name', 'referred:id,name'])
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($referral) {
                    return [
                        'type' => 'success',
                        'title' => 'Nouveau parrainage',
                        'description' => "{$referral->referrer->name} a parrainé {$referral->referred->name}",
                        'user_name' => $referral->referrer->name,
                        'created_at' => $referral->created_at,
                        'amount' => '+' . config('affiliate.referral_points', 100) . ' pts'
                    ];
                });

            // Récompenses récentes
            $recentRewards = AffiliateReward::with('user:id,name')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($reward) {
                    return [
                        'type' => 'warning',
                        'title' => 'Récompense attribuée',
                        'description' => "Récompense {$reward->type} attribuée",
                        'user_name' => $reward->user->name,
                        'created_at' => $reward->created_at,
                        'amount' => $reward->value ?? ''
                    ];
                });

            // Nouveaux niveaux atteints
            $levelUps = User::whereHas('pointsTransactions', function($query) {
                $query->where('type', 'level_up')
                      ->where('created_at', '>=', Carbon::now()->subDays(7));
            })
            ->with(['pointsTransactions' => function($query) {
                $query->where('type', 'level_up')
                      ->where('created_at', '>=', Carbon::now()->subDays(7))
                      ->latest()
                      ->limit(1);
            }])
            ->limit(3)
            ->get()
            ->map(function($user) {
                $transaction = $user->pointsTransactions->first();
                return [
                    'type' => 'info',
                    'title' => 'Nouveau niveau atteint',
                    'description' => "Niveau {$user->level} atteint !",
                    'user_name' => $user->name,
                    'created_at' => $transaction->created_at ?? $user->updated_at,
                    'amount' => "Niveau {$user->level}"
                ];
            });

            $activities = $activities->merge($recentReferrals)
                                   ->merge($recentRewards)
                                   ->merge($levelUps)
                                   ->sortByDesc('created_at')
                                   ->take(10)
                                   ->values();

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur activités récentes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des activités'
            ], 500);
        }
    }

    /**
     * API: Créer une récompense
     */
    public function createReward(Request $request)
    {
        $request->validate([
            'referrer_id' => 'required|exists:users,id',
            'type' => 'required|in:points,cash,badge,level_boost,custom',
            'reason' => 'nullable|string|max:300',
            'send_notification' => 'boolean',
            'make_public' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->referrer_id);
            
            // Créer la récompense
            $reward = AffiliateReward::create([
                'user_id' => $user->id,
                'admin_id' => auth()->check() ? auth()->id() : 1,
                'type' => $request->type,
                'value' => $this->calculateRewardValue($request),
                'description' => $this->generateRewardDescription($request),
                'reason' => $request->reason,
                'is_public' => $request->boolean('make_public', false),
                'metadata' => $this->collectRewardMetadata($request),
                'status' => 'active'
            ]);

            // Appliquer la récompense selon le type
            $this->applyReward($user, $request, $reward);

            // Envoyer notification si demandé
            if ($request->boolean('send_notification', true)) {
                $this->sendRewardNotification($user, $reward);
            }

            // Log de l'action
            $adminName = auth()->check() ? auth()->user()->name : 'Admin';
            Log::info("Récompense {$request->type} attribuée à l'utilisateur {$user->name} par l'admin {$adminName}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Récompense attribuée avec succès',
                'data' => $reward
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création récompense: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la récompense',
                'error' => $e->getMessage() // Temporaire pour le debug
            ], 500);
        }
    }

    /**
     * Calculer la valeur de la récompense
     */
    private function calculateRewardValue($request)
    {
        $value = $request->input('value', []);
        
        return match($request->type) {
            'points' => ($value['amount'] ?? 0) * ($value['multiplier'] ?? 1),
            'cash' => $value['amount'] ?? 0,
            'level_boost' => $value['levels'] ?? 1,
            default => 1
        };
    }

    /**
     * Générer la description de la récompense
     */
    private function generateRewardDescription($request)
    {
        $value = $request->input('value', []);
        
        return match($request->type) {
            'points' => "Points bonus: " . (($value['amount'] ?? 0) * ($value['multiplier'] ?? 1)),
            'cash' => "Récompense en argent: {$value['amount']} {$value['currency']}",
            'badge' => "Badge attribué: {$value['name']}",
            'level_boost' => "Boost de niveau: +{$value['levels']} niveau(x)",
            'custom' => $request->reason ?? 'Récompense personnalisée',
            default => 'Récompense attribuée'
        };
    }

    /**
     * Collecter les métadonnées de la récompense
     */
    private function collectRewardMetadata($request)
    {
        $metadata = [
            'value_data' => $request->input('value', [])
        ];

        switch($request->type) {
            case 'points':
                $metadata = [
                    'base_points' => $request->points,
                    'multiplier' => $request->multiplier ?? 1,
                    'final_points' => $request->points * ($request->multiplier ?? 1)
                ];
                break;
            case 'cash':
                $metadata = [
                    'amount' => $request->amount,
                    'currency' => $request->currency
                ];
                break;
            case 'badge':
                $metadata = [
                    'badge_name' => $request->badge_name,
                    'duration' => $request->badge_duration,
                    'expires_at' => $request->badge_duration !== 'permanent' ? 
                        Carbon::now()->addDays($request->badge_duration) : null
                ];
                break;
            case 'level_boost':
                $metadata = [
                    'boost_amount' => $request->level_boost,
                    'boost_type' => $request->boost_type,
                    'expires_at' => $request->boost_type === 'temporary' ? 
                        Carbon::now()->addDays(30) : null
                ];
                break;
        }

        return $metadata;
    }

    /**
     * Appliquer la récompense à l'utilisateur
     */
    private function applyReward($user, $request, $reward)
    {
        $value = $request->input('value', []);
        
        switch($request->type) {
            case 'points':
                $points = ($value['amount'] ?? 0) * ($value['multiplier'] ?? 1);
                
                // Créer ou mettre à jour les points de l'utilisateur
                $userPoints = $user->points()->firstOrCreate(['user_id' => $user->id], [
                    'total_points' => 0,
                    'available_points' => 0,
                    'level' => 1
                ]);
                
                $userPoints->increment('available_points', $points);
                $userPoints->increment('total_points', $points);
                
                // Créer la transaction de points
                PointTransaction::create([
                    'user_id' => $user->id,
                    'transaction_id' => PointTransaction::generateTransactionId(),
                    'type' => 'earn_bonus',
                    'amount' => $points,
                    'balance_before' => $userPoints->available_points - $points,
                    'balance_after' => $userPoints->available_points,
                    'description' => "Récompense admin: " . ($reward->reason ?: 'Bonus points'),
                    'status' => 'completed'
                ]);
                break;

            case 'level_boost':
                $boost = $value['levels'] ?? 1;
                $userPoints = $user->points()->firstOrCreate(['user_id' => $user->id], [
                    'total_points' => 0,
                    'available_points' => 0,
                    'level' => 1
                ]);
                $userPoints->increment('level', $boost);
                break;

            case 'cash':
                // TODO: Intégrer avec le système de wallet/paiements
                break;

            case 'badge':
                // TODO: Intégrer avec le système de badges
                break;
        }
    }

    /**
     * Envoyer une notification de récompense
     */
    private function sendRewardNotification($user, $reward)
    {
        // TODO: Intégrer avec le système de notifications
        // Notification::send($user, new RewardReceived($reward));
    }

    /**
     * API: Statistiques des codes par niveau (pour le graphique)
     */
    public function getLevelStats()
    {
        try {
            $levelStats = User::select('level', DB::raw('count(*) as count'))
                ->whereHas('referrals')
                ->groupBy('level')
                ->orderBy('level')
                ->get()
                ->pluck('count', 'level')
                ->toArray();

            // Compléter les niveaux manquants
            $completeStats = [];
            for ($i = 1; $i <= 5; $i++) {
                $completeStats[$i] = $levelStats[$i] ?? 0;
            }

            return response()->json([
                'success' => true,
                'data' => $completeStats
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur stats niveaux: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques par niveau'
            ], 500);
        }
    }
}