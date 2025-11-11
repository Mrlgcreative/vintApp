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
}
