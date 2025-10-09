<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExchangeRateController extends Controller
{
    /**
     * Récupère le taux de change USD/CDF depuis une API Forex réelle
     */
    public function getRate()
    {
        try {
            // Cache le taux pendant 1 heure (configurable dans .env)
            $cacheDuration = config('services.forex.cache_duration', 3600);
            
            $rate = Cache::remember('usd_cdf_rate', $cacheDuration, function () {
                return $this->fetchLiveRate();
            });

            // Si le taux n'a pas pu être récupéré, utiliser un taux de secours
            if (!$rate) {
                $rate = $this->getFallbackRate();
                Log::warning('Utilisation du taux de change de secours: ' . $rate);
            }

            return response()->json([
                'status' => 'success',
                'from' => 'USD',
                'to' => 'CDF',
                'rate' => $rate,
                'cached' => true,
                'updated_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du taux de change: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'success',
                'from' => 'USD',
                'to' => 'CDF',
                'rate' => $this->getFallbackRate(),
                'cached' => false,
                'fallback' => true,
                'updated_at' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Récupère le taux de change en direct depuis l'API Forex
     */
    private function fetchLiveRate()
    {
        $provider = config('services.forex.provider', 'exchangerate-api');
        
        try {
            switch ($provider) {
                case 'exchangerate-api':
                    return $this->fetchFromExchangeRateAPI();
                
                case 'currencyapi':
                    return $this->fetchFromCurrencyAPI();
                
                case 'fixer':
                    return $this->fetchFromFixerAPI();
                
                default:
                    return $this->fetchFromExchangeRateAPI();
            }
        } catch (\Exception $e) {
            Log::error("Erreur API Forex ({$provider}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère le taux depuis ExchangeRate-API (gratuit, pas de clé requise pour la version basique)
     */
    private function fetchFromExchangeRateAPI()
    {
        $apiKey = config('services.forex.exchangerate_api_key');
        
        // Si pas de clé API, utiliser la version gratuite sans clé
        if (empty($apiKey)) {
            $url = 'https://open.exchangerate-api.com/v6/latest/USD';
        } else {
            $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD";
        }
        
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['rates']['CDF'])) {
                $rate = $data['rates']['CDF'];
                Log::info("Taux USD/CDF récupéré depuis ExchangeRate-API: {$rate}");
                return $rate;
            }
        }
        
        return null;
    }

    /**
     * Récupère le taux depuis CurrencyAPI
     */
    private function fetchFromCurrencyAPI()
    {
        $apiKey = config('services.forex.exchangerate_api_key');
        
        if (empty($apiKey)) {
            return null;
        }
        
        $url = "https://api.currencyapi.com/v3/latest";
        
        $response = Http::timeout(10)->get($url, [
            'apikey' => $apiKey,
            'base_currency' => 'USD',
            'currencies' => 'CDF'
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['data']['CDF']['value'])) {
                $rate = $data['data']['CDF']['value'];
                Log::info("Taux USD/CDF récupéré depuis CurrencyAPI: {$rate}");
                return $rate;
            }
        }
        
        return null;
    }

    /**
     * Récupère le taux depuis Fixer.io
     */
    private function fetchFromFixerAPI()
    {
        $apiKey = config('services.forex.exchangerate_api_key');
        
        if (empty($apiKey)) {
            return null;
        }
        
        $url = "https://api.fixer.io/latest";
        
        $response = Http::timeout(10)->get($url, [
            'access_key' => $apiKey,
            'base' => 'USD',
            'symbols' => 'CDF'
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['rates']['CDF'])) {
                $rate = $data['rates']['CDF'];
                Log::info("Taux USD/CDF récupéré depuis Fixer.io: {$rate}");
                return $rate;
            }
        }
        
        return null;
    }

    /**
     * Taux de secours en cas d'échec de l'API
     * Basé sur le taux moyen du marché congolais
     */
    private function getFallbackRate()
    {
        // Taux de secours basé sur le marché de Kinshasa
        // À mettre à jour régulièrement ou à stocker en base de données
        return 2650.00; // 1 USD = 2650 CDF (taux approximatif octobre 2025)
    }

    /**
     * Convertit un montant d'une devise à une autre
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'from' => 'required|in:USD,CDF',
            'to' => 'required|in:USD,CDF'
        ]);

        $amount = $request->amount;
        $from = $request->from;
        $to = $request->to;

        // Si les devises sont identiques, pas besoin de conversion
        if ($from === $to) {
            return response()->json([
                'status' => 'success',
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'converted_amount' => $amount,
                'rate' => 1
            ]);
        }

        // Récupérer le taux de change en cache
        $cacheDuration = config('services.forex.cache_duration', 3600);
        $rate = Cache::remember('usd_cdf_rate', $cacheDuration, function () {
            $liveRate = $this->fetchLiveRate();
            return $liveRate ?: $this->getFallbackRate();
        });

        // Effectuer la conversion
        $convertedAmount = $from === 'USD' 
            ? $amount * $rate    // USD vers CDF
            : $amount / $rate;   // CDF vers USD

        return response()->json([
            'status' => 'success',
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'rate' => $rate,
            'converted_amount' => round($convertedAmount, 2),
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Force le rafraîchissement du taux de change
     * (accessible uniquement aux admins)
     */
    public function refreshRate(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Non autorisé'
            ], 403);
        }

        try {
            // Vider le cache
            Cache::forget('usd_cdf_rate');
            
            // Récupérer le nouveau taux
            $newRate = $this->fetchLiveRate();
            
            if ($newRate) {
                // Mettre en cache
                Cache::put('usd_cdf_rate', $newRate, config('services.forex.cache_duration', 3600));
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Taux de change mis à jour',
                    'rate' => $newRate,
                    'updated_at' => now()->toIso8601String()
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Impossible de récupérer le taux de change',
                    'fallback_rate' => $this->getFallbackRate()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors du rafraîchissement du taux: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du rafraîchissement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retourne l'historique des taux de change (simulé pour le moment)
     */
    public function history(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        // TODO: Implémenter avec une vraie API ou base de données d'historique
        // Pour l'instant, retourner des données simulées
        $history = [];
        $baseRate = Cache::get('usd_cdf_rate', $this->getFallbackRate());
        $date = strtotime($request->start_date);
        $end = strtotime($request->end_date);

        while ($date <= $end) {
            $variation = rand(-100, 100);
            $history[] = [
                'date' => date('Y-m-d', $date),
                'rate' => round($baseRate + $variation, 2)
            ];
            $date = strtotime('+1 day', $date);
        }

        return response()->json([
            'status' => 'success',
            'from' => 'USD',
            'to' => 'CDF',
            'history' => $history
        ]);
    }
}
