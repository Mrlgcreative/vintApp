<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Configuration Firebase Factory
        $this->app->singleton('firebase.factory', function ($app) {
            $serviceAccountPath = config('firebase.credentials');
            
            if (!$serviceAccountPath) {
                throw new \Exception('Firebase credentials path not configured. Set FIREBASE_CREDENTIALS in .env');
            }

            $fullPath = base_path($serviceAccountPath);
            if (!file_exists($fullPath)) {
                throw new \Exception("Firebase credentials file not found at: {$fullPath}");
            }

            try {
                $factory = new \Kreait\Firebase\Factory();
                
                return $factory
                    ->withServiceAccount($fullPath)
                    ->withProjectId(config('firebase.project_id'));
            } catch (\Exception $e) {
                throw new \Exception('Firebase initialization failed: ' . $e->getMessage());
            }
        });

        // Firebase Auth Service
        $this->app->singleton('firebase.auth', function ($app) {
            try {
                return $app->make('firebase.factory')->createAuth();
            } catch (\Exception $e) {
                throw new \Exception('Firebase Auth initialization failed: ' . $e->getMessage());
            }
        });

        // Firebase Messaging Service
        $this->app->singleton('firebase.messaging', function ($app) {
            try {
                return $app->make('firebase.factory')->createMessaging();
            } catch (\Exception $e) {
                throw new \Exception('Firebase Messaging initialization failed: ' . $e->getMessage());
            }
        });

        // Firebase Database Service
        $this->app->singleton('firebase.database', function ($app) {
            try {
                return $app->make('firebase.factory')->createDatabase();
            } catch (\Exception $e) {
                throw new \Exception('Firebase Database initialization failed: ' . $e->getMessage());
            }
        });

        // Firebase Storage Service
        $this->app->singleton('firebase.storage', function ($app) {
            try {
                return $app->make('firebase.factory')->createStorage();
            } catch (\Exception $e) {
                throw new \Exception('Firebase Storage initialization failed: ' . $e->getMessage());
            }
        });

        // Alias pour compatibilité
        $this->app->alias('firebase.factory', 'firebase');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publier la configuration si nécessaire
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/firebase.php' => config_path('firebase.php'),
            ], 'firebase-config');
        }
    }
}
