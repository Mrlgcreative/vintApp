<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Smalot\PdfParser\Parser;

/**
 * Import en masse de catégories depuis un fichier JSON ou PDF.
 *
 * - JSON : décodé directement (liste de noms, d'objets, ou hiérarchie via "children").
 * - PDF  : texte extrait par pdftotext, puis structuré par l'IA (xkiro) avant import.
 *
 * Les deux formats convergent vers importRows(), seul endroit qui écrit en base.
 */
class CategoryImportService
{
    /** Nombre maximum de catégories importées en une fois. */
    private const MAX_ROWS = 500;

    /** Longueur maximale du texte PDF envoyé à l'IA. */
    private const MAX_TEXT_CHARS = 15000;

    /**
     * Importe un fichier (détecté d'après son extension).
     *
     * @return array{created:int, updated:int, errors:array<int,string>, dry_run:bool}
     */
    public function import(string $path, string $extension, bool $dryRun = false): array
    {
        return strtolower($extension) === 'json'
            ? $this->importJson((string) file_get_contents($path), $dryRun)
            : $this->importPdf($path, $dryRun);
    }

    /**
     * Importe un JSON de catégories.
     *
     * @return array{created:int, updated:int, errors:array<int,string>, dry_run:bool}
     */
    public function importJson(string $json, bool $dryRun = false): array
    {
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Fichier JSON invalide.');
        }

