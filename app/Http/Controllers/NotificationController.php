<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiResponses;

    // ==================== API Methods ====================

    /**
     * Get all notifications for authenticated user
     */
    public function apiIndex(Request $request)
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
     * Get unread notifications count
     */
    public function apiUnreadCount(Request $request)
    {
        try {
            $count = Notification::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->count();

            return $this->successResponse([
                'unread_count' => $count
            ], 'Nombre de notifications non lues');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors du comptage', 500);
        }
    }

    /**
     * Get unread notifications
     */
    public function apiUnread(Request $request)
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
     * Mark notification as read
     */
    public function apiMarkAsRead(Request $request, $notificationId)
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
     * Mark all notifications as read
     */
    public function apiMarkAllAsRead(Request $request)
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
     * Delete a notification
     */
    public function apiDestroy(Request $request, $notificationId)
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
     * Delete all read notifications
     */
    public function apiDeleteRead(Request $request)
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
