<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'type',
        'target_id',
        'expiry_date',
        'description',
        'is_active',
        'click_count',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'target_id');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'target_id');
    }
}
