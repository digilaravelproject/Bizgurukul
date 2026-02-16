<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'from_user_id',
        'to_user_id',
        'transferred_at'
    ];

    protected $casts = [
        'transferred_at' => 'datetime'
    ];

    // Relationships
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
