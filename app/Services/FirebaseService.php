<?php

namespace App\Services;

class FirebaseService
{
    protected $auth;
    protected $messaging;
    protected $database;
    
    public function __construct()
    {
        // Les services seront initialisés à la demande pour éviter les erreurs si les credentials ne sont pas encore configurés
    }

    /**
     * Obtenir le service d'authentification Firebase
     */
    public function auth()
    {
        if (!$this->auth) {
            $this->auth = app('firebase.auth');
        }
        return $this->auth;
    }

    /**
     * Obtenir le service de messaging Firebase
     */
    public function messaging()
    {
        if (!$this->messaging) {
            $this->messaging = app('firebase.messaging');
        }
        return $this->messaging;
    }

    /**
     * Obtenir le service de database Firebase
     */
    public function database()
    {
        if (!$this->database) {
            $this->database = app('firebase.database');
        }
        return $this->database;
    }

    /**
     * Vérifier si Firebase est correctement configuré
     */
    public function isConfigured(): bool
    {
        try {
            $projectId = config('firebase.project_id');
            $credentialsPath = config('firebase.credentials');
            
            if (!$projectId || !$credentialsPath) {
                return false;
            }

            $fullPath = base_path($credentialsPath);
            if (!file_exists($fullPath)) {
                return false;
            }

            // Test simple de connexion
            app('firebase.factory');
            
            return true;
        } catch (\Exception $e) {
            logger('Firebase configuration check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les informations de configuration
     */
    public function getConfigInfo(): array
    {
        return [
            'project_id' => config('firebase.project_id'),
            'credentials_path' => config('firebase.credentials'),
            'credentials_exists' => file_exists(base_path(config('firebase.credentials', ''))),
            'web_config' => config('firebase.web_config'),
            'is_configured' => $this->isConfigured(),
        ];
    }
}