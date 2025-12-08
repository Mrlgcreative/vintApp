<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpertNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'type',
        'title',
        'message',
        'icon',
        'action_url',
        'read',
        'read_at',
        'data'
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public function scopeForExpert($query, $expertId)
    {
        return $query->where('user_id', $expertId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now()
        ]);

        return $this;
    }

    public function markAsUnread()
    {
        $this->update([
            'read' => false,
            'read_at' => null
        ]);

        return $this;
    }
}
