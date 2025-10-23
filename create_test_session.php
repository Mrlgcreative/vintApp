<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Récupérer tous les utilisateurs
$users = App\Models\User::all();

if ($users->isEmpty()) {
    echo "❌ Aucun utilisateur trouvé dans la base de données.\n";
    exit(1);
}

echo "📊 Création de sessions de test pour tous les utilisateurs...\n\n";

foreach ($users as $user) {
    $sessionId = 'test-session-' . $user->id . '-' . uniqid();
    
    try {
        App\Models\UserSession::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'ip_address' => '192.168.1.' . rand(1, 254),
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'device_type' => ['mobile', 'tablet', 'desktop'][rand(0, 2)],
            'browser' => ['Chrome', 'Firefox', 'Safari', 'Edge'][rand(0, 3)],
            'os' => ['Windows', 'macOS', 'Linux', 'Android', 'iOS'][rand(0, 4)],
            'last_activity' => now()->subMinutes(rand(0, 4)),
            'login_at' => now()->subMinutes(rand(5, 60)),
            'is_active' => true,
        ]);
        
        echo "✅ Session créée pour: {$user->name} ({$user->email})\n";
    } catch (\Exception $e) {
        echo "❌ Erreur pour {$user->name}: " . $e->getMessage() . "\n";
    }
}

$activeCount = App\Models\UserSession::where('is_active', true)->count();
echo "\n✅ Total: {$activeCount} sessions actives créées.\n";
echo "🔗 Visitez: /admin/users/online\n";
