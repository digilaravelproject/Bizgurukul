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
        'target_type', // bundle, course, all. Was 'type'
        'target_id',
        'expires_at', // Was 'expiry_date'
        'description',
        'is_deleted',
        'clicks', // Was 'click_count'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_deleted' => 'boolean',
        'clicks' => 'integer',
        'target_id' => 'integer',
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
