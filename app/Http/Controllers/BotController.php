<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{
    public function ask(Request $request)
    {
        $question = $request->input('question');
        try {
            $response = Http::withOptions([
                'verify' => base_path('cacert.pem'),
            ])
            ->withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant pour la marketplace Vintapp. Réponds de façon concise, utile et polie.'],
                    ['role' => 'user', 'content' => $question],
                ],
                'max_tokens' => 300,
            ]);
            if (!$response->ok()) {
                return response()->json([
                    'answer' => 'Erreur OpenAI : ' . ($response->json('error.message') ?? $response->body())
                ], 500);
            }
            return response()->json([
                'answer' => $response['choices'][0]['message']['content'] ?? 'Désolé, je n’ai pas compris.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'answer' => 'Erreur serveur : ' . $e->getMessage()
            ], 500);
        }
    }
} 