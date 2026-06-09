<?php

namespace App\Http\Controllers;

use App\Models\BoostType;
use App\Models\ProductBoost;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BoostController extends Controller
{
    /**
     * Afficher la page principale avec tous les types de boost disponibles
     */
    public function index()
    {
        $boostTypes = BoostType::where('is_active', true)
                              ->orderBy('base_price', 'asc')
                              ->get();
        
        return view('boost.index', compact('boostTypes'));
    }

    /**
     * Afficher les détails d'un type de boost spécifique
     */
    public function show(BoostType $boostType)
    {
        // Statistiques pour ce type de boost
        $stats = [
            'total_users' => ProductBoost::where('boost_type_id', $boostType->id)
                                        ->distinct('user_id')
                                        ->count(),
            'total_purchases' => ProductBoost::where('boost_type_id', $boostType->id)->count(),
            'avg_duration' => round(ProductBoost::where('boost_type_id', $boostType->id)
                                         ->avg('duration') ?? 0),
            'satisfaction' => 95, // Pourcentage fixe pour l'instant
        ];

        return view('boost.show', compact('boostType', 'stats'));
    }

    /**
     * Calculer le prix d'un boost en fonction de la durée
     */
    public function calculatePrice(Request $request)
    {
        try {
            $request->validate([
                'boost_type_id' => 'required|exists:boost_types,id',
                'item_id' => 'required|exists:items,id',
                'duration' => 'required|integer|min:1'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides: ' . implode(', ', $e->validator->errors()->all())
            ], 400);
        }

        try {
            $boostType = BoostType::findOrFail($request->boost_type_id);
            $user = Auth::user();
            $userCurrency = $user->preferred_currency ?? 'CDF';
            $durationInDays = (int) $request->duration;
            
            // Vérifier que la durée respecte les limites min/max
            // min_duration et max_duration sont stockés en jours dans la DB
            if ($durationInDays < $boostType->min_duration || $durationInDays > $boostType->max_duration) {
                return response()->json([
                    'success' => false,
                    'message' => "La durée doit être entre {$boostType->min_duration} et {$boostType->max_duration} jours"
                ], 400);
            }

        // Prix de base selon la devise
        $basePrice = $userCurrency === 'USD' ? $boostType->price_usd : $boostType->price_cdf;
        
        // Tarification dégressive fixe basée sur 17 000 CDF pour 30 jours (base 10 000)
        // Progression cohérente pour toutes les durées
        $duration = (int) $request->duration;
        
        switch ($duration) {
            case 1:
                $totalPrice = $basePrice; // 100% - Prix plein
                break;
            case 3:
                $totalPrice = $basePrice * 2.4; // 8 000 par jour → 24 000 pour 3 jours (base 10k)
                break;
            case 7:
                $totalPrice = $basePrice * 4.9; // 7 000 par jour → 49 000 pour 7 jours (base 10k)
                break;
            case 14:
                $totalPrice = $basePrice * 8.4; // 6 000 par jour → 84 000 pour 14 jours (base 10k)
                break;
            case 21:
                $totalPrice = $basePrice * 10.5; // 5 000 par jour → 105 000 pour 21 jours (base 10k)
                break;
            case 30:
                $totalPrice = $basePrice * 1.7; // 567 par jour → 17 000 pour 30 jours (base 10k)
                break;
            default:
                // Pour les durées non standard, interpolation proportionnelle
                if ($duration <= 3) {
                    $totalPrice = $basePrice * $duration * 0.80;
                } elseif ($duration <= 7) {
                    $totalPrice = $basePrice * $duration * 0.70;
                } elseif ($duration <= 14) {
                    $totalPrice = $basePrice * $duration * 0.60;
                } elseif ($duration <= 21) {
                    $totalPrice = $basePrice * $duration * 0.50;
                } else {
                    $totalPrice = $basePrice * $duration * 0.057;
                }
                break;
        }
        $currencySymbol = $userCurrency === 'USD' ? '$' : 'CDF';

            return response()->json([
                'success' => true,
                'price' => $totalPrice,
                'currency' => $userCurrency,
                'formatted_price' => $userCurrency === 'USD' 
                    ? '$' . number_format($totalPrice, 2) 
                    : number_format($totalPrice, 0, ',', ' ') . ' CDF'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du calcul du prix de boost: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du calcul du prix'
            ], 500);
        }
    }

    /**
     * Acheter un boost pour un produit
     */
    public function purchase(Request $request)
    {
        try {
            $request->validate([
                'boost_type_id' => 'required|exists:boost_types,id',
                'item_id' => 'required|exists:items,id',
                'duration' => 'required|integer|min:1|max:365'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation boost purchase failed', [
                'errors' => $e->validator->errors()->all(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Données invalides: ' . implode(', ', $e->validator->errors()->all())
            ], 400);
        }

        try {
            $item = Item::where('id', $request->item_id)
                       ->where('user_id', Auth::id())
                       ->first();

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé ou vous n\'en êtes pas le propriétaire'
                ], 404);
            }

            $boostType = BoostType::findOrFail($request->boost_type_id);
            $durationInDays = (int) $request->duration;

            // Vérifier qu'il n'y a pas déjà un boost actif pour ce produit
            $existingBoost = ProductBoost::where('item_id', $item->id)
                                        ->where('status', 'active')
                                        ->where('expires_at', '>', now())
                                        ->first();

            if ($existingBoost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit a déjà un boost actif. Annulez-le d\'abord ou attendez qu\'il expire.'
                ], 400);
            }

            // Vérifier la durée (min_duration et max_duration sont en jours)
            if ($durationInDays < $boostType->min_duration || $durationInDays > $boostType->max_duration) {
                Log::warning('Duration out of bounds', [
                    'requested' => $durationInDays,
                    'min' => $boostType->min_duration,
                    'max' => $boostType->max_duration
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => "La durée doit être entre {$boostType->min_duration} et {$boostType->max_duration} jours"
                ], 400);
            }

            // Calculer le prix total (même logique que calculatePrice)
            $user = Auth::user();
            $userCurrency = $user->preferred_currency ?? 'CDF';
            $basePrice = $userCurrency === 'USD' ? $boostType->price_usd : $boostType->price_cdf;
            
            // Tarification dégressive fixe (identique à calculatePrice)
            $duration = (int) $request->duration;
            
            switch ($duration) {
                case 1:
                    $totalPrice = $basePrice;
                    break;
                case 3:
                    $totalPrice = $basePrice * 2.4;
                    break;
                case 7:
                    $totalPrice = $basePrice * 4.9;
                    break;
                case 14:
                    $totalPrice = $basePrice * 8.4;
                    break;
                case 21:
                    $totalPrice = $basePrice * 10.5;
                    break;
                case 30:
                    $totalPrice = $basePrice * 1.7;
                    break;
                default:
                    if ($duration <= 3) {
                        $totalPrice = $basePrice * $duration * 0.80;
                    } elseif ($duration <= 7) {
                        $totalPrice = $basePrice * $duration * 0.70;
                    } elseif ($duration <= 14) {
                        $totalPrice = $basePrice * $duration * 0.60;
                    } elseif ($duration <= 21) {
                        $totalPrice = $basePrice * $duration * 0.50;
                    } else {
                        $totalPrice = $basePrice * $duration * 0.057;
                    }
                    break;
            }

            // Vérifier le solde du wallet de l'utilisateur
            $cdfWallet = $user->cdfWallet();
            $cdfBalance = $cdfWallet ? $cdfWallet->balance : 0;
            if ($cdfBalance < $totalPrice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant. Votre solde: ' . number_format($cdfBalance, 0, ',', ' ') . ' CDF, Requis: ' . number_format($totalPrice, 0, ',', ' ') . ' CDF'
                ], 400);
            }

            DB::beginTransaction();

            // Déduire le montant du wallet
            $cdfWallet->decrement('balance', $totalPrice);

            // Créer l'enregistrement de boost
            $boost = ProductBoost::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'boost_type_id' => $boostType->id,
                'duration' => (int) $request->duration,
                'total_price' => $totalPrice,
                'status' => 'active',
                'activated_at' => now(),
                'expires_at' => now()->addDays((int) $request->duration)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Boost acheté avec succès !',
                'boost' => $boost->load(['boostType', 'item']),
                'redirect' => route('boost.dashboard')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Boost purchase failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'achat du boost: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annuler un boost actif avec remboursement partiel possible
     */
    public function cancel(ProductBoost $productBoost)
    {
        // Vérifier que l'utilisateur peut annuler ce boost
        if ($productBoost->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à annuler ce boost'
            ], 403);
        }

        if ($productBoost->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Ce boost n\'est pas actif'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Calculer le remboursement selon la politique
            $refundAmount = $this->calculateRefundAmount($productBoost);

            // Récupérer le wallet CDF pour le remboursement
            $cdfWallet = Auth::user()->cdfWallet();

            // Mettre à jour le boost
            $productBoost->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'refund_amount' => $refundAmount
            ]);

            // Effectuer le remboursement si applicable
            if ($refundAmount > 0) {
                $cdfWallet->increment('balance', $refundAmount);
            }

            DB::commit();

            $message = $refundAmount > 0 
                ? "Boost annulé avec succès. Remboursement de " . number_format($refundAmount, 0, ',', ' ') . " CDF effectué."
                : "Boost annulé avec succès.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'refund_amount' => $refundAmount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'annulation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard utilisateur pour gérer ses boosts
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Statistiques générales
        $stats = [
            'active_boosts' => ProductBoost::where('user_id', $user->id)
                                          ->where('status', 'active')
                                          ->count(),
            'total_spent' => ProductBoost::where('user_id', $user->id)
                                       ->whereIn('status', ['active', 'expired', 'cancelled'])
                                       ->sum('total_price'),
            'total_views' => ProductBoost::where('user_id', $user->id)
                                       ->sum('views_generated'),
            'total_clicks' => ProductBoost::where('user_id', $user->id)
                                        ->sum('clicks_generated')
        ];

        // Boosts actifs
        $activeBoosts = ProductBoost::where('user_id', $user->id)
                                  ->where('status', 'active')
                                  ->with(['item.category', 'boostType'])
                                  ->orderBy('activated_at', 'desc')
                                  ->get();

        // Boosts expirés
        $expiredBoosts = ProductBoost::where('user_id', $user->id)
                                   ->where('status', 'expired')
                                   ->with(['item.category', 'boostType'])
                                   ->orderBy('expires_at', 'desc')
                                   ->get();

        // Boosts annulés
        $cancelledBoosts = ProductBoost::where('user_id', $user->id)
                                     ->where('status', 'cancelled')
                                     ->with(['item.category', 'boostType'])
                                     ->orderBy('cancelled_at', 'desc')
                                     ->get();

        return view('boost.dashboard', compact('stats', 'activeBoosts', 'expiredBoosts', 'cancelledBoosts'));
    }

    /**
     * Calculer le montant de remboursement selon la politique de remboursement
     * 
     * Politique:
     * - Annulation dans les 24h: remboursement complet
     * - Annulation après 24h: remboursement partiel basé sur le temps restant
     * - Annulation après 50% de la durée: aucun remboursement
     */
    private function calculateRefundAmount(ProductBoost $boost)
    {
        $now = now();
        $activatedAt = $boost->activated_at;
        $expiresAt = $boost->expires_at;
        
        // Temps écoulé depuis l'activation (en heures)
        $elapsedHours = $activatedAt->diffInHours($now);
        
        // Durée totale du boost (en heures)
        $totalHours = $activatedAt->diffInHours($expiresAt);
        
        // Si annulation dans les 24h: remboursement complet
        if ($elapsedHours <= 24) {
            return $boost->total_price;
        }
        
        // Calculer le pourcentage de temps écoulé
        $timeElapsedRatio = $elapsedHours / $totalHours;
        
        // Si plus de 50% du temps est écoulé: pas de remboursement
        if ($timeElapsedRatio >= 0.5) {
            return 0;
        }
        
        // Remboursement partiel: 50% du temps restant
        $remainingTimeRatio = 1 - $timeElapsedRatio;
        return round($boost->total_price * $remainingTimeRatio * 0.5);
    }

    /**
     * API: Récupérer les produits de l'utilisateur connecté pour le boost
     */
    public function getUserItems()
    {
        $items = Item::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->select('id', 'name', 'price', 'status', 'images')
                    ->get()
                    ->map(function ($item) {
                        // Désactiver les appends pour éviter les erreurs
                        $item->setAppends([]);
                        
                        // Convertir name en title pour compatibilité JavaScript
                        $item->title = $item->name;
                        
                        // Décoder les images JSON et créer la structure attendue
                        $images = [];
                        if ($item->images) {
                            $imageArray = $item->images;
                            // Si c'est une string JSON, la décoder
                            if (is_string($imageArray)) {
                                $imageArray = json_decode($imageArray, true);
                            }
                            // Si c'est un array
                            if (is_array($imageArray)) {
                                foreach ($imageArray as $imagePath) {
                                    $images[] = [
                                        'image_url' => asset('storage/' . $imagePath)
                                    ];
                                }
                            }
                        }
                        $item->images = $images;
                        
                        // Vérifier les boosts actifs
                        $activeBoosts = ProductBoost::where('item_id', $item->id)
                                                  ->where('status', 'active')
                                                  ->where('expires_at', '>', now())
                                                  ->get();
                        
                        $item->active_boosts = $activeBoosts;
                        
                        return $item;
                    });

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    /**
     * Récupérer les durées disponibles pour un type de boost
     */
    public function getDurations(BoostType $boostType)
    {
        $durations = $boostType->available_durations;
        
        // Si c'est une chaîne JSON, la décoder
        if (is_string($durations)) {
            $durations = json_decode($durations, true);
        }
        
        // Fallback vers des durées par défaut EN JOURS
        if (!is_array($durations) || empty($durations)) {
            // Durées par défaut basées sur min_duration et max_duration du boost type
            $minDuration = $boostType->min_duration ?? 1;
            $maxDuration = $boostType->max_duration ?? 30;
            
            // Proposer des durées standards : 1, 3, 7, 14, 21, 30 jours
            $standardDurations = [1, 3, 7, 14, 21, 30];
            $durations = array_filter($standardDurations, function($d) use ($minDuration, $maxDuration) {
                return $d >= $minDuration && $d <= $maxDuration;
            });
            
            // Si aucune durée standard ne convient, utiliser min et max
            if (empty($durations)) {
                $durations = [$minDuration];
                if ($maxDuration > $minDuration) {
                    $durations[] = $maxDuration;
                }
            }
        }
        
        // S'assurer que toutes les durées sont des entiers
        $durations = array_map('intval', array_values($durations));
        
        // Trier les durées
        sort($durations);
        
        return response()->json([
            'success' => true,
            'durations' => $durations
        ]);
    }
}