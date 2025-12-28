<?php

namespace App\Http\Controllers;

use App\Models\VintPass;
use App\Models\Item;
use App\Services\VintPassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VintPassController extends Controller
{
    protected VintPassService $vintPassService;

    public function __construct(VintPassService $vintPassService)
    {
        $this->vintPassService = $vintPassService;
    }

    /**
     * Page publique de vérification d'un VintPass
     */
    public function verify(string $shortCode, Request $request)
    {
        $result = $this->vintPassService->verifyByShortCode($shortCode);

        if (!$result) {
            return view('vintpass.not-found', [
                'shortCode' => $shortCode,
            ]);
        }

        // Enregistrer le scan
        $vintPass = VintPass::where('short_code', $shortCode)->first();
        $vintPass->recordScan(
            Auth::user(),
            $request->ip(),
            $request->userAgent()
        );

        return view('vintpass.verify', [
            'vintPass' => $vintPass,
            'data' => $result['vint_pass'],
            'isValid' => $result['valid'],
        ]);
    }

    /**
     * API: Vérifier un VintPass
     */
    public function apiVerify(string $shortCode, Request $request)
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
     * Liste des VintPass de l'utilisateur connecté
     */
    public function myPasses(Request $request)
    {
        $passes = VintPass::where('current_owner_id', Auth::id())
            ->with(['item'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('vintpass.my-passes', [
            'passes' => $passes,
        ]);
    }

    /**
     * API: Liste des VintPass de l'utilisateur
     */
    public function apiMyPasses(Request $request)
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
    public function apiShow(VintPass $vintPass)
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
    public function apiRequestPass(Item $item, Request $request)
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

    /**
     * Détail d'un VintPass (propriétaire uniquement)
     */
    public function show(VintPass $vintPass)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($vintPass->current_owner_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas le propriétaire de ce VintPass');
        }

        return view('vintpass.show', [
            'vintPass' => $vintPass->load(['item', 'verifiedByExpert', 'scans', 'transfers']),
        ]);
    }

    /**
     * Historique des scans d'un VintPass
     */
    public function scanHistory(VintPass $vintPass)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($vintPass->current_owner_id !== Auth::id()) {
            abort(403);
        }

        $scans = $vintPass->scans()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('vintpass.scan-history', [
            'vintPass' => $vintPass,
            'scans' => $scans,
        ]);
    }

    /**
     * Historique des transferts d'un VintPass
     */
    public function transferHistory(VintPass $vintPass)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($vintPass->current_owner_id !== Auth::id()) {
            abort(403);
        }

        $transfers = $vintPass->transfers()
            ->with(['fromUser', 'toUser'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('vintpass.transfer-history', [
            'vintPass' => $vintPass,
            'transfers' => $transfers,
        ]);
    }

    /**
     * Télécharger le QR Code
     */
    public function downloadQR(VintPass $vintPass)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($vintPass->current_owner_id !== Auth::id()) {
            abort(403);
        }

        $path = storage_path('app/public/' . $vintPass->qr_code_path);

        if (!file_exists($path)) {
            // Régénérer le QR Code
            $qrPath = $this->vintPassService->generateQRCode($vintPass);
            $vintPass->update(['qr_code_path' => $qrPath]);
            $path = storage_path('app/public/' . $qrPath);
        }

        return response()->download($path, 'vintpass-' . $vintPass->pass_id . '.svg');
    }

    /**
     * Demander un VintPass pour un article
     */
    public function requestPass(Item $item)
    {
        // Vérifier que l'utilisateur est le propriétaire de l'article
        if ($item->user_id !== Auth::id()) {
            return back()->with('error', 'Vous n\'êtes pas le propriétaire de cet article');
        }

        // Vérifier qu'il n'y a pas déjà un VintPass
        if (VintPass::where('item_id', $item->id)->exists()) {
            return back()->with('error', 'Cet article possède déjà un VintPass');
        }

        // Vérifier que l'article est vérifié
        if (!$item->authenticity_verified && $item->verification_status !== 'approved') {
            return redirect()->route('authenticity.request', $item)
                ->with('info', 'Votre article doit d\'abord être vérifié pour obtenir un VintPass');
        }

        try {
            $vintPass = $this->vintPassService->createVintPass($item);

            return redirect()->route('vintpass.show', $vintPass)
                ->with('success', 'VintPass créé avec succès ! ID: ' . $vintPass->pass_id);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création du VintPass: ' . $e->getMessage());
        }
    }
}
