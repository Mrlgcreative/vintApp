<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VintPassTransfer extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'vint_pass_id',
        'from_user_id',
        'to_user_id',
        'order_id',
        'transfer_price',
        'currency',
        'blockchain_tx_hash',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'transfer_price' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function vintPass(): BelongsTo
    {
        return $this->belongsTo(VintPass::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