        return $this->importRows($this->normalize($decoded), $dryRun);
    }

    /**
     * Importe un PDF : extraction du texte puis structuration par l'IA.
     *
     * @return array{created:int, updated:int, errors:array<int,string>, dry_run:bool}
     */
    public function importPdf(string $path, bool $dryRun = false): array
    {
        return $this->importPdfText($this->extractPdfText($path), $dryRun);
    }

    /**
     * Importe à partir du texte brut d'un PDF (séparé pour être testable sans PDF).
     *
     * @return array{created:int, updated:int, errors:array<int,string>, dry_run:bool}
     */
    public function importPdfText(string $text, bool $dryRun = false): array
    {
        $text = trim($text);

        if ($text === '') {
            throw new RuntimeException('PDF sans texte exploitable (document scanné ?)');
        }

        return $this->importRows($this->normalize($this->structureWithAi($text)), $dryRun);
    }

    /**
     * Extrait le texte d'un PDF (librairie pure PHP, aucun binaire système requis).
     */
    private function extractPdfText(string $path): string
    {
        if (! class_exists(Parser::class)) {
            throw new RuntimeException('Lecture du PDF indisponible : dépendance manquante (lancez « composer install »).');
        }

        try {
            return (new Parser())->parseFile($path)->getText();
        } catch (\Throwable $e) {
            Log::error('Import catégories: lecture PDF impossible', ['error' => $e->getMessage()]);

            throw new RuntimeException('Lecture du PDF impossible (' . $e->getMessage() . ')');
        }
    }

    /**
     * Envoie le texte du PDF à l'IA et récupère une liste de catégories normalisées.
     *
     * @return array<int, mixed>
     */
    private function structureWithAi(string $text): array
    {
        $apiKey = config('services.xkiro.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('Import PDF non configuré (XKIRO_API_KEY manquant).');
        }

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
                    'messages' => [[
                        'role' => 'user',
                        'content' => $this->pdfPrompt(mb_substr($text, 0, self::MAX_TEXT_CHARS)),
                    ]],
                ]);
        } catch (\Throwable $e) {
            Log::error('Import catégories: appel xkiro impossible', ['error' => $e->getMessage()]);

            throw new RuntimeException("Analyse IA indisponible.");
        }

        if (! $response->successful()) {
            Log::error('Import catégories: xkiro en échec', ['status' => $response->status(), 'body' => $response->body()]);

            throw new RuntimeException("Analyse IA indisponible.");
        }

        $data = json_decode($this->cleanJson((string) $response->json('choices.0.message.content')), true);

        if (! is_array($data)) {
            throw new RuntimeException("Réponse de l'IA illisible.");
        }

        return (isset($data['categories']) && is_array($data['categories'])) ? $data['categories'] : $data;
    }

    private function pdfPrompt(string $text): string
    {
        return <<<TXT
        Voici le texte brut d'un document qui contient des catégories de produits pour la marketplace VintApp.

        Extrais toutes les catégories. Réponds UNIQUEMENT avec un objet JSON valide de cette forme :
        {"categories":[{"name":"...","parent":null,"description":null}]}

        Règles :
        - "name" : nom de la catégorie, court et propre.
        - "parent" : nom de la catégorie parente si le document présente une hiérarchie, sinon null.
        - "description" : courte description si présente dans le document, sinon null.
        - N'invente rien qui ne figure pas dans le texte. Ignore les prix, quantités, en-têtes, tableaux de nombres et numéros de page.
        - Maximum {$this->maxRows()} catégories. Si aucune catégorie n'est identifiable, renvoie {"categories":[]}.

        Texte du document :
        ---
        {$text}
        ---
        TXT;
    }

    private function maxRows(): int
    {
        return self::MAX_ROWS;
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
     * @return array<int, array<string, mixed>>
     */
    private function normalize(mixed $data): array
    {
        // Déballer un éventuel conteneur : {"categories":[...]} / {"data":[...]}.
        if (is_array($data) && ! array_is_list($data)) {
            foreach (['categories', 'data', 'items', 'list'] as $key) {
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
            $rows = array_merge($rows, $this->flatten($entry, null));
        }

        return array_slice($rows, 0, self::MAX_ROWS);
    }

    /**
     * Transforme une entrée (chaîne, objet, ou objet avec enfants) en lignes.
     *
     * @return array<int, array<string, mixed>>
     */
    private function flatten(mixed $entry, ?string $parent): array
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

        foreach (['slug', 'description', 'icon', 'color', 'image', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
            $value = $entry[$field] ?? null;
            if (is_string($value) && trim($value) !== '') {
                $row[$field] = trim($value);
            }
        }

        if (isset($entry['sort_order']) && is_numeric($entry['sort_order'])) {
            $row['sort_order'] = (int) $entry['sort_order'];
        }

        foreach (['is_active', 'is_featured', 'show_in_menu'] as $field) {
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
                    $rows = array_merge($rows, $this->flatten($child, $name));
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

    /**
     * Écrit (ou simule) les catégories. Seul point d'écriture en base.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{created:int, updated:int, errors:array<int,string>, dry_run:bool}
     */
    private function importRows(array $rows, bool $dryRun): array
    {
        $report = ['created' => 0, 'updated' => 0, 'errors' => [], 'dry_run' => $dryRun];

        if (empty($rows)) {
            $report['errors'][] = 'Aucune catégorie trouvée dans le fichier.';

            return $report;
        }

        foreach ($rows as $row) {
            $name = $row['name'];

            try {
                $slug = Str::slug($row['slug'] ?? $name);

                if ($slug === '') {
                    throw new RuntimeException("Nom inutilisable : « {$name} »");
                }

                $parent = $this->resolveParentId($row['parent'] ?? null, $dryRun);
                if ($parent['created']) {
                    $report['created']++;
                }
                $parentId = $parent['id'];
                $existing = Category::where('slug', $slug)->first();

                // Champs facultatifs fournis (on ne touche pas à ce qui n'est pas présent).
                $data = ['parent_id' => $parentId];
                foreach (['slug', 'description', 'icon', 'color', 'image', 'meta_title', 'meta_description', 'meta_keywords', 'sort_order', 'is_active', 'is_featured', 'show_in_menu'] as $field) {
                    if (array_key_exists($field, $row)) {
                        $data[$field] = $row[$field];
                    }
                }

                if ($existing) {
                    if (! $dryRun) {
                        $existing->update($data);
                    }
                    $report['updated']++;
                    continue;
                }

                if ($dryRun) {
                    $report['created']++;
                    continue;
                }

                $data['slug'] = $slug;
                $data['name'] = $name;
                // Les catégories importées sont actives par défaut (sauf indication contraire).
                $data['is_active'] = $data['is_active'] ?? true;
                $data['sort_order'] = $data['sort_order'] ?? ((Category::where('parent_id', $parentId)->max('sort_order') ?? 0) + 1);

                Category::create($data);
                $report['created']++;
            } catch (\Throwable $e) {
                $report['errors'][] = $e->getMessage();
            }
        }

        return $report;
    }

    /**
     * Résout l'id de la catégorie parente par slug ou nom, en la créant si besoin.
     *
     * @return array{id:?int, created:bool}
     */
    private function resolveParentId(?string $parent, bool $dryRun): array
    {
        $parent = trim((string) $parent);

        if ($parent === '') {
            return ['id' => null, 'created' => false];
        }

        $found = Category::where('slug', Str::slug($parent))->orWhere('name', $parent)->first();

        if ($found) {
            return ['id' => $found->id, 'created' => false];
        }

        if ($dryRun) {
            return ['id' => null, 'created' => true];
        }

        $created = Category::create([
            'name' => $parent,
            'slug' => Str::slug($parent) ?: Str::random(8),
            'is_active' => true,
            'sort_order' => (Category::whereNull('parent_id')->max('sort_order') ?? 0) + 1,
        ]);

        return ['id' => $created->id, 'created' => true];
    }
}
