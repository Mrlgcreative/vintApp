<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class ApiController
{
    protected function successResponse(mixed $data = null, string $message = 'OK', array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

    protected function errorResponse(string $message = 'Erreur', int $status = 400, array $errors = [], mixed $data = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'meta' => (object) [],
        ];

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
