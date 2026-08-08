<?php

namespace App\Http\Controllers\Api\VintPass;

use App\Http\Controllers\Api\ApiController;
use App\Models\Item;
use App\Models\VintPass;
use App\Services\VintPassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VintPassController extends ApiController
{
    protected VintPassService $vintPassService;

    public function __construct(VintPassService $vintPassService)
    {
        $this->vintPassService = $vintPassService;
    }

    /**
     * API: Vérifier un VintPass (public)
     */
    public function verify(string $shortCode, Request $request): JsonResponse
    {
        $result = $this->vintPassService->verifyByShortCode($shortCode);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'VintPass non trouvé',
            ], 404);
        }

        // Enregistrer le scan
        $vintPass = VintPass::where('short_code', $shortCode)->first();
        $vintPass->recordScan(
            Auth::user(),
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
            'valid' => $result['valid'],
            'data' => $result['vint_pass'],
        ]);
    }

    /**
     * API: Liste des VintPass de l'utilisateur
     */
    public function myPasses(Request $request): JsonResponse
    {
        $passes = VintPass::where('current_owner_id', Auth::id())
            ->with(['item'])
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $passes,
        ]);
    }

    /**
     * API: Détail d'un VintPass
     */
    public function show(VintPass $vintPass): JsonResponse
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($vintPass->current_owner_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas le propriétaire de ce VintPass',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $vintPass->load(['item', 'verifiedByExpert', 'scans', 'transfers']),
        ]);
    }

    /**
     * API: Demander un VintPass pour un article
     */
    public function requestPass(Item $item, Request $request): JsonResponse
    {
        // Vérifier que l'utilisateur est le propriétaire de l'article
        if ($item->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas le propriétaire de cet article',
            ], 403);
        }

        // Vérifier qu'il n'y a pas déjà un VintPass
        if (VintPass::where('item_id', $item->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cet article possède déjà un VintPass',
            ], 400);
        }

        // Vérifier que l'article est vérifié
        if (!$item->authenticity_verified && $item->verification_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Votre article doit d\'abord être vérifié pour obtenir un VintPass',
                'requires_verification' => true,
            ], 400);
        }

        try {
            $vintPass = $this->vintPassService->createVintPass($item);

            return response()->json([
                'success' => true,
                'message' => 'VintPass créé avec succès',
                'data' => $vintPass->load(['item']),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du VintPass: ' . $e->getMessage(),
            ], 500);
        }
    }
}
