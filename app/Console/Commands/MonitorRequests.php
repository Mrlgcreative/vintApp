<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitorer les requêtes pour détecter la source de la 404 pending:1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Monitoring des requêtes - Surveillez maintenant votre navigateur...');
        $this->info('📝 Logs écrits dans storage/logs/request-monitor.log');
        $this->info('🛑 Appuyez sur Ctrl+C pour arrêter');
        $this->newLine();

        // Créer un middleware temporaire pour capturer toutes les requêtes
        $logFile = storage_path('logs/request-monitor.log');
        file_put_contents($logFile, "=== SESSION DE MONITORING COMMENCÉE À " . now() . " ===\n", FILE_APPEND);

        // Surveiller les logs Laravel en temps réel
        $this->monitorLogs();
    }

    private function monitorLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $requestLogFile = storage_path('logs/request-monitor.log');
        
        if (!file_exists($logFile)) {
            $this->error('Fichier de log Laravel introuvable');
            return;
        }

        $handle = fopen($logFile, 'r');
        if (!$handle) {
            $this->error('Impossible d\'ouvrir le fichier de log');
            return;
        }

        // Aller à la fin du fichier
        fseek($handle, -1, SEEK_END);

        $this->line('👀 Surveillance active... Reproduisez l\'erreur dans votre navigateur');

        while (true) {
            $line = fgets($handle);
            if ($line !== false) {
                // Filtrer les lignes qui nous intéressent
                if (strpos($line, '404') !== false || 
                    strpos($line, 'pending') !== false ||
                    strpos($line, 'admin/wallets') !== false ||
                    strpos($line, 'GET') !== false) {
                    
                    $timestamp = now()->format('H:i:s');
                    $output = "[{$timestamp}] {$line}";
                    
                    $this->line($output);
                    file_put_contents($requestLogFile, $output, FILE_APPEND);
                }
            } else {
                usleep(100000); // Attendre 100ms
            }
        }

        fclose($handle);
    }
}
