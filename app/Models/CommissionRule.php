<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Affiliate this rule applies to (optional)
     */
    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    /**
     * Product this rule applies to (optional)
     */
    public function product()
    {
        return $this->morphTo();
    }
}
