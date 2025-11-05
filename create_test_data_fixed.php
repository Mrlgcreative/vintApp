<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

echo "=== CRÉATION DE DONNÉES DE TEST CORRIGÉE ===\n\n";

try {
    DB::beginTransaction();
    
    // 1. Créer des codes de parrainage pour les utilisateurs existants
    $users = User::whereNull('referral_code')->limit(5)->get();
    
    echo "📝 Création de codes de parrainage...\n";
    foreach ($users as $user) {
        $code = strtoupper(Str::random(6));
        $user->referral_code = $code;
        $user->save();
        echo "   - {$user->name}: {$code}\n";
    }
    
    // 2. Créer des parrainages simples
    echo "\n🤝 Création de parrainages de test...\n";
    $referrers = User::whereNotNull('referral_code')->take(3)->get();
    $availableUsers = User::whereNull('referred_by')->whereNotIn('id', $referrers->pluck('id'))->take(8)->get();
    
    $referralCount = 0;
    foreach ($referrers as $index => $referrer) {
        // Chaque parrain aura un nombre différent de parrainés
        $numReferrals = [3, 2, 1][$index] ?? 1;
        
        for ($i = 0; $i < $numReferrals && $referralCount < count($availableUsers); $i++) {
            $referred = $availableUsers[$referralCount];
            
            // Mettre à jour l'utilisateur référé
            $referred->referred_by = $referrer->id;
            $referred->save();
            
            // Créer l'entrée dans la table referrals (structure correcte)
            DB::table('referrals')->insert([
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'status' => 'completed',
                'points_earned' => 100,
                'activated_at' => now()->subDays(rand(1, 30)),
                'completed_at' => now()->subDays(rand(0, 5)),
                'conditions_met' => true,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
            
            echo "   - {$referrer->name} a parrainé {$referred->name}\n";
            $referralCount++;
        }
    }
    
    // 3. Créer/Mettre à jour des points pour les parrains
    echo "\n💰 Attribution de points aux parrains...\n";
    foreach ($referrers as $referrer) {
        $referralCount = DB::table('referrals')->where('referrer_id', $referrer->id)->count();
        $totalPoints = $referralCount * 100;
        $availablePoints = $totalPoints * 0.8;
        $level = min(5, floor($referralCount / 2) + 1);
        
        DB::table('user_points')->updateOrInsert(
            ['user_id' => $referrer->id],
            [
                'total_points' => $totalPoints,
                'available_points' => $availablePoints,
                'pending_points' => $totalPoints * 0.1,
                'redeemed_points' => $totalPoints * 0.1,
                'level' => $level,
                'level_multiplier' => 1 + ($level - 1) * 0.2,
                'last_activity_at' => now(),
                'updated_at' => now(),
                'created_at' => DB::table('user_points')->where('user_id', $referrer->id)->exists() ? 
                    DB::table('user_points')->where('user_id', $referrer->id)->value('created_at') : now(),
            ]
        );
        
        echo "   - {$referrer->name}: {$totalPoints} points (Niveau {$level})\n";
    }
    
    // 4. Créer des transactions de points
    echo "\n📈 Création de transactions de points...\n";
    foreach ($referrers as $referrer) {
        $referrals = DB::table('referrals')->where('referrer_id', $referrer->id)->get();
        
        foreach ($referrals as $referral) {
            DB::table('point_transactions')->insert([
                'user_id' => $referrer->id,
                'type' => 'earned',
                'amount' => 100,
                'description' => 'Points de parrainage',
                'reference_type' => 'referral',
                'reference_id' => $referral->id,
                'status' => 'completed',
                'created_at' => $referral->created_at,
                'updated_at' => now(),
            ]);
        }
        
        $transactionCount = DB::table('point_transactions')->where('user_id', $referrer->id)->count();
        echo "   - {$referrer->name}: {$transactionCount} transactions\n";
    }
    
    DB::commit();
    
    // 5. Afficher le résumé final
    echo "\n=== RÉSUMÉ DES DONNÉES CRÉÉES ===\n";
    echo "✅ Utilisateurs avec codes de parrainage: " . User::whereNotNull('referral_code')->count() . "\n";
    echo "✅ Parrainages effectués: " . DB::table('referrals')->count() . "\n";
    echo "✅ Utilisateurs avec points: " . DB::table('user_points')->where('total_points', '>', 0)->count() . "\n";
    echo "✅ Transactions de points: " . DB::table('point_transactions')->count() . "\n";
    
    echo "\n🎯 TOP PARRAINS:\n";
    $topReferrers = DB::table('referrals')
        ->select('referrer_id', DB::raw('COUNT(*) as referral_count'))
        ->groupBy('referrer_id')
        ->orderBy('referral_count', 'desc')
        ->get();
        
    foreach ($topReferrers as $stat) {
        $user = User::find($stat->referrer_id);
        $points = DB::table('user_points')->where('user_id', $stat->referrer_id)->first();
        echo "   - {$user->name}: {$stat->referral_count} parrainages";
        if ($points) {
            echo ", {$points->total_points} points (Niveau {$points->level})";
        }
        echo "\n";
    }
    
    echo "\n✅ Données de test créées avec succès !\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA CRÉATION ===\n";