<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'price',
        'discount_price',
        'used_count',
        'is_active',
        'couponable_type',
        'couponable_id',
        'selected_courses',
        'selected_bundles'
    ];

    protected $casts = [
        'selected_courses' => 'array',
        'selected_bundles' => 'array',
        'is_active' => 'boolean',
        'price' => 'float',
        'discount_price' => 'float',
    ];
}
