<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur de base pour toutes les API
 * Standardise les réponses JSON
 */
class ApiController extends Controller
{
    /**
     * Réponse de succès standardisée
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Réponse de succès avec données paginées
     */
    protected function paginatedResponse($paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ]
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Réponse d'erreur standardisée
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Réponse de validation échouée
     */
    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return $this->errorResponse('Erreur de validation', 422, $errors);
    }

    /**
     * Réponse non autorisée
     */
    protected function unauthorizedResponse(string $message = 'Non autorisé'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Réponse interdite
     */
    protected function forbiddenResponse(string $message = 'Accès interdit'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Réponse ressource non trouvée
     */
    protected function notFoundResponse(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Réponse erreur serveur
     */
    protected function serverErrorResponse(string $message = 'Erreur serveur', \Exception $e = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($e && config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($response, 500, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Réponse de création réussie
     */
    protected function createdResponse($data, string $message = 'Créé avec succès'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Réponse de suppression réussie
     */
    protected function deletedResponse(string $message = 'Supprimé avec succès'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }

    /**
     * Réponse de mise à jour réussie
     */
    protected function updatedResponse($data = null, string $message = 'Mis à jour avec succès'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }
}
