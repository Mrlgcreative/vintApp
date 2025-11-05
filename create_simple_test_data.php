<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

echo "=== CRÉATION DE DONNÉES SIMPLIFIÉE ===\n\n";

try {
    DB::beginTransaction();
    
    // 1. Créer des codes de parrainage pour les utilisateurs
    $users = User::limit(5)->get();
    
    echo "📝 Attribution de codes et création de référentiels...\n";
    $referrers = [];
    
    foreach ($users as $user) {
        if (!$user->referral_code) {
            $code = strtoupper(Str::random(6));
            $user->referral_code = $code;
            $user->save();
        }
        
        // Créer dans referral_codes si besoin
        $codeId = DB::table('referral_codes')->insertGetId([
            'user_id' => $user->id,
            'code' => $user->referral_code,
            'is_active' => true,
            'usage_limit' => 100,
            'current_usage' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $referrers[] = ['user' => $user, 'code_id' => $codeId];
        echo "   - {$user->name}: {$user->referral_code} (ID: {$codeId})\n";
    }
    
    // 2. Créer des parrainages
    echo "\n🤝 Création de parrainages...\n";
    $otherUsers = User::whereNotIn('id', collect($referrers)->pluck('user.id'))->limit(6)->get();
    
    $referralIndex = 0;
    foreach ($referrers as $index => $referrerData) {
        if ($index >= 3) break; // Seulement les 3 premiers sont des parrains
        
        $referrer = $referrerData['user'];
        $codeId = $referrerData['code_id'];
        $numReferrals = [3, 2, 1][$index];
        
        for ($i = 0; $i < $numReferrals && $referralIndex < count($otherUsers); $i++) {
            $referred = $otherUsers[$referralIndex];
            
            // Marquer comme référé
            $referred->referred_by = $referrer->id;
            $referred->save();
            
            // Créer le parrainage
            DB::table('referrals')->insert([
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'referral_code_id' => $codeId,
                'status' => 'completed',
                'points_earned' => 100,
                'activated_at' => now()->subDays(rand(1, 30)),
                'completed_at' => now()->subDays(rand(0, 5)),
                'conditions_met' => true,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
            
            // Incrémenter l'usage du code
            DB::table('referral_codes')->where('id', $codeId)->increment('current_usage');
            
            echo "   - {$referrer->name} a parrainé {$referred->name}\n";
            $referralIndex++;
        }
    }
    
    // 3. Mettre à jour les points
    echo "\n💰 Mise à jour des points...\n";
    foreach ($referrers as $referrerData) {
        $referrer = $referrerData['user'];
        $referralCount = DB::table('referrals')->where('referrer_id', $referrer->id)->count();
        
        if ($referralCount > 0) {
            $totalPoints = $referralCount * 100;
            $level = min(5, floor($referralCount / 2) + 1);
            
            DB::table('user_points')->updateOrInsert(
                ['user_id' => $referrer->id],
                [
                    'total_points' => $totalPoints,
                    'available_points' => $totalPoints * 0.8,
                    'pending_points' => $totalPoints * 0.1,
                    'redeemed_points' => $totalPoints * 0.1,
                    'level' => $level,
                    'level_multiplier' => 1 + ($level - 1) * 0.2,
                    'last_activity_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            
            echo "   - {$referrer->name}: {$totalPoints} points (Niveau {$level}, {$referralCount} parrainages)\n";
        }
    }
    
    DB::commit();
    
    // Résumé
    echo "\n=== RÉSUMÉ ===\n";
    echo "✅ Codes créés: " . DB::table('referral_codes')->count() . "\n";
    echo "✅ Parrainages: " . DB::table('referrals')->count() . "\n";
    echo "✅ Parrains actifs: " . DB::table('referrals')->distinct('referrer_id')->count() . "\n";
    
    echo "\n🎯 PARRAINS AVEC LEURS DONNÉES:\n";
    $activeReferrers = DB::table('referrals')
        ->select('referrer_id', DB::raw('COUNT(*) as count'))
        ->groupBy('referrer_id')
        ->get();
        
    foreach ($activeReferrers as $ref) {
        $user = User::find($ref->referrer_id);
        echo "   - {$user->name}: {$ref->count} parrainages, Code: {$user->referral_code}\n";
    }
    
    echo "\n✅ Données créées avec succès ! Vous pouvez maintenant tester l'interface.\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}

echo "\n=== FIN ===\n";