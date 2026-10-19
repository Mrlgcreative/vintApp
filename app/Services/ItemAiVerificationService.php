<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;

/**
 * Vérification IA des articles soumis (modèle vision xkiro) :
 * analyse les photos ET la description, puis renvoie un verdict structuré.
 */
class ItemAiVerificationService
{
    /** Nombre maximum de photos envoyées au modèle. */
    private const MAX_IMAGES = 6;

    /** Dimension maximale (px) de chaque photo envoyée (réduit le coût en tokens). */
    private const MAX_DIMENSION = 1024;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Analyse un article et renvoie le verdict structuré (sans rien persister).
     *
     * @return array{score:int,recommendation:string,real_product_photos:bool,photo_matches_description:bool,issues:array<int,string>,summary:string}
     */
    public function verify(Item $item): array
    {
        $apiKey = config('services.xkiro.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('Vérification IA non configurée (XKIRO_API_KEY manquant).');
        }

        $images = $this->imageDataUris($item->images ?? []);

        if (empty($images)) {
            throw new RuntimeException("Aucune photo exploitable pour l'analyse IA.");
        }

        $content = [['type' => 'text', 'text' => $this->prompt($item, count($images))]];
        foreach ($images as $uri) {
            $content[] = ['type' => 'image_url', 'image_url' => ['url' => $uri]];
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('services.xkiro.vision_timeout', 60))
                ->acceptJson()
                ->retry(2, 200)
                ->post(rtrim(config('services.xkiro.base_url'), '/') . '/chat/completions', [
                    'model' => config('services.xkiro.vision_model'),
                    'messages' => [['role' => 'user', 'content' => $content]],
                    'max_tokens' => 800,
                    'response_format' => ['type' => 'json_object'],
                ]);
        } catch (\Throwable $e) {
            Log::error('Vérification IA: appel xkiro impossible', ['item_id' => $item->id, 'error' => $e->getMessage()]);

            throw new RuntimeException("Service d'analyse IA momentanément indisponible.");
        }

