<?php

namespace App\Services;

use App\Models\Category;
use App\Services\Concerns\ImportsFromPdf;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Import en masse de catégories depuis un fichier JSON ou PDF.
 *
 * - JSON : décodé directement (liste de noms, d'objets, ou hiérarchie via "children").
 * - PDF  : texte extrait en pur PHP, puis structuré par l'IA (xkiro) avant import.
 *
 * Les deux formats convergent vers importRows(), seul endroit qui écrit en base.
 */
class CategoryImportService
{
    use ImportsFromPdf;

    /** Champs texte repris tels quels depuis le fichier. */
    private const STRING_FIELDS = ['slug', 'description', 'icon', 'color', 'image', 'meta_title', 'meta_description', 'meta_keywords'];

    /** Champs booléens (is_active / is_featured / show_in_menu). */
    private const BOOL_FIELDS = ['is_active', 'is_featured', 'show_in_menu'];

    /** Champs entiers (sort_order). */
    private const INT_FIELDS = ['sort_order'];

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

        return $this->importRows($this->rows($decoded), $dryRun);
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

        return $this->importRows($this->rows($this->structureWithAi($text, $this->prompt())), $dryRun);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(mixed $data): array
    {
        return $this->normalizeRows($data, self::STRING_FIELDS, self::BOOL_FIELDS, self::INT_FIELDS);
    }

    private function prompt(): string
    {
        $max = $this->maxRows();

        return <<<TXT
        Voici le texte brut d'un document qui contient des catégories de produits pour la marketplace VintApp.

        Extrais toutes les catégories. Réponds UNIQUEMENT avec un objet JSON valide de cette forme :
        {"categories":[{"name":"...","parent":null,"description":null}]}

        Règles :
        - "name" : nom de la catégorie, court et propre.
        - "parent" : nom de la catégorie parente si le document présente une hiérarchie, sinon null.
        - "description" : courte description si présente dans le document, sinon null.
        - N'invente rien qui ne figure pas dans le texte. Ignore les prix, quantités, en-têtes, tableaux de nombres et numéros de page.
        - Maximum {$max} catégories. Si aucune catégorie n'est identifiable, renvoie {"categories":[]}.
        TXT;
    }

    /**
     * Écrit (ou simule) les catégories. Seul point d'écriture en base.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{created:int, updated:int, errors:array<int,string>, dry_run:bool}
     */
    private function importRows(array $rows, bool $dryRun): array
    {
        $report = $this->emptyReport($dryRun);

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
                foreach ([...self::STRING_FIELDS, ...self::INT_FIELDS, ...self::BOOL_FIELDS] as $field) {
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
