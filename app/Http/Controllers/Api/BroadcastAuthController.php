<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastAuthController extends Controller
{
    /**
     * Authentifie un client mobile (Bearer token Sanctum) sur un canal privé Pusher.
     * POST /api/v1/broadcasting/auth
     */
    public function auth(Request $request)
    {
        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');

        if (!$channelName || !$socketId) {
            return response()->json(['error' => 'channel_name et socket_id requis'], 422);
        }

        $user = $request->user();

        // Seul le canal privé de l'utilisateur courant est autorisé.
        if (preg_match('/^private-user\.(\d+)$/', $channelName, $matches)) {
            if ((int) $matches[1] === (int) $user->id) {
                $payload = Broadcast::auth($request);
                if (is_array($payload)) {
                    return response()->json($payload);
                }
                return $payload;
            }
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json(['error' => 'Canal non autorisé'], 403);
    }
}
