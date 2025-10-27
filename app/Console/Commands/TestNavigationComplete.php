<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestNavigationComplete extends Command
{
    protected $signature = 'test:navigation';
    protected $description = 'Teste tous les scénarios de navigation';

    public function handle()
    {
        $this->info("🧪 Test Complet de Navigation - VintApp");
        $this->line("");
        
        // Test 1: Routes existantes
        $this->info("📋 1. Vérification des Routes");
        $this->testRoutes();
        $this->line("");
        
        // Test 2: Redirection Admin
        $this->info("🛡️  2. Test Redirection Admin");
        $this->call('test:admin-redirect', ['user_id' => 1]);
        $this->line("");
        
        // Test 3: Utilisateur Normal (s'il existe)
        $this->info("👤 3. Test Utilisateur Normal");
        $userExists = \App\Models\User::where('email', 'test@example.com')->exists();
        if ($userExists) {
            $user = \App\Models\User::where('email', 'test@example.com')->first();
            $this->call('test:admin-redirect', ['user_id' => $user->id]);
        } else {
            $this->warn("   ⚠️  Utilisateur test non trouvé");
            $this->line("   💡 Créez un avec: php artisan create:test-user");
        }
        $this->line("");
        
        // Test 4: Navigation URLs
        $this->info("🔗 4. URLs de Navigation");
        $this->testNavigationUrls();
        $this->line("");
        
        // Résumé
        $this->info("✅ Test de Navigation Terminé");
        $this->line("");
        $this->info("🚀 Instructions de Test Manuel:");
        $this->line("1. 🌐 Navigation privée → Allez sur votre domaine → Vérifiez page splash");
        $this->line("2. 👤 Connectez-vous utilisateur normal → Vérifiez page d'accueil");
        $this->line("3. 🛡️  Connectez-vous admin → Vérifiez redirection /admin");
    }
    
    private function testRoutes()
    {
        $routes = [
            '/' => 'Route racine',
            '/splash' => 'Page splash',
            '/home' => 'Page d\'accueil',
            '/dashboard' => 'Dashboard utilisateur',
            '/admin' => 'Dashboard admin'
        ];
        
        foreach ($routes as $uri => $description) {
            try {
                $route = \Route::getRoutes()->match(
                    \Illuminate\Http\Request::create($uri, 'GET')
                );
                $this->line("   ✅ {$description} : {$uri}");
            } catch (\Exception $e) {
                $this->line("   ❌ {$description} : {$uri} - {$e->getMessage()}");
            }
        }
    }
    
    private function testNavigationUrls()
    {
        try {
            $homeUrl = route('home');
            $this->line("   🏠 Home: {$homeUrl}");
        } catch (\Exception $e) {
            $this->line("   ❌ Home route error: {$e->getMessage()}");
        }
        
        try {
            $splashUrl = route('splash');
            $this->line("   🌟 Splash: {$splashUrl}");
        } catch (\Exception $e) {
            $this->line("   ❌ Splash route error: {$e->getMessage()}");
        }
        
        try {
            $dashboardUrl = route('dashboard');
            $this->line("   📊 Dashboard: {$dashboardUrl}");
        } catch (\Exception $e) {
            $this->line("   ❌ Dashboard route error: {$e->getMessage()}");
        }
        
        try {
            $adminUrl = route('admin.dashboard');
            $this->line("   🛡️  Admin: {$adminUrl}");
        } catch (\Exception $e) {
            $this->line("   ❌ Admin route error: {$e->getMessage()}");
        }
    }
}