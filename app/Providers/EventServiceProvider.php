<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\UserRegistered;
use App\Events\UserRegisteredWithReferral;
use App\Events\OrderCompleted;
use App\Listeners\InitializeUserAffiliate;
use App\Listeners\HandleReferralBonus;
use App\Listeners\AwardOrderPoints;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegistered::class => [
            InitializeUserAffiliate::class,
        ],
        
        UserRegisteredWithReferral::class => [
            HandleReferralBonus::class,
        ],
        
        OrderCompleted::class => [
            AwardOrderPoints::class,
        ],
        
        // Événements Laravel standard
        \Illuminate\Auth\Events\Registered::class => [
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        ],
        
        \Illuminate\Auth\Events\Verified::class => [
            // Activer les parrainages quand l'email est vérifié
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        // Événement personnalisé pour la vérification d'email
        Event::listen(\Illuminate\Auth\Events\Verified::class, function ($event) {
            $user = $event->user;
            
            // Activer le parrainage si applicable
            if ($user->referredBy && $user->referredBy->status === 'pending') {
                $user->activateReferral();
                
                // Vérifier les conditions de completion
                app(\App\Services\AffiliateService::class)->checkReferralCompletion($user);
            }
        });

        // Événement pour connexion quotidienne (attribution de points)
        Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            $user = $event->user;
            
            // Attribuer des points de connexion quotidienne
            app(\App\Services\AffiliateService::class)->awardPoints($user, 'daily_login');
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}