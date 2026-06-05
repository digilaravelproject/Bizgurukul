<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerJobClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'career_job_id',
        'user_id',
        'action_type',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(CareerJob::class, 'career_job_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
