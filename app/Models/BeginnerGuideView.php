<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeginnerGuideView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'seconds',
        'completed',
        'progress_percentage',
        'ip_address',
        'user_agent',
        'last_viewed_at'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'seconds' => 'integer',
        'progress_percentage' => 'integer',
        'last_viewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(BeginnerGuideVideo::class, 'video_id');
    }
}
