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

    public function resolveSoftDeletableRouteBinding($value, $field = null)
    {
        $instance = parent::resolveSoftDeletableRouteBinding($value, $field);

        if ($instance !== null || $field !== null || ! is_numeric($value)) {
            return $instance;
        }

        return $this->where($this->getKeyName(), $value)->withTrashed()->first();
    }
}
