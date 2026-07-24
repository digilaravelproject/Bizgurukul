<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'reward_value',
        'reward_type',
        'target_amount',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'reward_value' => 'float',
        'target_amount' => 'float',
    ];

    /**
     * Scope for offers currently in their ACTIVE phase (start_date <= now() <= end_date).
     * These offers MUST be strictly EXCLUDED from standard rewards calculation.
     */
    public function scopeActivePhase($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $now);
            });
    }

    /**
     * Scope for offers in their EXPIRED phase (now() > end_date).
     * These offer details & rewards MUST be automatically INCLUDED back into calculations.
     */
    public function scopeExpiredPhase($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<', $now);
    }

    /**
     * Real-time visual status string.
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Disabled';
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return 'Upcoming';
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return 'Expired';
        }

        return 'Active';
    }

    /**
     * Get offer image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
