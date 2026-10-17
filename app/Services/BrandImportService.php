<?php

namespace App\Services;

use App\Models\Brand;
use App\Services\Concerns\ImportsFromPdf;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Import en masse de marques depuis un fichier JSON ou PDF.
 *
 * - JSON : décodé directement (liste de noms ou d'objets).
 * - PDF  : texte extrait en pur PHP, puis structuré par l'IA (xkiro) avant import.
 *
 * Les deux formats convergent vers importRows(), seul endroit qui écrit en base.
 */
class BrandImportService
{
    use ImportsFromPdf;

    /** Champs texte repris tels quels depuis le fichier. */
    private const STRING_FIELDS = ['slug', 'description', 'logo', 'website', 'country', 'type'];

    /** Champs booléens (is_active). */
    private const BOOL_FIELDS = ['is_active'];

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
     * Importe un JSON de marques.
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
        return $this->normalizeRows($data, self::STRING_FIELDS, self::BOOL_FIELDS);
    }

    private function prompt(): string
    {
        $max = $this->maxRows();

        return <<<TXT
        Voici le texte brut d'un document qui contient des marques (vêtements, accessoires, etc.) pour la marketplace VintApp.

        Extrais toutes les marques. Réponds UNIQUEMENT avec un objet JSON valide de cette forme :
        {"brands":[{"name":"...","description":null,"website":null,"country":null}]}

        Règles :
        - "name" : nom de la marque, tel qu'il apparaît.
        - "description" : courte description si présente dans le document, sinon null.
        - "website" : site officiel si présent dans le document, sinon null.
        - "country" : pays d'origine si présent dans le document, sinon null.
        - N'invente rien qui ne figure pas dans le texte. Ignore les prix, quantités, en-têtes, tableaux de nombres et numéros de page.
        - Maximum {$max} marques. Si aucune marque n'est identifiable, renvoie {"brands":[]}.
        TXT;
    }

    /**
     * Écrit (ou simule) les marques. Seul point d'écriture en base.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{created:int, updated:int, errors:array<int,string>, dry_run:bool}
     */
    private function importRows(array $rows, bool $dryRun): array
    {
        $report = $this->emptyReport($dryRun);

        if (empty($rows)) {
            $report['errors'][] = 'Aucune marque trouvée dans le fichier.';

            return $report;
        }

        foreach ($rows as $row) {
            $name = $row['name'];

            try {
                $slug = Str::slug($row['slug'] ?? $name);

                if ($slug === '') {
                    throw new RuntimeException("Nom inutilisable : « {$name} »");
                }

                // Marque déjà présente : même slug ou même nom.
                $existing = Brand::where('slug', $slug)->orWhere('name', $name)->first();

                // Champs facultatifs fournis (on ne touche pas à ce qui n'est pas présent).
                $data = [];
                foreach ([...self::STRING_FIELDS, ...self::BOOL_FIELDS] as $field) {
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
                // Les marques importées sont actives par défaut (sauf indication contraire).
                $data['is_active'] = $data['is_active'] ?? true;

                Brand::create($data);
                $report['created']++;
            } catch (\Throwable $e) {
                $report['errors'][] = $e->getMessage();
            }
        }

        return $report;
    }
}
