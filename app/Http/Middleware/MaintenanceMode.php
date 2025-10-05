<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le fichier de maintenance existe
        $maintenanceFile = storage_path('framework/maintenance.json');
        
        if (file_exists($maintenanceFile)) {
            $maintenanceData = json_decode(file_get_contents($maintenanceFile), true);
            
            // Vérifier si les données sont valides
            if (!is_array($maintenanceData)) {
                $maintenanceData = [];
            }
            
            // Permettre l'accès aux admins (ajustez selon votre système d'authentification)
            if (Auth::check() && (Auth::user()->is_admin ?? false)) {
                return $next($request);
            }
            
            // Permettre l'accès aux routes admin pour que les admins puissent se connecter
            if ($request->is('admin*') || $request->is('login*') || $request->is('api*')) {
                return $next($request);
            }
            
            // Définir les données par défaut si elles sont manquantes
            $defaultData = [
                'enabled' => true,
                'message' => 'Nous effectuons actuellement des travaux de maintenance sur le site.',
                'estimated_time' => null,
                'enabled_at' => now()->toISOString(),
                'contact_email' => config('mail.from.address', 'support@vintapp.com')
            ];
            
            $maintenanceData = array_merge($defaultData, $maintenanceData);
            
            // Afficher la page de maintenance
            return response()->view('maintenance', $maintenanceData, 503);
        }
        
        return $next($request);
    }
    
    /**
     * Activer le mode maintenance
     */
    public static function enable($message = null, $estimatedTime = null)
    {
        $contactEmail = 'support@vintapp.com';
        
        // Essayer d'obtenir l'email de config si possible
        try {
            if (function_exists('config')) {
                $contactEmail = config('mail.from.address', 'support@vintapp.com');
            }
        } catch (\Exception $e) {
            // Utiliser la valeur par défaut en cas d'erreur
        }
        
        $maintenanceData = [
            'enabled' => true,
            'message' => $message ?: 'Nous effectuons actuellement des travaux de maintenance sur le site.',
            'estimated_time' => $estimatedTime,
            'enabled_at' => date('c'), // Format ISO 8601
            'contact_email' => $contactEmail
        ];
        
        $maintenanceFile = storage_path('framework/maintenance.json');
        
        // Créer le répertoire s'il n'existe pas
        if (!file_exists(dirname($maintenanceFile))) {
            mkdir(dirname($maintenanceFile), 0755, true);
        }
        
        file_put_contents($maintenanceFile, json_encode($maintenanceData, JSON_PRETTY_PRINT));
        
        return true;
    }
    
    /**
     * Désactiver le mode maintenance
     */
    public static function disable()
    {
        $maintenanceFile = storage_path('framework/maintenance.json');
        
        if (file_exists($maintenanceFile)) {
            unlink($maintenanceFile);
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifier si le mode maintenance est actif
     */
    public static function isEnabled()
    {
        return file_exists(storage_path('framework/maintenance.json'));
    }
}
