<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponses;

class RefundController extends Controller
{
    use ApiResponses;
    /**
     * Afficher la liste des demandes de remboursement
     */
    public function index(Request $request)
    {
        $query = Refund::with(['order.item', 'buyer', 'seller'])
                      ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('refund_type')) {
            $query->where('refund_type', $request->refund_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($orderQuery) use ($search) {
                    $orderQuery->where('order_number', 'like', "%{$search}%");
                })->orWhereHas('buyer', function ($buyerQuery) use ($search) {
                    $buyerQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Filtrer par vendeur si l'utilisateur n'est pas admin
        if (!$this->isAdmin()) {
            $query->where('seller_id', Auth::id());
        }

        // Statistiques (mêmes règles de visibilité que la liste)
        $statsQuery = Refund::query();
        if (!$this->isAdmin()) {
            $statsQuery->where('seller_id', Auth::id());
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'negotiation' => (clone $statsQuery)->where('status', 'negotiation')->count(),
            'approved' => (clone $statsQuery)->where('status', 'approved')->count(),
            'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
        ];

        $refunds = $query->paginate(10)->withQueryString();

        return view('admin.refunds.index', compact('refunds', 'stats'));
    }

    /**
     * Afficher les détails d'une demande de remboursement
     */
    public function show(Refund $refund)
    {
        // Vérifier que l'utilisateur peut voir cette demande
        if (!$this->canViewRefund($refund)) {
            abort(403, 'Accès non autorisé');
        }

        $refund->load(['order.item', 'buyer', 'seller']);

        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    private function isAdmin()
    {
        if (!Auth::check()) {
            return false;
        }
        
        $user = Auth::user();
        
        // Vérification via la table role_user comme dans les routes
        return \Illuminate\Support\Facades\DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'admin')
            ->exists();
    }

    /**
     * Vérifier si l'utilisateur peut voir cette demande de remboursement
     */
    private function canViewRefund(Refund $refund)
    {
        // Les admins peuvent tout voir
        if ($this->isAdmin()) {
            return true;
        }

        // Les vendeurs peuvent voir leurs propres demandes
        return $refund->seller_id === Auth::id();
    }

    // ==================== API Methods ====================

    /**
     * Get refunds via API
     */
    public function apiIndex(Request $request)
    {
        try {
            $query = Refund::with(['order.item', 'buyer', 'seller'])
                          ->orderBy('created_at', 'desc');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if (!$this->isAdmin()) {
                $query->where('seller_id', Auth::id());
            }

            $refunds = $query->paginate($request->per_page ?? 15);

            return $this->paginatedResponse($refunds, 'Demandes de remboursement');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération remboursements', 500);
        }
    }

    /**
     * Get refund details via API
     */
    public function apiShow(Refund $refund)
    {
        try {
            if (!$this->canViewRefund($refund)) {
                return $this->errorResponse('Accès non autorisé', 403);
            }

            $refund->load(['order.item', 'buyer', 'seller']);

            return $this->successResponse($refund, 'Détails remboursement');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération détails', 500);
        }
    }
}