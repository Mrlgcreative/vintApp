<?php

namespace App\Http\Controllers;

use App\Services\TokenIntrospectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenIntrospectionController extends ApiController
{
    public function __construct(private readonly TokenIntrospectionService $introspection) {}

    /**
     * POST /v1/token/introspect
     *
     * Résout le Bearer token en identité. Le middleware `service` a déjà
     * vérifié la signature du service appelant et rejeté les tokens invalides ;
     * ici on se contente de renvoyer l'identité résolue.
     */
    public function introspect(Request $request): JsonResponse
    {
        $identity = $request->attributes->get('vintapp_identity');

        return $this->successResponse([
            'active' => true,
            'identity' => $identity,
        ]);
    }
}
