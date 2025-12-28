<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VintPassScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'vint_pass_id',
        'scanned_by_user_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'scan_result',
    ];

    public function vintPass(): BelongsTo
    {
        return $this->belongsTo(VintPass::class);
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by_user_id');
    }
}
