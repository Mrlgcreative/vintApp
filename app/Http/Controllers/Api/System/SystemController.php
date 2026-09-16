<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'VintApp API is running',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String()
        ]);
    }

    public function currencies(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'code' => 'USD',
                    'name' => 'Dollar américain',
                    'symbol' => '$',
                    'flag' => '🇺🇸'
                ],
                [
                    'code' => 'CDF',
                    'name' => 'Franc congolais',
                    'symbol' => 'FC',
                    'flag' => '🇨🇩'
                ],
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function fees(): JsonResponse
    {
        $commissionPercentage = (float) (DB::table('settings')
            ->where('key', 'platform_commission_percentage')
            ->value('value') ?? 10);

        $transportFeePercentage = (float) (DB::table('settings')
            ->where('key', 'transport_fee_percentage')
            ->value('value') ?? 5);

        return response()->json([
            'success' => true,
            'data' => [
                'commission_percentage' => $commissionPercentage,
                'transport_fee_percentage' => $transportFeePercentage,
            ],
        ]);
    }
}
