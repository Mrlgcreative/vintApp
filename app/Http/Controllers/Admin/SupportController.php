<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportAgent;
use App\Models\SupportChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SupportService;
use App\Traits\ApiResponses;

class SupportController extends Controller
{
    use ApiResponses;

    public function __construct(
        private readonly SupportService $supportService
    ) {
    }
    /**
     * Afficher la liste des conversations de support
     */
    public function index(Request $request)
    {
        $query = SupportChat::with(['user', 'admin', 'lastMessage'])
            ->orderBy('last_message_at', 'desc');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('admin_id');
            } else {
                $query->where('admin_id', $request->assigned_to);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $chats = $query->paginate(20);

        // Statistiques pour le dashboard
        $stats = $this->supportService->getGlobalStats();

        // Agents support disponibles pour l'assignation
        $agents = SupportAgent::with('user')->active()->get();
        
        // Fallback: si pas d'agents configurés, utiliser les admins
        if ($agents->isEmpty()) {
            $admins = User::whereHas('roles', function($query) {
                $query->where('slug', 'admin');
            })->get();
            
            if ($admins->isEmpty() && Auth::check()) {
                $admins = collect([Auth::user()]);
            }
        } else {
            $admins = $agents->pluck('user');
        }

        return view('admin.support.index', compact('chats', 'stats', 'admins', 'agents'));
    }

    /**
     * Afficher une conversation de support
     */
    public function show(SupportChat $supportChat)
    {
        $supportChat->load(['user', 'admin', 'messages.user']);
        
        // Marquer les messages non lus de l'utilisateur comme lus
        $supportChat->messages()
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.support.show', compact('supportChat'));
    }

