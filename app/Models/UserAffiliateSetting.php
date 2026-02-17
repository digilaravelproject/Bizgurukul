<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAffiliateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'can_sell_courses',
        'allowed_bundle_ids',
        'custom_commission_percentage',
    ];

    protected $casts = [
        'can_sell_courses' => 'boolean',
        'allowed_bundle_ids' => 'array',
        'custom_commission_percentage' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
