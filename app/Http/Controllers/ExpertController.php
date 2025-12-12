<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductAuthenticityCheck;
use App\Models\VerificationImage;
use App\Models\AuthenticityAuditLog;
use App\Services\AuthenticityVerificationService;
use Illuminate\Support\Facades\Validator;


class ExpertController extends Controller
{
    protected $verificationService;

    public function __construct(AuthenticityVerificationService $verificationService)
    {
        
        $this->verificationService = $verificationService;
    }

    /**
     * Dashboard principal de l'expert
     */
    public function dashboard()
    {
        $expert = Auth::user();
        $expertProfile = $expert->expertProfile;

        // Statistiques de l'expert
        $stats = [
            'pending_assignments' => ProductAuthenticityCheck::where('expert_id', $expert->id)
                ->where('status', ProductAuthenticityCheck::STATUS_EXPERT_REVIEW)
                ->count(),
            'completed_today' => ProductAuthenticityCheck::where('expert_id', $expert->id)
                ->whereDate('expert_completed_at', today())
                ->count(),
            'total_verified' => ProductAuthenticityCheck::where('expert_id', $expert->id)
                ->whereIn('status', [ProductAuthenticityCheck::STATUS_EXPERT_APPROVED, ProductAuthenticityCheck::STATUS_EXPERT_REJECTED])
                ->count(),
            'approval_rate' => $expertProfile->approval_rate ?? 0,
        ];

        // Vérifications assignées (en attente)
        $pendingChecks = ProductAuthenticityCheck::where('expert_id', $expert->id)
            ->where('status', ProductAuthenticityCheck::STATUS_EXPERT_REVIEW)
            ->with(['item.category', 'item.brand', 'vendor', 'verificationImages'])
            ->orderBy('expert_assigned_at', 'asc')
            ->get();

        // Vérifications récentes
        $recentChecks = ProductAuthenticityCheck::where('expert_id', $expert->id)
            ->whereIn('status', [ProductAuthenticityCheck::STATUS_EXPERT_APPROVED, ProductAuthenticityCheck::STATUS_EXPERT_REJECTED])
            ->with(['item.category', 'item.brand', 'vendor'])
            ->orderBy('expert_completed_at', 'desc')
            ->limit(10)
            ->get();

        return view('expert.dashboard', compact('stats', 'pendingChecks', 'recentChecks', 'expertProfile'));
    }

