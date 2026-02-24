<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'achievement_id',
        'status',
        'unlocked_at',
        'claimed_at',
        'admin_notes',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    public function scopeUnlocked($query)
    {
        return $query->where('status', 'unlocked');
    }

    public function scopeClaimed($query)
    {
        return $query->where('status', 'claimed');
    }
}
