<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Api\ApiController;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Services\StorageSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends ApiController
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * API: Historique de paiements
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $payments = Transaction::where('user_id', $request->user()->id)
                ->latest()
                ->paginate($request->per_page ?? 15);

            return $this->paginatedResponse($payments, 'Historique de paiements récupéré');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération', 500);
        }
    }

    /**
     * API: Détails d'un paiement
     */
    public function show(Request $request, $transactionId): JsonResponse
    {
        try {
            $payment = Transaction::where('transaction_id', $transactionId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            return $this->successResponse($payment, 'Détails du paiement récupérés');
        } catch (\Exception $e) {
            return $this->errorResponse('Paiement introuvable', 404);
        }
    }

    /**
     * API: Initier un paiement
     */
    public function initiate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|in:orange_money,mpesa,airtel_money,africell,illicocash',
            'amount' => 'required|numeric|min:1|max:500000',
            'phone' => 'required|string|min:9|max:15',
            'purpose' => 'required|string',
            'currency' => 'nullable|in:USD,CDF'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $paymentData = [
                'amount' => $request->amount,
                'phone' => $request->phone,
                'purpose' => $request->purpose,
                'buyer_id' => $request->user()->id
            ];

            $methodName = 'payWith' . str_replace('_', '', ucwords($request->provider, '_'));

            if (!method_exists($this->paymentService, $methodName)) {
                return $this->errorResponse('Méthode de paiement non supportée', 400);
            }

            $result = $this->paymentService->{$methodName}($paymentData);

            if ($result['status'] === 'pending') {
                return $this->successResponse($result, 'Paiement initié avec succès');
            }

            return $this->errorResponse($result['message'] ?? 'Erreur lors du paiement', 400);
        } catch (\Exception $e) {
            Log::error('API Payment initiation error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erreur lors de l\'initiation du paiement', 500);
        }
    }

    /**
     * API: Demander un remboursement
     */
    public function requestRefund(Request $request, $orderId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:10|max:1000',
            'refund_type' => 'required|in:partial,full',
            'refund_amount' => 'nullable|numeric|min:0',
            'evidence_photos' => 'nullable|array|max:5',
            'evidence_photos.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $order = Order::findOrFail($orderId);

            if ($order->buyer_id !== $request->user()->id) {
                return $this->errorResponse('Non autorisé', 403);
            }

            if (!$this->isRefundEligible($order)) {
                return $this->errorResponse('Commande non éligible au remboursement', 400);
            }

            $evidencePhotos = [];
            if ($request->hasFile('evidence_photos')) {
                foreach ($request->file('evidence_photos') as $photo) {
                    $path = $photo->store('refund_evidence', 'public');
                    StorageSyncService::syncFile($path);
                    $evidencePhotos[] = $path;
                }
            }

            $refundAmount = $request->refund_type === 'full'
                ? $order->total_amount
                : min($request->refund_amount ?? $order->total_amount, $order->total_amount);

            $refund = Refund::create([
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'transaction_id' => $this->getTransactionIdForOrder($order),
                'refund_amount' => $refundAmount,
                'original_amount' => $order->total_amount,
                'currency' => $order->currency,
                'reason' => $request->reason,
                'refund_type' => $request->refund_type,
                'status' => 'pending',
                'evidence_photos' => json_encode($evidencePhotos),
                'requested_at' => now()
            ]);

            return $this->successResponse(
                $refund,
                'Demande de remboursement créée avec succès',
                201
            );
        } catch (\Exception $e) {
            Log::error('API Refund request error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erreur lors de la demande de remboursement', 500);
        }
    }

    /**
     * API: Statut d'un remboursement
     */
    public function refundStatus(Request $request, $refundId): JsonResponse
    {
        try {
            $refund = Refund::where('id', $refundId)
                ->where('buyer_id', $request->user()->id)
                ->with(['order'])
                ->firstOrFail();

            return $this->successResponse($refund, 'Statut du remboursement');
        } catch (\Exception $e) {
            return $this->errorResponse('Remboursement introuvable', 404);
        }
    }

    /**
     * API: Statistiques de paiement
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $stats = [
                'total_payments' => Transaction::where('user_id', $userId)->count(),
                'successful_payments' => Transaction::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->count(),
                'total_amount' => Transaction::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'pending_refunds' => Refund::where('buyer_id', $userId)
                    ->where('status', 'pending')
                    ->count(),
            ];

            return $this->successResponse($stats, 'Statistiques de paiement');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération', 500);
        }
    }

    /**
     * API: Initier un paiement MaishaPay
     */
    public function initiateMaishaPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'phone' => 'required|string|min:9|max:12',
            'currency' => 'sometimes|string|in:CDF,USD',
            'operator' => 'sometimes|string|in:VODACOM,AIRTEL,ORANGE,AFRICELL,vodacom,airtel,orange,africell',
            'purpose' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $maishaPay = new \App\Services\MaishaPay();

            if (!$maishaPay->isConfigured()) {
                Log::error('MaishaPay non configuré');
                return response()->json([
                    'success' => false,
                    'message' => 'Service de paiement non disponible',
                ], 503);
            }

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié',
                ], 401);
            }
            $buyerId = $user->id;

            // Générer un ID de transaction unique
            $transactionId = 'MP-' . strtoupper(\Illuminate\Support\Str::random(12)) . '-' . time();

            // Stocker le panier dans les métadonnées pour le callback
            $cartData = get_cart_array();
            $deliveryAddressId = session('maishapay_checkout.delivery_address_id');

            // Créer la transaction dans la base
            $transaction = Transaction::create([
                'user_id' => $buyerId,
                'buyer_id' => $buyerId,
                'transaction_id' => $transactionId,
                'transaction_ref' => $transactionId, // Pour le callback
                'amount' => $request->amount,
                'currency' => $request->input('currency', 'CDF'),
                'provider' => 'maishapay',
                'status' => 'pending',
                'purpose' => $request->input('purpose', 'Paiement VintApp'),
                'phone' => $request->phone,
                'metadata' => json_encode([
                    'operator' => $request->input('operator'),
                    'gateway' => 'maishapay',
                    'cart' => $cartData,
                    'delivery_address_id' => $deliveryAddressId,
                ]),
            ]);

            $result = $maishaPay->initiatePayment([
                'transaction_id' => $transactionId, // Utiliser notre ID
                'amount' => $request->amount,
                'phone' => $request->phone,
                'currency' => $request->input('currency', 'CDF'),
                'operator' => $request->input('operator'),
                'buyer_id' => $buyerId,
                'description' => $request->input('purpose', 'Paiement VintApp'),
            ]);

            if ($result['success']) {
                $maishapayRef = $result['status_reference'] ?? $result['maishapay_id'] ?? $result['transaction_id'];
                $transaction->update([
                    'transaction_ref' => $maishapayRef,
                    'description' => 'Ref: ' . $result['transaction_id'],
                    'metadata' => json_encode(array_merge(
                        json_decode($transaction->metadata ?? '{}', true),
                        [
                            'maishapay_id' => $result['maishapay_id'] ?? null,
                            'status_reference' => $result['status_reference'] ?? null,
                            'ref' => $result['transaction_id'],
                        ]
                    )),
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'transaction_id' => $transaction->id,
                    'reference' => $result['transaction_id'],
                    'message' => $result['message'],
                ]);
            }

            $transaction->update(['status' => 'failed']);

            Log::error('MaishaPay: echec initiation', [
                'result' => $result,
                'request' => $request->only(['amount', 'phone', 'currency', 'operator']),
            ]);

            return response()->json([
                'success' => false,
                'status' => 'failed',
                'message' => $result['message'] ?? 'Erreur lors du paiement',
                'transaction_id' => $transaction->id,
            ], 400);

        } catch (\Exception $e) {
            Log::error('MaishaPay Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du service de paiement',
            ], 500);
        }
    }

    /**
     * API: Statut d'un paiement MaishaPay
     */
    public function checkMaishaStatus(Request $request, $transactionId): JsonResponse
    {
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction introuvable',
            ], 404);
        }

        // Si déjà complété ou échoué, retourner le statut
        if (in_array($transaction->status, ['completed', 'failed'])) {
            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'transaction_id' => $transaction->id,
            ]);
        }

        // Sinon vérifier auprès de MaishaPay
        if ($transaction->transaction_ref) {
            $maishaPay = new \App\Services\MaishaPay();
            $result = $maishaPay->checkStatus($transaction->transaction_ref);

            if ($result['success'] && isset($result['status'])) {
                $newStatus = match (strtolower($result['status'])) {
                    'success', 'completed', 'successful' => 'completed',
                    'failed', 'declined', 'cancelled' => 'failed',
                    default => 'pending',
                };

                $previousStatus = $transaction->status;

                if ($newStatus !== $previousStatus) {
                    $transaction->update(['status' => $newStatus]);
                }

                if ($newStatus === 'completed' && $previousStatus !== 'completed') {
                    create_orders_from_transaction($transaction->fresh());
                }

                return response()->json([
                    'success' => true,
                    'status' => $newStatus,
                    'transaction_id' => $transaction->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'transaction_id' => $transaction->id,
        ]);
    }

    /**
     * Vérifier si une commande est éligible au remboursement
     */
    private function isRefundEligible($order)
    {
        // La commande doit être confirmée par l'acheteur (réception confirmée)
        if (!$order->confirmed_by_buyer_at) {
            Log::info('Refund not eligible: no buyer confirmation', ['order' => $order->order_number]);
            return false;
        }

        // Vérifier qu'il n'y a pas déjà une demande de remboursement
        if ($order->refunds()->exists()) {
            Log::info('Refund not eligible: refund already exists', ['order' => $order->order_number]);
            return false;
        }

        // Délai de 30 jours après confirmation de réception (étendu pour test)
        $daysSinceConfirmation = $order->confirmed_by_buyer_at->diffInDays(now());
        if ($daysSinceConfirmation > 30) {
            Log::info('Refund not eligible: too old', [
                'order' => $order->order_number,
                'days_since_confirmation' => $daysSinceConfirmation
            ]);
            return false;
        }

        return true;
    }

    /**
     * Récupérer l'ID de transaction pour une commande
     */
    private function getTransactionIdForOrder($order)
    {
        $transaction = Transaction::where('user_id', $order->buyer_id)
            ->whereDate('created_at', $order->paid_at->toDateString())
            ->where('status', 'completed')
            ->first();

        return $transaction ? $transaction->transaction_id : null;
    }
}
