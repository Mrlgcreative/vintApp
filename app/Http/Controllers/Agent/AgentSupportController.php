<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\SupportAgent;
use App\Models\SupportChat;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgentSupportController extends Controller
{
    /**
     * Dashboard de l'agent
     */
    public function dashboard()
    {
        $userId = Auth::id();
        $agent = SupportAgent::where('user_id', $userId)->firstOrFail();

        $stats = [
            'active' => SupportChat::where('admin_id', $userId)->whereNotIn('status', ['closed'])->count(),
            'waiting_reply' => SupportChat::where('admin_id', $userId)->where('status', 'in_progress')
                ->whereHas('messages', fn($q) => $q->where('is_admin', false)->where('is_read', false))->count(),
            'closed_today' => SupportChat::where('admin_id', $userId)->where('status', 'closed')
                ->whereDate('closed_at', today())->count(),
            'closed_week' => SupportChat::where('admin_id', $userId)->where('status', 'closed')
                ->where('closed_at', '>=', now()->startOfWeek())->count(),
            'unassigned' => SupportChat::whereNull('admin_id')->whereIn('status', ['open'])->count(),
            'max_chats' => $agent->max_chats,
        ];

        // Derniers tickets actifs
        $recentTickets = SupportChat::where('admin_id', $userId)
            ->whereNotIn('status', ['closed'])
            ->with(['user', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get();

        // Tickets non assignés urgents
        $urgentUnassigned = SupportChat::whereNull('admin_id')
            ->where('status', 'open')
            ->whereIn('priority', ['high', 'urgent'])
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        return view('agent.dashboard', compact('agent', 'stats', 'recentTickets', 'urgentUnassigned'));
    }

    /**
     * Mes tickets assignés
     */
    public function tickets(Request $request)
    {
        $userId = Auth::id();

        $query = SupportChat::where('admin_id', $userId)
            ->with(['user', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->orderBy('last_message_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Par défaut, ne pas montrer les fermés
            $query->whereNotIn('status', ['closed']);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $chats = $query->paginate(20)->withQueryString();

        return view('agent.tickets', compact('chats'));
    }

    /**
     * Tickets non assignés
     */
    public function unassigned(Request $request)
    {
        $query = SupportChat::whereNull('admin_id')
            ->where('status', 'open')
            ->with('user')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderBy('created_at', 'asc');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $chats = $query->paginate(20)->withQueryString();

        return view('agent.unassigned', compact('chats'));
    }

    /**
     * Voir une conversation
     */
    public function show(SupportChat $supportChat)
    {
        $userId = Auth::id();

        // L'agent ne peut voir que ses tickets ou les non-assignés
        if ($supportChat->admin_id && $supportChat->admin_id !== $userId) {
            abort(403, 'Ce ticket ne vous est pas assigné.');
        }

        $supportChat->load(['user', 'admin', 'messages.user']);

        // Marquer les messages utilisateur comme lus
        $supportChat->messages()
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('agent.show', compact('supportChat'));
    }

    /**
     * Répondre à un ticket
     */
    public function reply(Request $request, SupportChat $supportChat)
    {
        $userId = Auth::id();

        $request->validate([
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('support/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                    ];
                }
            }

            SupportMessage::createNew(
                $supportChat->id,
                $userId,
                $request->message,
                true,
                !empty($attachments) ? $attachments : null
            );

            // Auto-assigner si pas encore assigné
            if (!$supportChat->admin_id) {
                $supportChat->update([
                    'admin_id' => $userId,
                    'status' => 'in_progress',
                ]);
            } elseif ($supportChat->status === 'open') {
                $supportChat->update(['status' => 'in_progress']);
            } elseif ($supportChat->status === 'waiting_user') {
                $supportChat->update(['status' => 'in_progress']);
            }

            DB::commit();

            return redirect()->route('agent.show', $supportChat)
                ->with('success', 'Réponse envoyée.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent reply error', ['error' => $e->getMessage(), 'chat' => $supportChat->id]);
            return redirect()->back()->with('error', 'Erreur lors de l\'envoi.')->withInput();
        }
    }

    /**
     * Prendre un ticket (self-assign)
     */
    public function claim(SupportChat $supportChat)
    {
        $userId = Auth::id();

        if ($supportChat->admin_id) {
            return response()->json(['success' => false, 'message' => 'Ce ticket est déjà assigné.'], 422);
        }

        $agent = SupportAgent::where('user_id', $userId)->first();
        if ($agent && !$agent->canAcceptChats()) {
            return response()->json(['success' => false, 'message' => 'Vous avez atteint votre limite de tickets.'], 422);
        }

        try {
            $supportChat->assignToAdmin($userId);
            if ($agent) {
                $agent->update(['last_assigned_at' => now()]);
            }

            return response()->json(['success' => true, 'message' => 'Ticket pris en charge.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur.'], 500);
        }
    }

    /**
     * Changer le statut d'un ticket
     */
    public function updateStatus(Request $request, SupportChat $supportChat)
    {
        if ($supportChat->admin_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $request->validate([
            'status' => 'required|in:in_progress,waiting_user,closed',
        ]);

        $data = ['status' => $request->status];
        if ($request->status === 'closed') {
            $data['closed_at'] = now();
        }

        $supportChat->update($data);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour.']);
    }

    /**
     * Changer la priorité
     */
    public function updatePriority(Request $request, SupportChat $supportChat)
    {
        if ($supportChat->admin_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $supportChat->update(['priority' => $request->priority]);

        return response()->json(['success' => true, 'message' => 'Priorité mise à jour.']);
    }
}