        if (! $response->successful()) {
            Log::error('Vérification IA: xkiro en échec', [
                'item_id' => $item->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException("Service d'analyse IA momentanément indisponible.");
        }

        $verdict = $this->parseVerdict($response->json('choices.0.message.content'));

        return $verdict + [
            'model' => (string) config('services.xkiro.vision_model'),
            'analyzed_at' => now()->toIso8601String(),
            'photos_count' => count($images),
        ];
    }

    /**
     * Enregistre le verdict dans verification_details['ai'] et met à jour le score affiché.
     *
     * Le score du modèle vision remplace le score heuristique local : la fiche
     * article et les listes admin reflètent donc l'analyse IA réelle.
     */
    public function store(Item $item, array $verdict): void
    {
        $details = is_array($item->verification_details) ? $item->verification_details : [];
        $details['ai'] = $verdict;

        $item->verification_details = $details;

        if (isset($verdict['score'])) {
            $item->verification_score = $verdict['score'];
        }

        $item->save();
    }

    /**
     * Enregistre l'échec de l'analyse (visible par l'admin, sans casser l'article).
     */
    public function storeError(Item $item, string $message): void
    {
        $this->store($item, [
            'error' => $message,
            'analyzed_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Redimensionne et encode les photos en data URIs (base64) pour le modèle.
     *
     * @param  array<int,string>  $paths
     * @return array<int,string>
     */
    private function imageDataUris(array $paths): array
    {
        $uris = [];

        foreach (array_slice(array_values(array_filter($paths)), 0, self::MAX_IMAGES) as $path) {
            try {
                $fullPath = Storage::disk('public')->path($path);

                if (! is_file($fullPath)) {
                    continue;
                }

                $uris[] = $this->manager
                    ->read($fullPath)
                    ->scaleDown(width: self::MAX_DIMENSION, height: self::MAX_DIMENSION)
                    ->toJpeg(75)
                    ->toDataUri();
            } catch (\Throwable $e) {
                Log::warning('Vérification IA: image ignorée', ['path' => $path, 'error' => $e->getMessage()]);
            }
        }

        return $uris;
    }

    /**
     * Prompt de modération (photos + texte) demandant un JSON strict.
     */
    private function prompt(Item $item, int $imageCount): string
    {
        $description = mb_substr((string) ($item->description ?? ''), 0, 2000);

        $listing = implode("\n", [
            'Nom : ' . $item->name,
            'Description : ' . ($description !== '' ? $description : '(vide)'),
            'Marque : ' . ($item->brand->name ?? 'non renseignée'),
            'Catégorie : ' . ($item->category->name ?? 'non renseignée'),
            'État : ' . ($item->condition ?: 'non renseigné'),
            'Couleur : ' . ($item->color ?: 'non renseignée'),
            'Taille : ' . ($item->size ?: 'non renseignée'),
            'Prix : ' . $item->price . ' ' . ($item->currency ?? ''),
        ]);

        return <<<TXT
        Tu es modérateur de VintApp, une marketplace d'articles d'occasion. On te fournit {$imageCount} photo(s) et le texte d'une annonce soumise par un vendeur.

        Vérifie :
        1. Les photos montrent-elles réellement un produit (et non un logo, un visuel marketing, une capture d'écran, une image de stock ou un watermark) ?
        2. Les photos correspondent-elles à l'article décrit (couleur, type, marque) ?
        3. Le texte contient-il des signaux de fraude (coordonnées hors plateforme, liens externes, spam, article contrefait) ?

        Annonce :
        {$listing}

        Réponds UNIQUEMENT avec un objet JSON valide, sans texte autour, avec exactement ces clés :
        {
          "score": entier de 0 à 100 (confiance que l'annonce est authentique et publiable),
          "recommendation": "approve" | "review" | "reject",
          "real_product_photos": booléen (les photos montrent-elles un vrai produit),
          "photo_matches_description": booléen (les photos correspondent-elles au texte),
          "issues": tableau de chaînes courtes décrivant les problèmes détectés (vide si aucun),
          "summary": une à deux phrases en français résumant ton avis
        }

        Règles : "approve" si score >= 75 et aucun problème bloquant ; "reject" si score < 40 ou problème grave (image volée, contrefaçon évidente, fraude) ; sinon "review".
        TXT;
    }

    /**
     * Parse et normalise le JSON renvoyé par le modèle (tolère les fences Markdown).
     *
     * @return array{score:int,recommendation:string,real_product_photos:bool,photo_matches_description:bool,issues:array<int,string>,summary:string}
     */
    private function parseVerdict(?string $raw): array
    {
        $raw = trim((string) $raw);

        // Retirer un éventuel bloc ```json ... ```
        if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $raw, $m)) {
            $raw = $m[1];
        } elseif (($start = strpos($raw, '{')) !== false && ($end = strrpos($raw, '}')) !== false) {
            $raw = substr($raw, $start, $end - $start + 1);
        }

        $data = json_decode($raw, true);

        if (! is_array($data)) {
            throw new RuntimeException("Réponse de l'IA illisible.");
        }

        $score = max(0, min(100, (int) round((float) ($data['score'] ?? 0))));
        $recommendation = in_array($data['recommendation'] ?? null, ['approve', 'review', 'reject'], true)
            ? $data['recommendation']
            : 'review';

        return [
            'score' => $score,
            'recommendation' => $recommendation,
            'real_product_photos' => $this->toBool($data['real_product_photos'] ?? null),
            'photo_matches_description' => $this->toBool($data['photo_matches_description'] ?? null),
            'issues' => $this->cleanIssues($data['issues'] ?? []),
            'summary' => trim(mb_substr((string) ($data['summary'] ?? ''), 0, 600)),
        ];
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
    }

    /**
     * @return array<int,string>
     */
    private function cleanIssues(mixed $issues): array
    {
        if (! is_array($issues)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($issue) => is_string($issue) ? mb_substr(trim($issue), 0, 300) : null,
            $issues
        )));
    }
}
