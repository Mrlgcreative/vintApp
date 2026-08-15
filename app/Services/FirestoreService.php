<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirestoreService
{
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const API_BASE = 'https://firestore.googleapis.com/v1';
    private const SCOPE = 'https://www.googleapis.com/auth/datastore';

    private ?array $credentials = null;

    public function __construct()
    {
        $path = storage_path('firebase/serviceAccountKey.json');
        if (is_file($path)) {
            $this->credentials = json_decode((string) file_get_contents($path), true);
        }
    }

    /**
     * Token OAuth2 court de vie, obtenu depuis la clé de compte de service.
     * Mis en cache (fichier/redis) pour ne pas resigner un JWT à chaque appel.
     */
    private function accessToken(): ?string
    {
        if (!$this->credentials) {
            Log::error('Firestore: serviceAccountKey.json introuvable');
            return null;
        }

        $cached = Cache::get('firestore_access_token');
        if ($cached) {
            return $cached;
        }

        try {
            $now = time();
            $jwt = JWT::encode([
                'iss' => $this->credentials['client_email'],
                'scope' => self::SCOPE,
                'aud' => self::TOKEN_URL,
                'iat' => $now,
                'exp' => $now + 3600,
            ], $this->credentials['private_key'], 'RS256');

            $response = Http::asForm()->post(self::TOKEN_URL, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (!$response->successful()) {
                Log::error('Firestore: echec obtention token OAuth', [
                    'error' => $response->body(),
                ]);
                return null;
            }

            $token = $response->json('access_token');
            $expiresIn = (int) $response->json('expires_in', 3600);
            Cache::put('firestore_access_token', $token, $expiresIn - 120);

            return $token;
        } catch (\Exception $e) {
            Log::error('Firestore: echec obtention token OAuth', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Signaler un nouvel événement temps réel à l'utilisateur.
     *
     * Met à jour (PATCH) le document signal léger users/{userId}/realtime/last.
     * Le mobile écoute ce document (onSnapshot) et rafraîchit ses compteurs
     * dès que `seq` change — l'écran de notifications recharge ensuite via
     * l'API authentifiée. Aucune donnée sensible dans ce document.
     *
     * Transport REST pur (HTTP) : aucun besoin de l'extension gRPC, qui
     * n'est pas disponible sur l'hébergement mutualisé.
     *
     * @param int $userId
     * @return bool
     */
    public function signalUser(int $userId): bool
    {
        $token = $this->accessToken();
        if (!$token) {
            return false;
        }

        $projectId = $this->credentials['project_id'] ?? 'vintapp-e6fa7';
        $documentPath = sprintf(
            'projects/%s/databases/(default)/documents/users/%s/realtime/last',
            $projectId,
            $userId
        );
        $url = self::API_BASE . '/' . $documentPath
            . '?updateMask.fieldPaths=seq&updateMask.fieldPaths=at';

        $seq = (int) round(microtime(true) * 1000);

        try {
            $response = Http::withToken($token)->patch($url, [
                'fields' => [
                    'seq' => ['integerValue' => (string) $seq],
                    'at' => ['timestampValue' => now()->toIso8601String()],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Firestore: echec ecriture du signal', [
                    'error' => $response->body(),
                    'user_id' => $userId,
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Firestore: echec ecriture du signal', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);
            return false;
        }
    }
}
