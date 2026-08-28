<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\ApiController;
use App\Services\UserDataExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Contrôleur — Portabilité des données (RGPD article 20).
 *
 * Permet à l'utilisateur de télécharger l'ensemble de ses données
 * personnelles dans un format structuré et interopérable (JSON).
 */
class DataExportController extends ApiController
{
    public function download(Request $request): Response
    {
        try {
            $user = $request->user();

            $export = app(UserDataExportService::class)->export($user);

            $filename = 'vintapp-donnees-' . strtolower($user->username ?? $user->name) . '-' . now()->format('Y-m-d') . '.json';
            $filename = preg_replace('/[^a-z0-9\-\.]/i', '-', $filename);

            $json = json_encode($export, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);

            return response($json, 200, [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($json),
            ]);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse(
                'Erreur lors de la génération de vos données. Réessayez plus tard.',
                $e
            );
        }
    }

    /**
     * Version "aperçu" de l'export — utile pour la consommation directe
     * de l'app mobile sans téléchargement.
     */
    public function preview(Request $request)
    {
        try {
            $user = $request->user();

            $export = app(UserDataExportService::class)->export($user);

            return $this->successResponse([
                'exported_at' => $export['meta']['exported_at'],
                'format_version' => $export['meta']['format_version'],
                'categories' => array_keys(array_diff_key($export, ['meta' => true])),
                'data' => $export,
            ], 'Données exportées avec succès');
        } catch (\Throwable $e) {
            return $this->serverErrorResponse(
                'Erreur lors de l\'export de vos données.',
                $e
            );
        }
    }
}
