<?php

namespace App\Services\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Smalot\PdfParser\Parser;

/**
 * Briques communes aux imports « fichier (PDF/JSON) -> IA -> lignes -> upsert ».
 *
 * Le service qui l'utilise fournit la consigne IA, les listes de champs acceptés,
 * et sa propre logique d'écriture (seule à connaître le modèle Eloquent).
 */
trait ImportsFromPdf
{
    /** Nombre maximum de lignes importées en une fois. */
    protected const MAX_ROWS = 500;

    /** Longueur maximale du texte PDF envoyé à l'IA. */
    protected const MAX_TEXT_CHARS = 15000;

    /**
     * @return array{created:int, updated:int, errors:array<int,string>, dry_run:bool}
     */
    protected function emptyReport(bool $dryRun): array
    {
        return ['created' => 0, 'updated' => 0, 'errors' => [], 'dry_run' => $dryRun];
    }

    protected function maxRows(): int
    {
        return self::MAX_ROWS;
    }

    /**
     * Extrait le texte d'un PDF (librairie pure PHP, aucun binaire système requis).
     */
    protected function extractPdfText(string $path): string
    {
        if (! class_exists(Parser::class)) {
            throw new RuntimeException('Lecture du PDF indisponible : dépendance manquante (lancez « composer install »).');
        }

        try {
            return (new Parser())->parseFile($path)->getText();
        } catch (\Throwable $e) {
            Log::error('Import PDF : lecture impossible', ['error' => $e->getMessage()]);

            throw new RuntimeException('Lecture du PDF impossible (' . $e->getMessage() . ')');
        }
    }

    /**
     * Envoie le texte à l'IA et renvoie le tableau décodé (brut, à normaliser).
     *
     * @return array<int|string, mixed>
     */
    protected function structureWithAi(string $text, string $instruction): array
    {
        $apiKey = config('services.xkiro.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('Import PDF non configuré (XKIRO_API_KEY manquant).');
        }

        $prompt = $instruction . "\n\nTexte du document :\n---\n"
            . mb_substr($text, 0, self::MAX_TEXT_CHARS) . "\n---";

        try {
            $response = Http::withToken($apiKey)
                // Réutilise le timeout « lourd » : l'analyse IA d'un document est lente.
                ->timeout((int) config('services.xkiro.vision_timeout', 60))
                ->acceptJson()
                ->retry(2, 200)
                ->post(rtrim(config('services.xkiro.base_url'), '/') . '/chat/completions', [
                    'model' => config('services.xkiro.model'),
                    'max_tokens' => 4000,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                ]);
        } catch (\Throwable $e) {
            Log::error('Import PDF : appel xkiro impossible', ['error' => $e->getMessage()]);

            throw new RuntimeException('Analyse IA indisponible.');
        }

        if (! $response->successful()) {
            Log::error('Import PDF : xkiro en échec', ['status' => $response->status(), 'body' => $response->body()]);

            throw new RuntimeException('Analyse IA indisponible.');
        }

        $data = json_decode($this->cleanJson((string) $response->json('choices.0.message.content')), true);

        if (! is_array($data)) {
            throw new RuntimeException("Réponse de l'IA illisible.");
        }

        return $data;
    }

    private function cleanJson(string $raw): string
    {
        $raw = trim($raw);

        if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $raw, $m)) {
            return $m[1];
        }

        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($raw, $start, $end - $start + 1);
        }

        return $raw;
    }

    /**
     * Aplatit n'importe quelle forme de JSON en lignes {name, parent?, ...}.
     *
     * @param  array<int,string>  $stringFields
     * @param  array<int,string>  $boolFields
     * @param  array<int,string>  $intFields
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeRows(mixed $data, array $stringFields, array $boolFields, array $intFields = []): array
    {
        // Déballer un éventuel conteneur : {"categories":[...]} / {"brands":[...]}.
        if (is_array($data) && ! array_is_list($data)) {
            foreach (['categories', 'brands', 'data', 'items', 'list'] as $key) {
                if (isset($data[$key]) && is_array($data[$key])) {
                    $data = $data[$key];
                    break;
                }
            }
        }

        if (! is_array($data)) {
            return [];
        }

        $rows = [];
        foreach ($data as $entry) {
            $rows = array_merge($rows, $this->flatten($entry, null, $stringFields, $boolFields, $intFields));
        }

        return array_slice($rows, 0, self::MAX_ROWS);
    }

    /**
     * Transforme une entrée (chaîne, objet, ou objet avec enfants) en lignes.
     *
     * @param  array<int,string>  $stringFields
     * @param  array<int,string>  $boolFields
     * @param  array<int,string>  $intFields
     * @return array<int, array<string, mixed>>
     */
    private function flatten(mixed $entry, ?string $parent, array $stringFields, array $boolFields, array $intFields): array
    {
        if (is_string($entry)) {
            $name = trim($entry);

            return $name !== '' ? [['name' => $name, 'parent' => $parent]] : [];
        }

        if (! is_array($entry)) {
            return [];
        }

        $name = trim((string) ($entry['name'] ?? $entry['nom'] ?? $entry['title'] ?? $entry['label'] ?? ''));

        if ($name === '') {
            return [];
        }

        $row = [
            'name' => $name,
            'parent' => $this->rowParent($entry) ?? $parent,
        ];

        foreach ($stringFields as $field) {
            $value = $entry[$field] ?? null;
            if (is_string($value) && trim($value) !== '') {
                $row[$field] = trim($value);
            }
        }

        foreach ($intFields as $field) {
            if (isset($entry[$field]) && is_numeric($entry[$field])) {
                $row[$field] = (int) $entry[$field];
            }
        }

        foreach ($boolFields as $field) {
            if (array_key_exists($field, $entry)) {
                $bool = filter_var($entry[$field], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
                if ($bool !== null) {
                    $row[$field] = $bool;
                }
            }
        }

        $rows = [$row];

        foreach (['children', 'subcategories', 'sous_categories'] as $key) {
            if (isset($entry[$key]) && is_array($entry[$key])) {
                foreach ($entry[$key] as $child) {
                    $rows = array_merge($rows, $this->flatten($child, $name, $stringFields, $boolFields, $intFields));
                }
            }
        }

        return $rows;
    }

    private function rowParent(array $entry): ?string
    {
        foreach (['parent', 'parent_name', 'category', 'categorie_parente'] as $key) {
            if (isset($entry[$key]) && is_string($entry[$key]) && trim($entry[$key]) !== '') {
                return trim($entry[$key]);
            }
        }

        return null;
    }
}
