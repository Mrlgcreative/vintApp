<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserWaiting;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ConvertWaitingUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:convert-waiting 
                            {--all : Convertir tous les utilisateurs approuvés}
                            {--id=* : IDs spécifiques à convertir}
                            {--limit= : Nombre maximum à convertir}
                            {--notify : Envoyer un email avec les credentials}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convertir les pré-inscriptions approuvées en comptes utilisateurs réels';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Démarrage de la conversion des utilisateurs en attente...');
        $this->newLine();

        // Récupérer les utilisateurs à convertir
        $query = UserWaiting::approved()->notConverted();

        if ($this->option('id')) {
            $ids = $this->option('id');
            $query->whereIn('id', $ids);
            $this->info("Mode: Conversion des IDs spécifiques: " . implode(', ', $ids));
        } elseif ($this->option('all')) {
            $this->info("Mode: Conversion de TOUS les utilisateurs approuvés");
        } else {
            $this->error("❌ Vous devez spécifier --all ou --id=X");
            return Command::FAILURE;
        }

        if ($this->option('limit')) {
            $limit = (int) $this->option('limit');
            $query->limit($limit);
            $this->info("Limite: {$limit} utilisateurs maximum");
        }

        $waitingUsers = $query->get();

        if ($waitingUsers->isEmpty()) {
            $this->warn('⚠️  Aucun utilisateur approuvé à convertir.');
            return Command::SUCCESS;
        }

        $this->info("📊 {$waitingUsers->count()} utilisateur(s) à convertir");
        $this->newLine();

        if (!$this->confirm('Voulez-vous continuer ?', true)) {
            $this->info('Opération annulée.');
            return Command::SUCCESS;
        }

        $this->newLine();

        // Barre de progression
        $progressBar = $this->output->createProgressBar($waitingUsers->count());
        $progressBar->start();

        $converted = 0;
        $failed = 0;
        $credentials = [];

        foreach ($waitingUsers as $waitingUser) {
            try {
                DB::beginTransaction();

                // Générer un mot de passe temporaire aléatoire
                $temporaryPassword = $this->generateTemporaryPassword();

                // Créer le compte utilisateur
                $user = User::create([
                    'name' => $waitingUser->name,
                    'email' => $waitingUser->email,
                    'phone' => $waitingUser->phone,
                    'password' => Hash::make($temporaryPassword),
                    'email_verified_at' => now(), // Auto-vérifié car email déjà confirmé
                    'is_active' => true,
                ]);

                // Créer les wallets par défaut (USD et CDF)
                Wallet::create([
                    'user_id' => $user->id,
                    'currency' => 'USD',
                    'balance' => 0,
                    'is_active' => true,
                ]);

                Wallet::create([
                    'user_id' => $user->id,
                    'currency' => 'CDF',
                    'balance' => 0,
                    'is_active' => true,
                ]);

                // Marquer comme converti
                $waitingUser->markAsConverted($user);

                // Envoyer l'email de bienvenue si demandé
                if ($this->option('notify')) {
                    $waitingUser->sendWelcomeEmail($temporaryPassword);
                }

                // Stocker les credentials pour affichage
                $credentials[] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $temporaryPassword,
                    'user_id' => $user->id,
                ];

                DB::commit();

                $converted++;
                Log::info("Utilisateur converti: {$waitingUser->email} -> User ID {$user->id}");

            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
                Log::error("Erreur conversion {$waitingUser->email}: {$e->getMessage()}");
                $this->newLine();
                $this->error("❌ Erreur pour {$waitingUser->email}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Résumé
        $this->info('✅ Conversion terminée !');
        $this->newLine();
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['✅ Convertis avec succès', $converted],
                ['❌ Échecs', $failed],
                ['📊 Total traités', $waitingUsers->count()],
            ]
        );

        // Afficher les credentials générés
        if (!empty($credentials)) {
            $this->newLine();
            $this->warn('🔐 CREDENTIALS TEMPORAIRES (à communiquer aux utilisateurs) :');
            $this->newLine();
            $this->table(
                ['Nom', 'Email', 'Mot de passe temporaire', 'User ID'],
                array_map(function($cred) {
                    return [
                        $cred['name'],
                        $cred['email'],
                        $cred['password'],
                        $cred['user_id'],
                    ];
                }, $credentials)
            );
            $this->newLine();
            $this->warn('⚠️  IMPORTANT: Sauvegardez ces credentials ou envoyez-les immédiatement !');
            $this->warn('    Les utilisateurs devront changer leur mot de passe à la première connexion.');
        }

        return Command::SUCCESS;
    }

    /**
     * Générer un mot de passe temporaire sécurisé
     */
    private function generateTemporaryPassword(): string
    {
        // Format: XXXXXX-999999 (6 lettres majuscules + tiret + 6 chiffres)
        $letters = strtoupper(Str::random(6));
        $numbers = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        return "{$letters}-{$numbers}";
    }
}
