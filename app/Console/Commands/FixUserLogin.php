<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FixUserLogin extends Command
{
    protected $signature = 'fix:user-login
        {email? : Email de l\'utilisateur (ou --all pour scanner toute la base)}
        {--all : Scanner tous les comptes (diagnostic global)}
        {--password= : Mot de passe actuel/désiré (teste puis répare si hash corrompu)}
        {--reset-to= : Nouveau mot de passe (reset forcé)}
        {--force : Réinitialise même si le hash est déjà valide}
        {--yes : Confirme le reset groupé sans demande}';

    protected $description = 'Diagnostique et répare les mots de passe double-hachés (401 au login avec de bons identifiants)';

    public function handle()
    {
        $email = $this->argument('email');
        $current = $this->option('password');
        $resetTo = $this->option('reset-to');
        $force = $this->option('force');
        $all = $this->option('all');

        if ($all) {
            return $this->scanAll($resetTo, $force);
        }

        if (!$email) {
            $this->error("Email requis (ou utilisez --all pour scanner la base entière).");
            return 1;
        }

        return $this->fixOne($email, $current, $resetTo, $force);
    }

    private function fixOne(string $email, ?string $current, ?string $resetTo, bool $force): int
    {
        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            $this->error("Utilisateur introuvable: {$email}");
            return 1;
        }

        $storedHash = $user->password;

        $this->info("Email: {$email}");
        $this->line("Hash stocké: " . substr($storedHash, 0, 30) . "... (longueur: " . strlen($storedHash) . ")");
        $this->line("Valide bcrypt: " . ($this->isValidBcrypt($storedHash) ? 'oui' : 'NON'));
        $this->newLine();

        if ($resetTo) {
            $this->resetPassword($email, $resetTo);
            return 0;
        }

        if ($current) {
            if (Hash::check($current, $storedHash) && !$force) {
                $this->info("✅ Login OK : le hash correspond au mot de passe fourni.");
                return 0;
            }

            $this->warn("⚠️  Le hash ne valide pas le mot de passe fourni.");
            $this->line("   Cause probable: double-hachage (bcrypt(bcrypt(mdp))) dû au cast 'hashed'");
            $this->line("   sur une ancienne version de Laravel (< 12).");
            $this->newLine();
            $this->info("🔧 Réparation: ré-hachage du mot de passe fourni (une seule fois)...");
            $this->resetPassword($email, $current);
            return 0;
        }

        $this->warn("Utilisez --password= pour tester/réparer, ou --reset-to= pour forcer un reset.");
        $this->line("   php artisan fix:user-login {$email} --password='monMotDePasse'");
        $this->line("   php artisan fix:user-login {$email} --reset-to='nouveauMotDePasse'");
        return 0;
    }

    private function scanAll(?string $resetTo, bool $force): int
    {
        $users = DB::table('users')
            ->select('id', 'email', 'password')
            ->whereNotNull('password')
            ->where('password', '!=', '')
            ->get();

        $total = $users->count();
        $valid = 0;
        $invalid = [];

        foreach ($users as $user) {
            if ($this->isValidBcrypt($user->password)) {
                $valid++;
            } else {
                $invalid[] = [$user->id, $user->email, substr($user->password, 0, 20)];
            }
        }

        $this->info("📊 Scan de la table users :");
        $this->line("   Total comptes avec mot de passe: {$total}");
        $this->line("   Hash bcrypt valides: {$valid}");
        $this->line("   Hash invalides: " . count($invalid));

        if (!empty($invalid)) {
            $this->newLine();
            $this->warn("⚠️  Hash invalides (jamais connectables) :");
            $this->table(['ID', 'Email', 'Hash (début)'], $invalid);
        }

        $this->newLine();
        $this->warn("Note: impossible de détecter un double-hachage sans le mot de passe");
        $this->line("   en clair (sel bcrypt aléatoire). Les hash bcrypt valides peuvent");
        $this->line("   être simple OU double-hachés.");

        if ($resetTo) {
            $this->newLine();
            $this->info("Reset groupé de TOUS les {$total} mots de passe vers: " . str_repeat('*', strlen($resetTo)));

            if ($force || $this->option('yes') || $this->confirm('Confirmer le reset groupé ?')) {
                $bar = $this->output->createProgressBar($total);
                $bar->start();

                foreach ($users as $user) {
                    DB::table('users')->where('id', $user->id)->update([
                        'password' => bcrypt($resetTo),
                    ]);
                    $bar->advance();
                }

                $bar->finish();
                $this->newLine(2);
                $this->info("✅ {$total} mots de passe réinitialisés avec succès.");
                $this->warn("⚠️  Communiquez le nouveau mot de passe à vos utilisateurs !");
                return 0;
            }

            $this->warn("Reset groupé annulé.");
            return 0;
        }

        $this->line("");
        $this->line("💡 Pour réparer :");
        $this->line("   - Un utilisateur : php artisan fix:user-login email@x.com --password='mdp'");
        $this->line("   - Reset groupé   : php artisan fix:user-login --all --reset-to='nouveauMdp'");
        return 0;
    }

    private function isValidBcrypt(string $hash): bool
    {
        return preg_match('/^\$2[aby]\$\d{2}\$[.\/A-Za-z0-9]{53}$/', $hash) === 1;
    }

    private function resetPassword(string $email, string $password): void
    {
        DB::table('users')->where('email', $email)->update([
            'password' => bcrypt($password),
        ]);

        $newHash = DB::table('users')->where('email', $email)->value('password');

        $this->info("✅ Mot de passe réinitialisé pour {$email}");
        $this->line("   Nouveau hash: " . substr($newHash, 0, 30) . "...");

        $check = Hash::check($password, $newHash);
        $this->line("   Vérification Hash::check: " . ($check ? 'OK ✅' : 'ÉCHEC ❌'));
    }
}
