<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Api\ApiController;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    /**
     * API: Notifications de l'utilisateur
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $notifications = Notification::where('user_id', $request->user()->id)
                ->latest()
                ->paginate($request->per_page ?? 20);

            return $this->paginatedResponse($notifications, 'Notifications récupérées avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des notifications', 500);
        }
    }

    /**
     * API: Nombre de notifications non lues
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $count = Notification::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->count();

            return $this->successResponse([
                'count' => $count
            ], 'Nombre de notifications non lues');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors du comptage', 500);
        }
    }

    /**
     * API: Notifications non lues
     */
    public function unread(Request $request): JsonResponse
    {
        try {
            $notifications = Notification::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->latest()
                ->paginate($request->per_page ?? 20);

            return $this->paginatedResponse($notifications, 'Notifications non lues récupérées');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération', 500);
        }
    }

    /**
     * API: Marquer comme lue
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $notification->update(['read_at' => now()]);

            return $this->successResponse($notification, 'Notification marquée comme lue');
        } catch (\Exception $e) {
            return $this->errorResponse('Notification introuvable', 404);
        }
    }

    /**
     * API: Marquer comme non lue
     */
    public function markAsUnread(Request $request, $notificationId): JsonResponse
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $notification->update(['read_at' => null]);

            return response()->json(['success' => true, 'message' => 'Notification marquée comme non lue']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Notification introuvable'], 404);
        }
    }

    /**
     * API: Marquer toutes comme lues
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $count = Notification::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return $this->successResponse([
                'marked_count' => $count
            ], 'Toutes les notifications marquées comme lues');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la mise à jour', 500);
        }
    }

    /**
     * API: Supprimer une notification
     */
    public function destroy(Request $request, $notificationId): JsonResponse
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $notification->delete();

            return $this->successResponse(null, 'Notification supprimée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Notification introuvable', 404);
        }
    }

    /**
     * API: Supprimer les notifications lues
     */
    public function deleteRead(Request $request): JsonResponse
    {
        try {
            $count = Notification::where('user_id', $request->user()->id)
                ->whereNotNull('read_at')
                ->delete();

            return $this->successResponse([
                'deleted_count' => $count
            ], 'Notifications lues supprimées');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la suppression', 500);
        }
    }
}
