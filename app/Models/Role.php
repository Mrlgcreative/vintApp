<?php

namespace App\Models;


use App\Models\Concerns\HasPublicId;use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasPublicId;

    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Les utilisateurs qui ont ce rôle.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}