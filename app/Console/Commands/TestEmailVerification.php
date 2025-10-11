<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TestEmailVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-verification {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester l\'envoi d\'email de vérification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test d\'envoi d\'email de vérification');
        $this->info('=========================================');
        $this->newLine();

        // Récupérer l'utilisateur
        $email = $this->argument('email');
        
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("❌ Utilisateur avec l'email '{$email}' non trouvé.");
                return 1;
            }
        } else {
            $user = User::whereNull('email_verified_at')->latest()->first();
            if (!$user) {
                $this->error('❌ Aucun utilisateur non vérifié trouvé.');
                $this->info('💡 Créez un compte via /register pour tester.');
                return 1;
            }
        }

        $this->info('👤 Utilisateur trouvé :');
        $this->line("   - ID: {$user->id}");
        $this->line("   - Email: {$user->email}");
        $this->line("   - Créé le: {$user->created_at}");
        $this->line("   - Email vérifié: " . ($user->email_verified_at ? 'Oui ✅' : 'Non ❌'));
        $this->newLine();

        $this->info('📧 Envoi de l\'email de vérification...');

        try {
            $user->sendEmailVerificationNotification();
            $this->info('✅ Email envoyé avec succès !');
            $this->newLine();
            $this->line("📬 Vérifiez votre boîte email : {$user->email}");
            $this->line("📁 Ou vérifiez les logs dans : storage/logs/laravel.log");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi : ' . $e->getMessage());
            $this->newLine();
            $this->error('📝 Trace complète :');
            $this->error($e->getTraceAsString());
            
            return 1;
        }
    }
}
