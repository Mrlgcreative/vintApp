<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
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

        $refunds = $query->paginate(10)->withQueryString();

        return view('admin.refunds.index', compact('refunds'));
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
}