<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

echo "=== CRÉATION DE DONNÉES DE TEST POUR LE SYSTÈME DE PARRAINAGE ===\n\n";

try {
    DB::beginTransaction();
    
    // 1. Créer des codes de parrainage pour les utilisateurs existants
    $users = User::whereNull('referral_code')->limit(10)->get();
    $referralCodes = [];
    
    echo "📝 Création de codes de parrainage...\n";
    foreach ($users as $user) {
        $code = strtoupper(Str::random(6));
        $user->referral_code = $code;
        $user->save();
        $referralCodes[] = $code;
        echo "   - {$user->name}: {$code}\n";
    }
    
    // 2. Créer des parrainages
    echo "\n🤝 Création de parrainages de test...\n";
    $referrers = User::whereNotNull('referral_code')->take(3)->get();
    $availableUsers = User::whereNull('referred_by')->where('id', '!=', $referrers->first()->id)->take(8)->get();
    
    $referralCount = 0;
    foreach ($referrers as $index => $referrer) {
        // Chaque parrain aura un nombre différent de parrainés
        $numReferrals = [5, 3, 2][$index] ?? 1;
        
        for ($i = 0; $i < $numReferrals && $referralCount < count($availableUsers); $i++) {
            $referred = $availableUsers[$referralCount];
            
            // Mettre à jour l'utilisateur référé
            $referred->referred_by = $referrer->id;
            $referred->save();
            
            // Créer l'entrée dans la table referrals
            DB::table('referrals')->insert([
                'referrer_id' => $referrer->id,
                'referred_id' => $referred->id,
                'referral_code' => $referrer->referral_code,
                'status' => 'completed',
                'points_awarded' => 100,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
            
            echo "   - {$referrer->name} a parrainé {$referred->name}\n";
            $referralCount++;
        }
    }
    
    // 3. Créer des points pour les parrains
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
    
    // 5. Créer quelques récompenses d'affiliation
    echo "\n🎁 Création de récompenses d'affiliation...\n";
    $topReferrer = $referrers->first();
    
    $rewards = [
        [
            'user_id' => $topReferrer->id,
            'type' => 'badge',
            'value' => json_encode(['name' => 'top_referrer', 'display_name' => 'Top Parrain']),
            'reason' => 'Performance exceptionnelle en parrainage',
            'status' => 'active',
            'expires_at' => null,
        ],
        [
            'user_id' => $topReferrer->id,
            'type' => 'points',
            'value' => json_encode(['amount' => 500]),
            'reason' => 'Bonus pour 5 parrainages',
            'status' => 'active',
            'expires_at' => null,
        ]
    ];
    
    foreach ($rewards as $reward) {
        $reward['created_at'] = now();
        $reward['updated_at'] = now();
        DB::table('affiliate_rewards')->insert($reward);
    }
    
    echo "   - 2 récompenses créées pour {$topReferrer->name}\n";
    
    DB::commit();
    
    // 6. Afficher le résumé final
    echo "\n=== RÉSUMÉ DES DONNÉES CRÉÉES ===\n";
    echo "✅ Codes de parrainage: " . User::whereNotNull('referral_code')->count() . "\n";
    echo "✅ Parrainages effectués: " . DB::table('referrals')->count() . "\n";
    echo "✅ Utilisateurs avec points: " . DB::table('user_points')->where('total_points', '>', 0)->count() . "\n";
    echo "✅ Transactions de points: " . DB::table('point_transactions')->count() . "\n";
    echo "✅ Récompenses d'affiliation: " . DB::table('affiliate_rewards')->count() . "\n";
    
    echo "\n🎯 TOP 3 PARRAINS:\n";
    $topReferrers = DB::table('referrals')
        ->select('referrer_id', DB::raw('COUNT(*) as referral_count'))
        ->groupBy('referrer_id')
        ->orderBy('referral_count', 'desc')
        ->limit(3)
        ->get();
        
    foreach ($topReferrers as $stat) {
        $user = User::find($stat->referrer_id);
        $points = DB::table('user_points')->where('user_id', $stat->referrer_id)->first();
        echo "   - {$user->name}: {$stat->referral_count} parrainages, {$points->total_points} points (Niveau {$points->level})\n";
    }
    
    echo "\n✅ Données de test créées avec succès !\n";
    echo "🚀 Vous pouvez maintenant tester l'interface d'affiliation admin.\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE LA CRÉATION ===\n";