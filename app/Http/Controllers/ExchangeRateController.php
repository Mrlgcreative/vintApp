<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExchangeRateController extends Controller
{
    /**
     * Récupère le taux de change USD/CDF
     */
    public function getRate()
    {
        // Cache le taux pendant 1 heure
        $rate = Cache::remember('usd_cdf_rate', 3600, function () {
            // Ici, vous devriez implémenter l'appel à votre API de taux de change
            // Pour l'exemple, nous utilisons un taux fixe
            return 2500.00;
        });

        return response()->json([
            'status' => 'success',
            'from' => 'USD',
            'to' => 'CDF',
            'rate' => $rate
        ]);
    }

    /**
     * Convertit un montant d'une devise à une autre
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
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
                'converted_amount' => $amount
            ]);
        }

        // Récupérer le taux de change
        $rate = Cache::remember('usd_cdf_rate', 3600, function () {
            return 2500.00; // Taux fixe pour l'exemple
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
            'converted_amount' => round($convertedAmount, 2)
        ]);
    }

    /**
     * Retourne l'historique des taux de change (à implémenter avec une vraie API)
     */
    public function history(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        // Ici, vous devriez implémenter l'appel à votre API pour récupérer l'historique
        // Pour l'exemple, nous retournons des données factices
        $history = [];
        $date = strtotime($request->start_date);
        $end = strtotime($request->end_date);

        while ($date <= $end) {
            $history[] = [
                'date' => date('Y-m-d', $date),
                'rate' => 2500 + rand(-50, 50)
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