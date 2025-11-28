<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

echo "=== CRÉATION FINALE DE DONNÉES DE TEST ===\n\n";

try {
    DB::beginTransaction();
    
    // 1. Créer des codes de parrainage
    $users = User::limit(5)->get();
    
    echo "📝 Création des codes de parrainage...\n";
    $referrers = [];
    
    foreach ($users as $user) {
        if (!$user->referral_code) {
            $code = strtoupper(Str::random(6));
            $user->referral_code = $code;
            $user->save();
        }
        
        // Créer dans referral_codes avec la bonne structure
        $codeId = DB::table('referral_codes')->insertGetId([
            'user_id' => $user->id,
            'code' => $user->referral_code,
            'title' => "Code de {$user->name}",
            'description' => "Code de parrainage personnel",
            'is_active' => true,
            'max_uses' => 100,
            'current_uses' => 0,
            'bonus_points' => 100,
            'expires_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $referrers[] = ['user' => $user, 'code_id' => $codeId];
        echo "   - {$user->name}: {$user->referral_code} (Code ID: {$codeId})\n";
    }
    
    // 2. Créer des parrainages
    echo "\n🤝 Création des parrainages...\n";
    $otherUsers = User::whereNotIn('id', collect($referrers)->pluck('user.id'))->limit(6)->get();
    
    $referralIndex = 0;
    foreach ($referrers as $index => $referrerData) {
        if ($index >= 3) break; // Les 3 premiers deviennent parrains
        
        $referrer = $referrerData['user'];
        $codeId = $referrerData['code_id'];
        $numReferrals = [3, 2, 1][$index]; // Différents nombres de parrainages
        
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
            
            // Incrémenter l'usage
            DB::table('referral_codes')->where('id', $codeId)->increment('current_uses');
            
            echo "   - {$referrer->name} a parrainé {$referred->name}\n";
            $referralIndex++;
        }
    }
    
    // 3. Créer/Mettre à jour les points utilisateurs
    echo "\n💰 Attribution des points...\n";
    foreach ($referrers as $referrerData) {
        $referrer = $referrerData['user'];
        $referralCount = DB::table('referrals')->where('referrer_id', $referrer->id)->count();
        
        if ($referralCount > 0) {
            $totalPoints = $referralCount * 100;
            $level = min(5, floor($referralCount / 2) + 1);
            
            // Vérifier si l'entrée existe déjà
            $existingPoints = DB::table('user_points')->where('user_id', $referrer->id)->first();
            
            if ($existingPoints) {
                DB::table('user_points')->where('user_id', $referrer->id)->update([
                    'total_points' => $totalPoints,
                    'available_points' => $totalPoints * 0.8,
                    'pending_points' => $totalPoints * 0.1,
                    'redeemed_points' => $totalPoints * 0.1,
                    'level' => $level,
                    'level_multiplier' => 1 + ($level - 1) * 0.2,
                    'last_activity_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('user_points')->insert([
                    'user_id' => $referrer->id,
                    'total_points' => $totalPoints,
                    'available_points' => $totalPoints * 0.8,
                    'pending_points' => $totalPoints * 0.1,
                    'redeemed_points' => $totalPoints * 0.1,
                    'level' => $level,
                    'level_multiplier' => 1 + ($level - 1) * 0.2,
                    'last_activity_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            echo "   - {$referrer->name}: {$totalPoints} points (Niveau {$level})\n";
        }
    }
    
    DB::commit();
    
    // 4. Résumé final
    echo "\n=== RÉSUMÉ FINAL ===\n";
    echo "✅ Codes de parrainage: " . DB::table('referral_codes')->count() . "\n";
    echo "✅ Parrainages réalisés: " . DB::table('referrals')->count() . "\n";
    echo "✅ Parrains actifs: " . DB::table('referrals')->distinct('referrer_id')->count() . "\n";
    echo "✅ Utilisateurs avec points: " . DB::table('user_points')->where('total_points', '>', 0)->count() . "\n";
    
    echo "\n🏆 PARRAINS AVEC LEURS STATISTIQUES:\n";
    $stats = DB::table('referrals')
        ->join('users', 'referrals.referrer_id', '=', 'users.id')
        ->select('referrals.referrer_id', 'users.name', 'users.referral_code', DB::raw('COUNT(*) as total_referrals'))
        ->groupBy('referrals.referrer_id', 'users.name', 'users.referral_code')
        ->orderBy('total_referrals', 'desc')
        ->get();
        
    foreach ($stats as $stat) {
        $points = DB::table('user_points')->where('user_id', $stat->referrer_id)->first();
        echo "   - {$stat->name}: {$stat->total_referrals} parrainages, Code: {$stat->referral_code}";
        if ($points) {
            echo ", {$points->total_points} points (Niveau {$points->level})";
        }
        echo "\n";
    }
    
    echo "\n🎉 Données de test créées avec succès !\n";
    echo "🔧 Maintenant vous pouvez tester l'interface d'affiliation.\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}

echo "\n=== FIN DE LA CRÉATION ===\n";