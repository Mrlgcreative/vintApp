<?php

namespace App\Http\Controllers;

use App\Services\AiAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class BotController extends Controller
{
    public function __construct(
        private readonly AiAssistantService $assistant
    ) {
    }

    /**
     * Assistant IA VintApp — utilisé par le widget web et l'API mobile.
     *
     * Requête : { message|question: string, history?: [{role, content}] }
     * Réponse : { success: true, answer: string }
     */
    public function ask(Request $request)
    {
        // Validation manuelle : renvoie toujours du JSON (l'app mobile ne fixe
        // pas forcément Accept: application/json, cf. fetch React Native).
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:2000',
            'question' => 'nullable|string|max:2000', // compat avec l'ancienne API
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string|max:4000',
        ], [
            'history.*.role.in' => 'Le rôle doit être "user" ou "assistant".',
            'history.*.content.required_with' => 'Chaque message d\'historique doit avoir un contenu.',
            'history.*.content.string' => 'Chaque message d\'historique doit être une chaîne de texte.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $message = trim((string) ($data['message'] ?? $data['question'] ?? ''));

        if ($message === '') {
            return response()->json(['success' => false, 'error' => 'Message vide.'], 422);
        }

        // Web (session) ou mobile (token Sanctum).
        $user = $request->user() ?? $request->user('sanctum');

        try {
            $result = $this->assistant->ask($message, $data['history'] ?? [], $user);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 503);
        }

        return response()->json([
            'success' => true,
            'answer' => $result['answer'],
            'actions' => $result['actions'],
        ]);
    }
}
