<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Slug
{
    /**
     * Construit un slug unique pour une table donnée.
     *
     * @param  class-string<Model>  $modelClass
     */
    public static function unique(string $modelClass, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'element';
        }

        $slug = $base;
        $suffix = 1;

        while ($modelClass::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()
        ) {
            $suffix++;
            $slug = $base.'-'.$suffix;
        }

        return $slug;
    }
}
