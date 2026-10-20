<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==========================================
// 📅 TÂCHES PLANIFIÉES (CRON JOBS)
// ==========================================

/**
 * 💾 Backup automatique de la base de données
 * Tous les jours à 2h00 du matin
 */
Schedule::command('backup:run --only-db')
    ->dailyAt('02:00')
    ->name('backup-database')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::channel('business')->info('✅ Backup BDD réussi');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::channel('errors')->error('❌ Échec du backup BDD');
    });

/**
 * 🧹 Nettoyage des anciens backups
 * Tous les dimanches à 3h00 du matin
 */
Schedule::command('backup:clean')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->name('clean-old-backups')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::channel('business')->info('✅ Nettoyage backups réussi');
    });

/**
 * 🔄 Réinitialiser les métriques de monitoring
 * Tous les lundis à 00:00
 */
Schedule::call(function () {
    app(\App\Services\MonitoringService::class)->resetMetrics();
})->weekly()->mondays()->at('00:00')->name('reset-monitoring-metrics');

/**
 * 📊 Nettoyer les anciennes entrées Telescope
 * Tous les jours à 1h00 du matin (conservation 7 jours)
 */
Schedule::command('telescope:prune --hours=168')
    ->dailyAt('01:00')
    ->name('prune-telescope-data');

/**
 * 🗑️ Nettoyer le cache expiré
 * Toutes les heures
 */
Schedule::command('cache:prune-stale-tags')
    ->hourly()
    ->name('prune-cache');

/**
 * ⚙️ Traiter la file d'attente
 * Hébergement mutualisé : pas de worker permanent, on traite puis on s'arrête.
 * Sans cela les jobs s'accumulent dans `jobs` et les notifications ne partent jamais.
 */
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=50')
    ->everyMinute()
    ->withoutOverlapping()
    ->name('queue-worker');

/**
 * 🧹 Purger les lignes devenues inutiles (sessions expirées, jobs anciens)
 * Tous les jours à 4h00.
 */
Schedule::call(function () {
    \Illuminate\Support\Facades\DB::table('sessions')
        ->where('last_activity', '<', now()->subDay()->timestamp)
        ->delete();

    // Conversations de l'assistant IA : données personnelles, purge à 7 jours.
    \App\Models\AssistantMessage::where('created_at', '<', now()->subDays(\App\Models\AssistantMessage::RETENTION_DAYS))
        ->delete();

    \Illuminate\Support\Facades\DB::table('jobs')
        ->where('created_at', '<', now()->subDays(7))
        ->delete();

    \Illuminate\Support\Facades\DB::table('failed_jobs')
        ->where('failed_at', '<', now()->subDays(7))
        ->delete();

    \Illuminate\Support\Facades\DB::table('job_batches')
        ->whereNotNull('finished_at')
        ->where('finished_at', '<', now()->subDays(7))
        ->delete();
})->dailyAt('04:00')->name('purge-stale-rows');
