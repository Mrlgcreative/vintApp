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

    public function resolveRouteBinding($value, $field = null)
    {
        $instance = parent::resolveRouteBinding($value, $field);

        if ($instance !== null || $field !== null || ! is_numeric($value)) {
            return $instance;
        }

        return $this->where($this->getKeyName(), $value)->first();
    }
}
