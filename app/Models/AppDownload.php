<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AppDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'version',
        'url',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'ip_address',
        'referrer',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeByPlatform(Builder $query, string $platform): Builder
    {
        return $query->where('platform', $platform);
    }

    public function scopeByPeriod(Builder $query, int $days): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }
}