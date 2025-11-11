<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;
use App\Models\User;

class AssignVerificationToExpert extends Command
{
    protected $signature = 'expert:assign {verification_id} {expert_id}';
    protected $description = 'Assigne une vérification à un expert';

    public function handle()
    {
        $verificationId = $this->argument('verification_id');
        $expertId = $this->argument('expert_id');

        // Vérifier que la vérification existe
        $check = ProductAuthenticityCheck::find($verificationId);
        if (!$check) {
            $this->error("Vérification ID {$verificationId} non trouvée");
            return 1;
        }

        // Vérifier que l'expert existe
        $expert = User::whereHas('roles', function($q) {
            $q->where('slug', 'expert');
        })->find($expertId);
        
        if (!$expert) {
            $this->error("Expert ID {$expertId} non trouvé");
            return 1;
        }

        // Assigner la vérification
        $check->expert_id = $expertId;
        $check->expert_assigned_at = now();
        $check->save();

        $this->info("Vérification ID {$verificationId} assignée à l'expert {$expert->name} (ID: {$expertId})");
        $this->line("Statut: {$check->status}");
        $this->line("Assigné le: {$check->expert_assigned_at}");

        return 0;
    }
}