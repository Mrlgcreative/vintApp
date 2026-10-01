<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'latitude',
        'longitude',
        'city',
        'country',
        'last_activity',
        'login_at',
        'logout_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_activity' => 'datetime',
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
            'is_active' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
