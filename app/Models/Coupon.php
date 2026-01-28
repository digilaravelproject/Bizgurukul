<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'expiry_date', 'usage_limit', 'used_count', 'is_active', 'couponable_type', 'couponable_id'];

    public function couponable()
    {
        return $this->morphTo();
    }
}
