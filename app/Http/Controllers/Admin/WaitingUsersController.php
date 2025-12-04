<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserWaiting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponses;

class WaitingUsersController extends Controller
{
    use ApiResponses;
    /**
     * Liste des utilisateurs en attente
     */
    public function index(Request $request)
    {
        $query = UserWaiting::query()->with('convertedUser');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $waitingUsers = $query->paginate(20);

        // Statistiques
        $stats = [
            'total' => UserWaiting::count(),
            'pending' => UserWaiting::pending()->count(),
            'confirmed' => UserWaiting::confirmed()->count(),
            'approved' => UserWaiting::approved()->count(),
            'rejected' => UserWaiting::rejected()->count(),
            'converted' => UserWaiting::converted()->count(),
            'today' => UserWaiting::whereDate('created_at', today())->count(),
            'this_week' => UserWaiting::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => UserWaiting::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.waiting-users.index', compact('waitingUsers', 'stats'));
    }

    /**
     * Voir les détails d'une pré-inscription
     */
    public function show(UserWaiting $waitingUser)
    {
        $waitingUser->load('convertedUser');
        return view('admin.waiting-users.show', compact('waitingUser'));
    }

    /**
     * Approuver une pré-inscription
     */
    public function approve(Request $request, UserWaiting $waitingUser)
    {
        try {
            $notes = $request->input('notes');
            $waitingUser->approve($notes);

            Log::info("Admin approuvé pré-inscription: {$waitingUser->email}");

            return redirect()->back()
                ->with('success', "Pré-inscription approuvée ! Email de notification envoyé à {$waitingUser->email}");

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'approbation: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Erreur lors de l\'approbation.');
        }
    }

    /**
     * Rejeter une pré-inscription
     */
    public function reject(Request $request, UserWaiting $waitingUser)
    {
        try {
            $reason = $request->input('reason');
            $waitingUser->reject($reason);

            Log::info("Admin rejeté pré-inscription: {$waitingUser->email}");

            return redirect()->back()
                ->with('warning', "Pré-inscription rejetée pour {$waitingUser->email}");

        } catch (\Exception $e) {
            Log::error("Erreur lors du rejet: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Erreur lors du rejet.');
        }
    }

    /**
     * Actions en masse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users_waiting,id',
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        try {
            DB::beginTransaction();

            $users = UserWaiting::whereIn('id', $userIds)->get();

            foreach ($users as $user) {
                switch ($action) {
                    case 'approve':
                        $user->approve();
                        break;
                    case 'reject':
                        $user->reject('Rejet en masse');
                        break;
                    case 'delete':
                        $user->delete();
                        break;
                }
            }

            DB::commit();

            $count = count($userIds);
            $message = match($action) {
                'approve' => "{$count} pré-inscription(s) approuvée(s)",
                'reject' => "{$count} pré-inscription(s) rejetée(s)",
                'delete' => "{$count} pré-inscription(s) supprimée(s)",
            };

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur action en masse: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Erreur lors de l\'action en masse.');
        }
    }

    /**
     * Renvoyer l'email de confirmation
     */
    public function resendConfirmation(UserWaiting $waitingUser)
    {
        try {
            $waitingUser->sendConfirmationEmail();

            return redirect()->back()
                ->with('success', 'Email de confirmation renvoyé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur renvoi email: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Erreur lors de l\'envoi de l\'email.');
        }
    }

    /**
     * Supprimer une pré-inscription
     */
    public function destroy(UserWaiting $waitingUser)
    {
        try {
            $email = $waitingUser->email;
            $waitingUser->delete();

            Log::info("Admin supprimé pré-inscription: {$email}");

            return redirect()->route('admin.waiting-users.index')
                ->with('success', "Pré-inscription de {$email} supprimée.");

        } catch (\Exception $e) {
            Log::error("Erreur suppression: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Export CSV
     */
    public function export(Request $request)
    {
        $query = UserWaiting::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $waitingUsers = $query->orderBy('created_at', 'desc')->get();

        $filename = 'pre-inscriptions-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($waitingUsers) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Nom', 'Email', 'Téléphone', 'Pays', 'Statut', 
                'Date inscription', 'Email confirmé', 'Approuvé le', 'Jours d\'attente'
            ]);

            // Données
            foreach ($waitingUsers as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? '-',
                    $user->country,
                    $user->status,
                    $user->created_at->format('Y-m-d H:i'),
                    $user->email_confirmed_at ? $user->email_confirmed_at->format('Y-m-d H:i') : '-',
                    $user->approved_at ? $user->approved_at->format('Y-m-d H:i') : '-',
                    $user->waiting_days,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== API Methods ====================

    /**
     * Get waiting users via API
     */
    public function apiIndex(Request $request)
    {
        try {
            $query = UserWaiting::query()->with('convertedUser');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $waitingUsers = $query->paginate($request->per_page ?? 20);

            return $this->paginatedResponse($waitingUsers, 'Utilisateurs en attente');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération', 500);
        }
    }

    /**
     * Get stats via API
     */
    public function apiStats()
    {
        try {
            $stats = [
                'total' => UserWaiting::count(),
                'pending' => UserWaiting::pending()->count(),
                'confirmed' => UserWaiting::confirmed()->count(),
                'approved' => UserWaiting::approved()->count(),
                'rejected' => UserWaiting::rejected()->count(),
                'converted' => UserWaiting::converted()->count(),
                'today' => UserWaiting::whereDate('created_at', today())->count(),
            ];

            return $this->successResponse($stats, 'Statistiques pré-inscriptions');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération stats', 500);
        }
    }

    /**
     * Approve waiting user via API
     */
    public function apiApprove(Request $request, UserWaiting $waitingUser)
    {
        try {
            $notes = $request->input('notes');
            $waitingUser->approve($notes);

            return $this->successResponse(null, 'Pré-inscription approuvée');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur approbation', 500);
        }
    }
}
