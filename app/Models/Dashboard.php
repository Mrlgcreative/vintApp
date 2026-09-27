<?php

namespace App\Models;


use App\Models\Concerns\HasPublicId;use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasPublicId;

    use HasFactory;

    protected $fillable = [
        'user_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
