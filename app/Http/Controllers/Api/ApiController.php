<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Base de tous les contrôleurs API REST.
 * Standardise les réponses JSON : { success, message, data, meta }.
 */
class ApiController extends Controller
{
    /**
     * Nettoie récursivement les données pour assurer un encodage UTF-8 valide.
     */
    protected function cleanUtf8($data)
    {
        if (is_string($data)) {
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');

            return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $data);
        }

        if (is_array($data)) {
            return array_map([$this, 'cleanUtf8'], $data);
        }

        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                return $this->cleanUtf8($data->toArray());
            }

            foreach ($data as $key => $value) {
                $data->$key = $this->cleanUtf8($value);
            }

            return $data;
        }

        return $data;
    }

    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = ['success' => true, 'message' => $message];

        if ($data !== null) {
            $response['data'] = $this->cleanUtf8($data);
        }

        return response()->json($response, $statusCode, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    protected function paginatedResponse($paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $this->cleanUtf8($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];

        if ($errors !== null) {
            $response['errors'] = $this->cleanUtf8($errors);
        }

        return response()->json($response, $statusCode, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return $this->errorResponse('Erreur de validation', 422, $errors);
    }

    protected function unauthorizedResponse(string $message = 'Non autorisé'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    protected function forbiddenResponse(string $message = 'Accès interdit'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    protected function notFoundResponse(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    protected function serverErrorResponse(string $message = 'Erreur serveur', ?\Exception $e = null): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];

        if ($e && config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($response, 500, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    protected function createdResponse($data, string $message = 'Créé avec succès'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    protected function updatedResponse($data = null, string $message = 'Mis à jour avec succès'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    protected function deletedResponse(string $message = 'Supprimé avec succès'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }
}
