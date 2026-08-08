<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

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
}
