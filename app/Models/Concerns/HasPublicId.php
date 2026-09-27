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

    public function getRouteKey(): string
    {
        return (string) $this->public_id;
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

    public function resolveSoftDeletableRouteBinding($value, $field = null)
    {
        $instance = parent::resolveSoftDeletableRouteBinding($value, $field);

        if ($instance !== null || $field !== null || ! is_numeric($value)) {
            return $instance;
        }

        return $this->where($this->getKeyName(), $value)->withTrashed()->first();
    }
}
