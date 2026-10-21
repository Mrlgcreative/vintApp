<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasPublicId
{
    public static function bootHasPublicId(): void
    {
        static::creating(function ($model) {
            if (empty($model->public_id)) {
                $model->public_id = (string) Str::ulid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    /**
     * Le public_id est normalement charge avec le modele. Mais une requete qui
     * restreint ses colonnes (`->select('id', 'name', ...)`) le laisse absent,
     * et l'URL generee sortait alors vide : `route()` levait
     * UrlGenerationException et la page repondait 500. On retombe donc sur la
     * cle primaire, que `resolveRouteBinding` sait deja resoudre (voir plus
     * bas) : le lien numerique est moins propre mais reste fonctionnel.
     */
    public function getRouteKey(): string
    {
        return (string) ($this->public_id ?: $this->getKey());
    }

    /**
     * Resout le binding implicite sur public_id, avec repli sur la cle primaire.
     *
     * Le repli est un filet de compatibilite, pas une voie normale : de
     * nombreuses vues et scripts JS construisent encore leurs URL avec `->id`,
     * et l'application mobile deja publiee le fait aussi. Sans ce filet, chaque
     * lien encore numerique se termine par un 404. Les nouveaux liens doivent
     * passer par public_id.
     *
     * Un public_id est un ULID de 26 caracteres, donc la valeur ne peut jamais
     * etre numerique : il n'y a pas d'ambiguite entre les deux resolutions.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $instance = parent::resolveRouteBinding($value, $field);

        if ($instance !== null || $field !== null || ! is_numeric($value)) {
            return $instance;
        }

        return $this->where($this->getKeyName(), $value)->first();
    }

    /**
     * Meme resolution que le binding implicite, pour les controleurs qui
     * recoivent l'identifiant en parametre brut (`$id`) au lieu d'un modele.
     *
     * Sans cela, un `where('public_id', $id)` echoue des que l'identifiant est
     * numerique — ce que fait encore l'application mobile, qui envoie `order.id`.
     *
     * Declare en scope : fonctionne aussi bien en `Modele::findByPublicId($id)`
     * qu'enchaine apres `with(...)`.
     */
    public function scopeFindByPublicId($query, string|int $value)
    {
        // OR groupé : sans parenthèses, le OR s'échapperait et se combinerait
        // avec les `where` ajoutés ensuite par l'appelant.
        return $query->where(function ($q) use ($value) {
            $q->where($this->getRouteKeyName(), $value);

            if (is_numeric($value)) {
                $q->orWhere($this->getKeyName(), $value);
            }
        });
    }

    /**
     * Resout un modele depuis un identifiant public (ULID) ou numerique
     * (cle primaire), comme le fait le binding implicite.
     *
     * Utilise par l'API, ou l'application mobile envoie `model.id` alors que
     * le web utilise `model.public_id` : les deux doivent aboutir au meme
     * modele. Retourne null si rien ne correspond.
     */
    public static function resolveByPublicId(string|int $value): ?static
    {
        return static::query()->findByPublicId($value)->first();
    }

    /**
     * Ramène un identifiant mixte (public_id ou cle primaire) vers la cle
     * primaire.
     *
     * La valeur d'origine est conservee si elle ne correspond a aucun modele :
     * la validation applicative la signalera alors comme invalide.
     */
    public static function resolveId(string|int|null $value): string|int|null
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return static::resolveByPublicId($value)?->getKey() ?? $value;
    }

    /**
     * Normalise une liste d'identifiants melanges (public_id ou cle primaire)
     * vers les cles primaires correspondantes.
     *
     * Les valeurs inconnues sont laissees telles quelles : la validation
     * applicative les signalera alors comme invalides.
     */
    public static function resolveIds(array $values): array
    {
        $values = array_values(array_filter($values, fn ($value) => $value !== null && $value !== ''));

        if ($values === []) {
            return [];
        }

        $query = static::query();
        $model = $query->getModel();

        $rows = $query->where(function ($q) use ($values, $model) {
            $q->whereIn($model->getKeyName(), array_values(array_filter($values, 'is_numeric')));

            $publicIds = array_values(array_filter($values, fn ($value) => ! is_numeric($value)));

            if ($publicIds !== []) {
                $q->orWhereIn($model->getRouteKeyName(), $publicIds);
            }
        })->get([$model->getKeyName(), $model->getRouteKeyName()]);

        $map = [];
        foreach ($rows as $row) {
            $map[(string) $row->getRouteKey()] = $row->getKey();
            $map[(string) $row->getKey()] = $row->getKey();
        }

        return array_map(fn ($value) => $map[(string) $value] ?? $value, $values);
    }

    public function resolveSoftDeletableRouteBinding($value, $field = null)
    {
        $instance = parent::resolveSoftDeletableRouteBinding($value, $field);

        if ($instance !== null || $field !== null || ! is_numeric($value)) {
            return $instance;
        }

        return $this->where($this->getKeyName(), $value)->withTrashed()->first();
    }
}