    /**
     * Liste de toutes les vérifications pour l'expert
     */
    public function verifications(Request $request)
    {
        $expert = Auth::user();
        
        $query = ProductAuthenticityCheck::where('expert_id', $expert->id)
            ->with(['item.category', 'item.brand', 'vendor']);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->whereHas('item.category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $verifications = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('expert.verifications.index', compact('verifications'));
    }

    /**
     * Détails d'une vérification pour examen expert
     */
    public function show(ProductAuthenticityCheck $check)
    {
        $expert = Auth::user();

        // Vérifier que cette vérification est assignée à cet expert
        if ($check->expert_id !== $expert->id) {
            abort(403, 'Cette vérification n\'est pas assignée à votre compte.');
        }

        // Charger les relations nécessaires
        $check->load([
            'item.category',
            'item.brand', 
            'item.user',
            'vendor',
            'verificationImages',
            'auditLogs.performer'
        ]);

        return view('expert.verifications.show', compact('check'));
    }

    /**
     * Test simple pour debug
     */
    public function testShow(ProductAuthenticityCheck $check)
    {
        // Charger les relations nécessaires
        $check->load([
            'item.category',
            'item.brand', 
            'item.user',
            'vendor',
            'verificationImages',
            'auditLogs.performer'
        ]);

        return view('expert.test-show', compact('check'));
    }

    /**
     * Commencer l'examen d'une vérification
     */
    public function startReview(ProductAuthenticityCheck $check)
    {
        $expert = Auth::user();

        if ($check->expert_id !== $expert->id) {
            abort(403);
        }

        if ($check->status !== ProductAuthenticityCheck::STATUS_EXPERT_REVIEW) {
            return redirect()->back()->with('error', 'Cette vérification ne peut pas être examinée.');
        }

        // Log du début d'examen
        AuthenticityAuditLog::create([
            'authenticity_check_id' => $check->id,
            'action' => AuthenticityAuditLog::ACTION_EXPERT_REVIEW_STARTED,
            'performed_by' => $expert->id,
            'details' => ['started_at' => now()]
        ]);

        return redirect()->route('expert.verifications.show', $check)
            ->with('success', 'Examen démarré. Vous pouvez maintenant analyser cette demande.');
    }

    /**
     * Finaliser la décision de l'expert
     */
    public function finalize(Request $request, ProductAuthenticityCheck $check)
    {
        $expert = Auth::user();

        // Vérifications de sécurité
        if ($check->expert_id !== $expert->id) {
            abort(403);
        }

        if ($check->status !== ProductAuthenticityCheck::STATUS_EXPERT_REVIEW) {
            return redirect()->back()->with('error', 'Cette vérification ne peut plus être modifiée.');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:approve,reject',
            'expert_notes' => 'required|string|min:10|max:1000',
            'confidence_level' => 'required|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $approved = $request->decision === 'approve';
        
        // Mettre à jour la vérification
        $check->update([
            'status' => $approved ? ProductAuthenticityCheck::STATUS_EXPERT_APPROVED : ProductAuthenticityCheck::STATUS_EXPERT_REJECTED,
            'expert_notes' => $request->expert_notes,
            'expert_completed_at' => now()
        ]);

        // Finaliser avec le service
        $this->verificationService->finalizeVerification($check, $approved, 'expert_certified');

        // Mettre à jour les stats de l'expert
        $expertProfile = $expert->expertProfile;
        $expertProfile->increment('verification_count');
        
        // Recalculer le taux d'approbation
        $totalVerified = ProductAuthenticityCheck::where('expert_id', $expert->id)
            ->whereIn('status', [ProductAuthenticityCheck::STATUS_EXPERT_APPROVED, ProductAuthenticityCheck::STATUS_EXPERT_REJECTED])
            ->count();
        
        $approvedCount = ProductAuthenticityCheck::where('expert_id', $expert->id)
            ->where('status', ProductAuthenticityCheck::STATUS_EXPERT_APPROVED)
            ->count();
        
        $expertProfile->update([
            'approval_rate' => $totalVerified > 0 ? ($approvedCount / $totalVerified) * 100 : 0
        ]);

        $message = $approved ? 'Produit approuvé avec succès !' : 'Produit rejeté. Le vendeur a été notifié.';

        return redirect()->route('expert.dashboard')
            ->with('success', $message);
    }

    /**
     * Profil et paramètres de l'expert
     */
    public function profile()
    {
        $expert = Auth::user();
        $expertProfile = $expert->expertProfile;

        // Statistiques détaillées
        $detailedStats = [
            'total_assigned' => ProductAuthenticityCheck::where('expert_id', $expert->id)->count(),
            'pending' => ProductAuthenticityCheck::where('expert_id', $expert->id)
                ->where('status', ProductAuthenticityCheck::STATUS_EXPERT_REVIEW)->count(),
            'approved' => ProductAuthenticityCheck::where('expert_id', $expert->id)
                ->where('status', ProductAuthenticityCheck::STATUS_EXPERT_APPROVED)->count(),
            'rejected' => ProductAuthenticityCheck::where('expert_id', $expert->id)
                ->where('status', ProductAuthenticityCheck::STATUS_EXPERT_REJECTED)->count(),
            'avg_review_time' => $this->calculateAverageReviewTime($expert->id),
            'categories_expertise' => $expertProfile->specialties ?? [],
        ];

        return view('expert.profile', compact('expert', 'expertProfile', 'detailedStats'));
    }

    /**
     * Mettre à jour le profil expert
     */
    public function updateProfile(Request $request)
    {
        $expert = Auth::user();
        $expertProfile = $expert->expertProfile;

        $validator = Validator::make($request->all(), [
            'bio' => 'nullable|string|max:500',
            'credentials' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $expertProfile->update([
            'bio' => $request->bio,
            'credentials' => $request->credentials
        ]);

        return redirect()->route('expert.profile')
            ->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Liste des articles en attente de vérification (pas encore assignés à un expert)
     */
    public function pendingItems(Request $request)
    {
        $expert = Auth::user();
        $expertProfile = $expert->expertProfile;
        
        // Récupérer les articles en attente qui correspondent aux spécialités de l'expert
        $query = \App\Models\Item::where('status', 'pending_verification')
            ->where('verification_status', 'pending')
            ->whereNull('verified_at')
            ->with(['user', 'category', 'brand', 'orders', 'reviews']);

        // Filtrer par catégories d'expertise si l'expert a des spécialités
        if ($expertProfile && !empty($expertProfile->specialties)) {
            // Les specialties sont stockées comme des slugs, on cherche par slug de catégorie
            $query->whereHas('category', function($q) use ($expertProfile) {
                $q->whereIn('slug', $expertProfile->specialties);
            });
        }
        // Si l'expert n'a pas de spécialités, il voit tous les articles en attente
        // (c'est un admin/expert généraliste)

        // Filtres
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Récupérer les catégories pour le filtre
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('expert.items.pending', compact('items', 'expertProfile', 'categories'));
    }

    /**
     * Afficher les détails d'un article pour vérification
     */
    public function showItemForVerification(\App\Models\Item $item)
    {
        $expert = Auth::user();
        $expertProfile = $expert->expertProfile;

        // Vérifier que l'article est en attente et dans les spécialités de l'expert
        if ($item->status !== 'pending_verification' || $item->verification_status !== 'pending' || $item->verified_at) {
            return redirect()->route('expert.pending-items')
                ->with('error', 'Cet article n\'est pas en attente de vérification.');
        }

        return view('expert.items.show-for-verification', compact('item', 'expertProfile'));
    }

    /**
     * Soumettre une vérification pour un article
     */
    public function submitItemVerification(Request $request, \App\Models\Item $item)
    {
        $expert = Auth::user();

        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:decision,rejected|string|max:500'
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $notificationService = new \App\Services\ExpertNotificationService();

            if ($validated['decision'] === 'approved') {
                $item->update([
                    'verification_status' => 'approved',
                    'verified_at' => now(),
                    'verified_by' => $expert->id
                ]);

                \Illuminate\Support\Facades\Log::info("Article approuvé par expert", [
                    'item_id' => $item->id,
                    'expert_id' => $expert->id
                ]);

                // Notifier le vendeur
                $notificationService->notifyItemApproved($item, $item->user);

            } else {
                $item->update([
                    'verification_status' => 'rejected',
                    'verified_at' => now(),
                    'verified_by' => $expert->id,
                    'rejection_reason' => $validated['rejection_reason'] ?? null
                ]);

                \Illuminate\Support\Facades\Log::info("Article rejeté par expert", [
                    'item_id' => $item->id,
                    'expert_id' => $expert->id,
                    'reason' => $validated['rejection_reason'] ?? null
                ]);

                // Notifier le vendeur du rejet
                $notificationService->notifyItemRejected($item, $item->user, $validated['rejection_reason'] ?? null);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('expert.items.pending')
                ->with('success', 'Vérification soumise avec succès.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Erreur lors de la vérification", [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Calculer le temps moyen d'examen
     */
    private function calculateAverageReviewTime(int $expertId): float
    {
        $completedChecks = ProductAuthenticityCheck::where('expert_id', $expertId)
            ->whereNotNull('expert_assigned_at')
            ->whereNotNull('expert_completed_at')
            ->get();

        if ($completedChecks->isEmpty()) {
            return 0;
        }

        $totalMinutes = $completedChecks->sum(function($check) {
            return $check->expert_assigned_at->diffInMinutes($check->expert_completed_at);
        });

        return round($totalMinutes / $completedChecks->count(), 1);
    }

    /**
     * Obtenir les notifications non lues de l'expert
     */
    public function getNotifications(Request $request)
    {
        $expert = Auth::user();
        $unreadCount = \App\Models\ExpertNotification::where('user_id', $expert->id)
            ->unread()
            ->count();

        $notifications = \App\Models\ExpertNotification::where('user_id', $expert->id)
            ->recent()
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon ?? 'fa-bell',
                    'action_url' => $notification->action_url,
                    'read' => $notification->read,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'timestamp' => $notification->created_at->toIso8601String()
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $notification = \App\Models\ExpertNotification::findOrFail($notificationId);
        
        // Vérifier que c'est la notification de l'expert
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        \App\Models\ExpertNotification::where('user_id', Auth::id())
            ->unread()
            ->update(['read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Enregistrer le token FCM pour les notifications push
     */
    public function registerFCMToken(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = Auth::user();
        $user->fcm_token = $validated['fcm_token'];
        $user->save();

        \Illuminate\Support\Facades\Log::info('Expert FCM token registered', [
            'user_id' => $user->id
        ]);

        return response()->json(['success' => true]);
    }
}

