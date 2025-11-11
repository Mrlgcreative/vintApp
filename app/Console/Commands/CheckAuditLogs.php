<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;

class CheckAuditLogs extends Command
{
    protected $signature = 'expert:check-logs {verification_id}';
    protected $description = 'Analyser les audit logs pour trouver les arrays problématiques';

    public function handle()
    {
        $verificationId = $this->argument('verification_id');
        $check = ProductAuthenticityCheck::with('auditLogs')->find($verificationId);
        
        if (!$check) {
            $this->error("Vérification {$verificationId} non trouvée");
            return 1;
        }

        $this->info("=== AUDIT LOGS POUR VERIFICATION {$verificationId} ===");
        $this->line("Nombre de logs: " . $check->auditLogs->count());
        
        if ($check->auditLogs->count() == 0) {
            $this->warn("Aucun audit log trouvé");
            return 0;
        }
        
        foreach ($check->auditLogs as $log) {
            $this->line("\n--- LOG ID: {$log->id} ---");
            $this->line("Action: {$log->action}");
            $this->line("Details type: " . gettype($log->details));
            
            if ($log->details) {
                $details = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                
                if (is_array($details)) {
                    $this->line("Details (array avec " . count($details) . " éléments):");
                    foreach ($details as $key => $value) {
                        $valueType = gettype($value);
                        if (is_array($value)) {
                            $this->error("  ⚠️  {$key}: array - " . json_encode($value) . " [PROBLÉMATIQUE]");
                        } else {
                            $this->line("  ✓ {$key}: {$valueType} - " . $value);
                        }
                    }
                } else {
                    $this->line("Details: " . $log->details);
                }
            } else {
                $this->line("Aucun détail");
            }
        }

        return 0;
    }
}