<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\NewsletterSubscriber;
use App\Mail\WelcomeNewsletter;

class TestNewsletterEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:test-email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer un email de test pour vérifier la configuration de la newsletter';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address');
        
        $this->info('🧪 Test d\'envoi d\'email newsletter');
        $this->newLine();
        
        // Vérifier la configuration
        $this->line('📋 Configuration SMTP :');
        $this->line('   Host: ' . config('mail.mailers.smtp.host'));
        $this->line('   Port: ' . config('mail.mailers.smtp.port'));
        $this->line('   From: ' . config('mail.from.address'));
        $this->newLine();
        
        // Créer ou récupérer un abonné de test
        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $email],
            ['name' => 'Test User']
        );
        
        $this->info("📧 Envoi à : {$subscriber->email}");
        $this->newLine();
        
        try {
            $startTime = microtime(true);
            
            // Envoyer l'email
            Mail::to($subscriber->email)->send(new WelcomeNewsletter($subscriber));
            
            $duration = round((microtime(true) - $startTime), 2);
            
            $this->info("✅ Email envoyé avec succès en {$duration}s !");
            $this->newLine();
            
            $this->line('💡 Conseils :');
            $this->line('   1. Attendez 1-2 minutes');
            $this->line('   2. Vérifiez le dossier SPAM');
            $this->line('   3. Vérifiez l\'onglet Promotions (Gmail)');
            $this->newLine();
            
            $this->line('🔗 Lien de désinscription :');
            $this->line('   ' . route('newsletter.unsubscribe', $subscriber->unsubscribe_token));
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi !');
            $this->error($e->getMessage());
            $this->newLine();
            
            $this->line('🔧 Solutions :');
            $this->line('   1. Vérifiez votre .env (MAIL_*)');
            $this->line('   2. Utilisez un mot de passe d\'application Gmail');
            $this->line('   3. Vérifiez storage/logs/laravel.log');
            
            return Command::FAILURE;
        }
    }
}
