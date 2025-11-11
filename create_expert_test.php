<?php
/**
 * Script temporaire pour créer un expert de test
 * À exécuter depuis le dossier vintapp: php create_expert_test.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\ExpertProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // 1. Vérifier si le rôle 'expert' existe, sinon le créer
    $expertRole = Role::where('slug', 'expert')->first();
    if (!$expertRole) {
        $expertRole = Role::create([
            'name' => 'Expert',
            'slug' => 'expert',
            'description' => 'Expert en vérification d\'authenticité'
        ]);
        echo "✅ Rôle 'expert' créé.\n";
    } else {
        echo "✅ Rôle 'expert' existe déjà.\n";
    }

    // 2. Créer un utilisateur expert de test
    $expertUser = User::where('email', 'expert@vintapp.com')->first();
    if (!$expertUser) {
        $expertUser = User::create([
            'name' => 'Expert Test',
            'email' => 'expert@vintapp.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'phone' => '+33123456789',
            'is_active' => true
        ]);
        echo "✅ Utilisateur expert créé: expert@vintapp.com / password123\n";
    } else {
        echo "✅ Utilisateur expert existe déjà.\n";
    }

    // 3. Assigner le rôle expert
    if (!$expertUser->roles->contains($expertRole)) {
        $expertUser->roles()->attach($expertRole);
        echo "✅ Rôle expert assigné à l'utilisateur.\n";
    } else {
        echo "✅ L'utilisateur a déjà le rôle expert.\n";
    }

    // 4. Créer le profil expert
    $expertProfile = ExpertProfile::where('user_id', $expertUser->id)->first();
    if (!$expertProfile) {
        $expertProfile = ExpertProfile::create([
            'user_id' => $expertUser->id,
            'level' => 'bronze',
            'specialties' => ['luxe', 'montres', 'sacs'],
            'commission_rate' => 5.0,
            'is_active' => true,
            'approval_rate' => 85.5,
            'verification_count' => 0,
            'bio' => 'Expert en authentification de produits de luxe avec 5 ans d\'expérience.',
            'credentials' => 'Certificat GIA, Formation horlogerie suisse'
        ]);
        echo "✅ Profil expert créé.\n";
    } else {
        echo "✅ Profil expert existe déjà.\n";
    }

    DB::commit();
    echo "\n🎉 Expert de test créé avec succès !\n";
    echo "📧 Email: expert@vintapp.com\n";
    echo "🔐 Mot de passe: password123\n";
    echo "🔗 URL: http://localhost:8000/expert\n";

} catch (Exception $e) {
    DB::rollback();
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}