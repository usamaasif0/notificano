<?php

namespace Notificano\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'from_user',
        'to_user',
        'title',
        'read_at',
        'url'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'from_user', 'id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'to_user', 'id');
    }

    public function markAsRead(): bool
    {
        return $this->update(['read_at' => now()]);
    }

    public function markAsUnread(): bool
    {
        return $this->update(['read_at' => null]);
    }
} 