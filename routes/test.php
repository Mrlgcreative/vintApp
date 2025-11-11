<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ProductAuthenticityCheck;

Route::get('/test-expert-view/{id}', function ($id) {
    try {
        $check = ProductAuthenticityCheck::findOrFail($id);
        
        // Charger les relations comme dans le contrôleur
        $check->load([
            'item.category',
            'item.brand', 
            'item.user',
            'vendor',
            'verificationImages',
            'auditLogs.performer'
        ]);
        
        // Tester chaque expression individuellement
        $tests = [
            'item_name' => $check->item->name ?? 'Produit sans nom',
            'item_price' => number_format($check->item->price, 0, ',', ' '),
            'item_currency' => $check->item->currency,
            'category_name' => $check->item->category->name ?? 'Non spécifiée',
            'brand_name' => $check->item->brand->name ?? 'Non spécifiée',
            'condition' => ucfirst($check->item->condition ?? 'Non spécifié'),
            'description' => $check->item->description,
            'images' => $check->item->images ?? []
        ];
        
        return response()->json([
            'success' => true,
            'check_id' => $check->id,
            'item_id' => $check->item_id,
            'tests' => $tests
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});