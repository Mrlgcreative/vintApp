<?php

namespace App\Services\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envoi de notifications vers des tokens Expo (expo-notifications).
 *
 * Les tokens générés par expo-notifications (Expo[...] / ExponentPushToken[...])
 * ne peuvent pas être délivrés par FCM : ils doivent être envoyés via l'API
 * Expo Push (https://exp.host/--/api/v2/push/send).
 */
trait SendsExpoPush
{
    protected function isExpoToken(string $token): bool
    {
        return str_starts_with($token, 'Expo[') || str_starts_with($token, 'ExponentPushToken[');
    }

    protected function sendViaExpoPush(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            $payload = [
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'channelId' => 'default',
                'data' => $data,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->timeout(15)
                ->post('https://exp.host/--/api/v2/push/send', $payload);

            $result = $response->json();
            $ticket = is_array($result) && isset($result['data'][0]) && is_array($result['data'][0])
                ? $result['data'][0]
                : null;
            $ticketStatus = is_array($ticket) ? ($ticket['status'] ?? null) : null;

            if ($response->successful() && $ticketStatus && $ticketStatus !== 'error') {
                Log::info('Notification Expo Push envoyée', [
                    'token' => substr($token, 0, 24) . '...',
                    'title' => $title,
                ]);
                return true;
            }

            Log::error('Erreur envoi notification Expo Push', [
                'status' => $response->status(),
                'response' => $result,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception envoi notification Expo Push', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
