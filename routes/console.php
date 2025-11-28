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