    /**
     * Répondre à une conversation
     */
    public function reply(Request $request, SupportChat $supportChat)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max par fichier
        ]);

        try {
            $this->supportService->replyToChat(
                $supportChat,
                Auth::id(),
                $request->message,
                true, // is_admin
                $request->file('attachments') ?? []
            );

            // TODO: Envoyer une notification à l'utilisateur

            return redirect()->route('admin.support.show', $supportChat)
                ->with('success', 'Réponse envoyée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la réponse support', [
                'error' => $e->getMessage(),
                'support_chat_id' => $supportChat->id,
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi de la réponse.')
                ->withInput();
        }
    }

    /**
     * Assigner une conversation à un admin
     */
    public function assign(Request $request, SupportChat $supportChat)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

        try {
            $this->supportService->assignChat($supportChat, $request->admin_id);

            return response()->json([
                'success' => true,
                'message' => 'Conversation assignée avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'assignation', [
                'error' => $e->getMessage(),
                'support_chat_id' => $supportChat->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'assignation.'
            ], 500);
        }
    }

    /**
     * Changer le statut d'une conversation
     */
    public function updateStatus(Request $request, SupportChat $supportChat)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,waiting_user,closed'
        ]);

        try {
            $data = ['status' => $request->status];
            
            if ($request->status === 'closed') {
                $data['closed_at'] = now();
            } elseif ($request->status === 'in_progress' && !$supportChat->admin_id) {
                $data['admin_id'] = Auth::id();
            }

            $supportChat->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès.',
                'status' => $supportChat->formatted_status
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du statut', [
                'error' => $e->getMessage(),
                'support_chat_id' => $supportChat->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut.'
            ], 500);
        }
    }

    /**
     * Changer la priorité d'une conversation
     */
    public function updatePriority(Request $request, SupportChat $supportChat)
    {
        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent'
        ]);

        try {
            $supportChat->update(['priority' => $request->priority]);

            return response()->json([
                'success' => true,
                'message' => 'Priorité mise à jour avec succès.',
                'priority' => $supportChat->formatted_priority
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la priorité', [
                'error' => $e->getMessage(),
                'support_chat_id' => $supportChat->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la priorité.'
            ], 500);
        }
    }

    /**
     * Fermer une conversation
     */
    public function close(SupportChat $supportChat)
    {
        try {
            $this->supportService->closeChat($supportChat);

            return response()->json([
                'success' => true,
                'message' => 'Conversation fermée avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la fermeture de la conversation', [
                'error' => $e->getMessage(),
                'support_chat_id' => $supportChat->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la fermeture de la conversation.'
            ], 500);
        }
    }

    /**
     * Rouvrir une conversation fermée
     */
    public function reopen(SupportChat $supportChat)
    {
        try {
            $supportChat->update([
                'status' => 'open',
                'closed_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation rouverte avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la réouverture de la conversation', [
                'error' => $e->getMessage(),
                'support_chat_id' => $supportChat->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réouverture de la conversation.'
            ], 500);
        }
    }

    /**
     * Statistiques détaillées du support
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', '30'); // 30 jours par défaut
        $startDate = now()->subDays($period);

        $stats = $this->supportService->getDetailedStats($startDate);

        return view('admin.support.stats', compact('stats', 'period'));
    }

    // ==================== Agents Support ====================

    /**
     * Page de gestion des agents support
     */
    public function agents()
    {
        $agents = SupportAgent::with('user')->get()->map(function ($agent) {
            $agent->active_chats = SupportChat::where('admin_id', $agent->user_id)
                ->whereNotIn('status', ['closed'])->count();
            $agent->total_resolved = SupportChat::where('admin_id', $agent->user_id)
                ->where('status', 'closed')->count();
            return $agent;
        });

        // Utilisateurs pouvant être ajoutés comme agents (pas encore agents)
        $existingAgentIds = $agents->pluck('user_id')->toArray();
        $availableUsers = User::whereNotIn('id', $existingAgentIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $categories = ['technical', 'account', 'payment', 'order', 'general'];

        return view('admin.support.agents', compact('agents', 'availableUsers', 'categories'));
    }

    /**
     * Ajouter un agent support
     */
    public function addAgent(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:support_agents,user_id',
            'max_chats' => 'nullable|integer|min:1|max:50',
            'specialties' => 'nullable|array',
            'specialties.*' => 'string|in:technical,account,payment,order,general',
        ]);

        try {
            $agent = SupportAgent::create([
                'user_id' => $request->user_id,
                'max_chats' => $request->max_chats ?? 10,
                'specialties' => $request->specialties ?? [],
                'is_active' => true,
            ]);

            // Ajouter le rôle support s'il ne l'a pas
            $user = User::find($request->user_id);
            $supportRole = \App\Models\Role::where('slug', 'support')->first();
            if ($supportRole && !$user->hasRole('support')) {
                $user->roles()->attach($supportRole->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Agent ajouté avec succès.',
                'agent' => $agent->load('user'),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur ajout agent support', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de l\'agent.',
            ], 500);
        }
    }

    /**
     * Supprimer un agent support
     */
    public function removeAgent(SupportAgent $agent)
    {
        try {
            // Retirer le rôle support
            $user = $agent->user;
            $supportRole = \App\Models\Role::where('slug', 'support')->first();
            if ($supportRole) {
                $user->roles()->detach($supportRole->id);
            }

            $agent->delete();

            return response()->json([
                'success' => true,
                'message' => 'Agent supprimé avec succès.',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur suppression agent', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression.',
            ], 500);
        }
    }

    /**
     * Activer/désactiver un agent
     */
    public function toggleAgent(SupportAgent $agent)
    {
        try {
            $agent->update(['is_active' => !$agent->is_active]);

            return response()->json([
                'success' => true,
                'message' => $agent->is_active ? 'Agent activé.' : 'Agent désactivé.',
                'is_active' => $agent->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de statut.',
            ], 500);
        }
    }

    /**
     * Mettre à jour les paramètres d'un agent
     */
    public function updateAgent(Request $request, SupportAgent $agent)
    {
        $request->validate([
            'max_chats' => 'nullable|integer|min:1|max:50',
            'specialties' => 'nullable|array',
            'specialties.*' => 'string|in:technical,account,payment,order,general',
        ]);

        try {
            $agent->update($request->only(['max_chats', 'specialties']));

            return response()->json([
                'success' => true,
                'message' => 'Agent mis à jour avec succès.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour.',
            ], 500);
        }
    }

    /**
     * Auto-assigner un ticket à l'agent le moins chargé
     */
    public function autoAssign(SupportChat $supportChat)
    {
        try {
            $agent = $this->supportService->autoAssignChat($supportChat);

            return response()->json([
                'success' => true,
                'message' => 'Ticket assigné à ' . $agent->user->name,
                'agent_name' => $agent->user->name,
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur auto-assign', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'auto-assignation.',
            ], 500);
        }
    }

    // ==================== API Methods ====================

    /**
     * Get support chats via API
     */
    public function apiIndex(Request $request)
    {
        try {
            $query = SupportChat::with(['user', 'admin', 'lastMessage'])
                ->orderBy('last_message_at', 'desc');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $chats = $query->paginate($request->per_page ?? 20);

            return $this->paginatedResponse($chats, 'Conversations support');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération conversations', 500);
        }
    }

    /**
     * Get chat details via API
     */
    public function apiShow(SupportChat $supportChat)
    {
        try {
            $supportChat->load(['user', 'admin', 'messages.user']);

            return $this->successResponse($supportChat, 'Détails conversation');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération détails', 500);
        }
    }

    /**
     * Get support stats via API
     */
    public function apiStats()
    {
        try {
            $stats = $this->supportService->getGlobalStats();

            return $this->successResponse($stats, 'Statistiques support');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération stats', 500);
        }
    }
}