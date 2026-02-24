<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'short_title',
        'target_amount',
        'reward_type',
        'reward_description',
        'reward_image',
        'priority',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getRewardImageUrlAttribute(): ?string
    {
        return $this->reward_image
            ? asset('storage/' . $this->reward_image)
            : null;
    }
}
