<?php

namespace App\Http\Controllers\Api\Authenticity;

use App\Http\Controllers\Api\ApiController;
use App\Models\Item;
use App\Models\ProductAuthenticityCheck;
use App\Models\VerificationImage;
use App\Services\AuthenticityVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticityController extends ApiController
{
    protected $verificationService;

    public function __construct(AuthenticityVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * API: Vérifier si un item peut être vérifié
     */
    public function canVerify($itemId): JsonResponse
    {
        try {
            $item = Item::findOrFail($itemId);

            if ($item->user_id !== Auth::id()) {
                return $this->errorResponse('Non autorisé', 403);
            }

            $canVerify = $item->canRequestVerification();
            $hasExisting = $item->authenticityCheck &&
                          $item->authenticityCheck->status !== ProductAuthenticityCheck::STATUS_PENDING;

            return $this->successResponse([
                'can_verify' => $canVerify,
                'has_existing_request' => $hasExisting,
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'authenticity_verified' => $item->authenticity_verified,
                    'authenticity_badge_type' => $item->authenticity_badge_type
                ]
            ], 'Vérification de l\'éligibilité effectuée');

        } catch (\Exception $e) {
            return $this->errorResponse('Article non trouvé', 404);
        }
    }

    /**
     * API: Soumettre une demande de vérification
     */
    public function submit(Request $request, $itemId): JsonResponse
    {
        try {
            $item = Item::findOrFail($itemId);

            if ($item->user_id !== Auth::id()) {
                return $this->errorResponse('Non autorisé', 403);
            }

            $validator = Validator::make($request->all(), [
                'product_images.*' => 'required|image|mimes:jpeg,png,jpg|max:10240',
                'certificate' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5120',
                'receipt' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5120',
                'serial_number' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'purchase_location' => 'nullable|string|max:255',
                'additional_notes' => 'nullable|string|max:1000',
                'terms_accepted' => 'required|accepted'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Données invalides', 422, $validator->errors());
            }

            // Préparer les preuves
            $evidence = [
                'serial_number' => $request->serial_number,
                'purchase_date' => $request->purchase_date,
                'purchase_location' => $request->purchase_location,
                'additional_notes' => $request->additional_notes,
                'submitted_by' => Auth::user()->name,
                'submitted_at' => now()->toISOString()
            ];

            // Préparer les images
            $images = [];

            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $index => $file) {
                    $type = match($index) {
                        0 => VerificationImage::TYPE_PRODUCT_FRONT,
                        1 => VerificationImage::TYPE_PRODUCT_BACK,
                        2 => VerificationImage::TYPE_PRODUCT_SIDE,
                        default => VerificationImage::TYPE_PRODUCT_DETAIL
                    };

                    $images[] = [
                        'file' => $file,
                        'type' => $type
                    ];
                }
            }

            if ($request->hasFile('certificate')) {
                $images[] = [
                    'file' => $request->file('certificate'),
                    'type' => VerificationImage::TYPE_CERTIFICATE
                ];
            }

            if ($request->hasFile('receipt')) {
                $images[] = [
                    'file' => $request->file('receipt'),
                    'type' => VerificationImage::TYPE_RECEIPT
                ];
            }

            $check = $this->verificationService->submitForVerification($item, $evidence, $images);

            return $this->successResponse([
                'check_id' => $check->id,
                'verification_fee' => $check->verification_fee,
                'status' => $check->status,
                'payment_required' => !$check->payment_completed
            ], 'Demande de vérification soumise avec succès', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la soumission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Confirmer le paiement
     */
    public function confirmPayment($checkId): JsonResponse
    {
        try {
            $check = ProductAuthenticityCheck::findOrFail($checkId);

            if ($check->user_id !== Auth::id()) {
                return $this->errorResponse('Non autorisé', 403);
            }

            if ($check->payment_completed) {
                return $this->errorResponse('Paiement déjà effectué', 400);
            }

            $paymentService = app(\App\Services\VerificationPaymentService::class);
            $result = $paymentService->processVerificationPayment($check);

            if (!$result['success']) {
                return $this->errorResponse('Échec du paiement: ' . $result['message'], 400);
            }

            $this->verificationService->analyzeWithAI($check);

            return $this->successResponse([
                'check_id' => $check->id,
                'status' => $check->fresh()->status,
                'payment_completed' => true,
                'analysis_started' => true
            ], 'Paiement confirmé, analyse démarrée');

        } catch (\Exception $e) {
            return $this->errorResponse('Demande non trouvée', 404);
        }
    }

    /**
     * API: Obtenir le statut d'une vérification
     */
    public function status($itemId): JsonResponse
    {
        try {
            $item = Item::findOrFail($itemId);

            if ($item->user_id !== Auth::id()) {
                return $this->errorResponse('Non autorisé', 403);
            }

            $check = $item->authenticityCheck;

            if (!$check) {
                return $this->errorResponse('Aucune demande de vérification trouvée', 404);
            }

            $check->load(['verificationImages', 'auditLogs.performer', 'expert']);

            return $this->successResponse([
                'check' => [
                    'id' => $check->id,
                    'status' => $check->status,
                    'ai_score' => $check->ai_score,
                    'ai_result' => $check->ai_result,
                    'expert_notes' => $check->expert_notes,
                    'verification_fee' => $check->verification_fee,
                    'payment_completed' => $check->payment_completed,
                    'created_at' => $check->created_at->format('d/m/Y H:i'),
                    'completed_at' => $check->completed_at?->format('d/m/Y H:i')
                ],
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'authenticity_verified' => $item->authenticity_verified,
                    'authenticity_badge_type' => $item->authenticity_badge_type
                ],
                'images' => $check->verificationImages->map(fn($img) => [
                    'id' => $img->id,
                    'type' => $img->type,
                    'url' => $img->image_url
                ]),
                'audit_logs' => $check->auditLogs->map(fn($log) => [
                    'action' => $log->action,
                    'performer' => $log->performer->name ?? 'Système',
                    'created_at' => $log->created_at->format('d/m/Y H:i')
                ])
            ], 'Statut de vérification récupéré');

        } catch (\Exception $e) {
            return $this->errorResponse('Article non trouvé', 404);
        }
    }

    /**
     * API: Dashboard des vérifications
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 15);

            $checks = ProductAuthenticityCheck::where('user_id', $user->id)
                ->with(['item', 'expert'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $stats = [
                'total_requests' => ProductAuthenticityCheck::where('user_id', $user->id)->count(),
                'verified_items' => Item::where('user_id', $user->id)
                    ->where('authenticity_verified', true)
                    ->count(),
                'pending_verifications' => ProductAuthenticityCheck::where('user_id', $user->id)
                    ->whereIn('status', [
                        ProductAuthenticityCheck::STATUS_PENDING,
                        ProductAuthenticityCheck::STATUS_EXPERT_REVIEW
                    ])
                    ->count()
            ];

            return $this->paginatedResponse($checks, 'Dashboard récupéré avec succès')
                ->header('X-Stats', json_encode($stats));

        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors du chargement du dashboard: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Mettre à jour le statut (experts uniquement)
     */
    public function updateStatus(Request $request, $checkId): JsonResponse
    {
        try {
            $check = ProductAuthenticityCheck::findOrFail($checkId);

            // Défense en profondeur : seuls les experts certifiés peuvent certifier
            if (!Auth::user() || !Auth::user()->isExpert()) {
                return $this->errorResponse('Accès réservé aux experts certifiés.', 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:expert_approved,expert_rejected',
                'expert_notes' => 'required|string|max:1000'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Données invalides', 422, $validator->errors());
            }

            $approved = $request->status === 'expert_approved';

            $check->update([
                'status' => $request->status,
                'expert_notes' => $request->expert_notes,
                'expert_completed_at' => now()
            ]);

            $this->verificationService->finalizeVerification($check, $approved, 'expert_certified');

            return $this->successResponse([
                'check_id' => $check->id,
                'status' => $check->status,
                'approved' => $approved
            ], 'Statut mis à jour avec succès');

        } catch (\Exception $e) {
            return $this->errorResponse('Demande non trouvée', 404);
        }
    }
}
