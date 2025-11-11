<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== Configuration Admin pour Remboursements ===\n\n";
    
    // Vérifier s'il y a un rôle admin
    $adminRole = \Illuminate\Support\Facades\DB::table('roles')->where('slug', 'admin')->first();
    
    if (!$adminRole) {
        echo "Création du rôle admin...\n";
        $adminRoleId = \Illuminate\Support\Facades\DB::table('roles')->insertGetId([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Administrateur système',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "Rôle admin créé avec ID: $adminRoleId\n";
    } else {
        echo "Rôle admin existe: {$adminRole->name} (ID: {$adminRole->id})\n";
        $adminRoleId = $adminRole->id;
    }
    
    // Vérifier s'il y a un utilisateur admin
    $adminUser = \App\Models\User::where('email', 'admin@vintapp.com')->first();
    
    if (!$adminUser) {
        echo "\nCréation d'un utilisateur admin...\n";
        $adminUser = \App\Models\User::create([
            'name' => 'Administrator',
            'email' => 'admin@vintapp.com',
            'password' => bcrypt('admin123456'),
            'email_verified_at' => now()
        ]);
        echo "Utilisateur admin créé: {$adminUser->email}\n";
    } else {
        echo "\nUtilisateur admin existe: {$adminUser->email}\n";
    }
    
    // Assigner le rôle admin à l'utilisateur
    $hasAdminRole = \Illuminate\Support\Facades\DB::table('role_user')
        ->where('user_id', $adminUser->id)
        ->where('role_id', $adminRoleId)
        ->exists();
    
    if (!$hasAdminRole) {
        echo "Attribution du rôle admin à l'utilisateur...\n";
        \Illuminate\Support\Facades\DB::table('role_user')->insert([
            'user_id' => $adminUser->id,
            'role_id' => $adminRoleId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "Rôle admin attribué avec succès!\n";
    } else {
        echo "L'utilisateur a déjà le rôle admin.\n";
    }
    
    echo "\n=== Informations de connexion ===\n";
    echo "Email: admin@vintapp.com\n";
    echo "Mot de passe: admin123456\n";
    echo "URL: http://localhost:8000/admin/refunds\n";
    
    // Test de la méthode isAdmin
    echo "\n=== Test de la logique admin ===\n";
    \Illuminate\Support\Facades\Auth::login($adminUser);
    
    $controller = new \App\Http\Controllers\Admin\RefundController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('isAdmin');
    $method->setAccessible(true);
    
    $isAdminResult = $method->invoke($controller);
    echo "Test isAdmin(): " . ($isAdminResult ? "✅ TRUE" : "❌ FALSE") . "\n";
    
    // Test de l'accès aux remboursements
    echo "\n=== Test d'accès aux remboursements ===\n";
    $request = new \Illuminate\Http\Request();
    
    try {
        $result = $controller->index($request);
        echo "Méthode index(): ✅ Success - " . get_class($result) . "\n";
        
        // Extraire les données de la vue
        if (method_exists($result, 'getData')) {
            $viewData = $result->getData();
            if (isset($viewData['refunds'])) {
                $refunds = $viewData['refunds'];
                echo "Nombre de remboursements trouvés: " . $refunds->count() . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "Méthode index(): ❌ Error - " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}