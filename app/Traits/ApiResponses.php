<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    /**
     * Nettoie récursivement les données pour assurer un encodage UTF-8 valide
     */
    private function cleanUtf8($data)
    {
        if (is_string($data)) {
            // Supprimer les caractères invalides et convertir en UTF-8 valide
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            // Remplacer les caractères non-UTF8 restants
            return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $data);
        }
        
        if (is_array($data)) {
            return array_map([$this, 'cleanUtf8'], $data);
        }
        
        if (is_object($data)) {
            // Convertir l'objet en tableau, nettoyer, puis reconvertir si nécessaire
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

    /**
     * Retourne une réponse de succès
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $this->cleanUtf8($data);
        }
        return response()->json($response, $code, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Retourne une réponse d'erreur
     */
    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];
        if ($errors !== null) {
            $response['errors'] = $this->cleanUtf8($errors);
        }
        return response()->json($response, $code, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Retourne une réponse paginée
     */
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
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
