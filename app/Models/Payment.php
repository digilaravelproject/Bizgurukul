<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Mass assignment se bachne ke liye fillable fields
    protected $fillable = [
        'user_id',
        'course_id',
        'bundle_id',
        'paymentable_type',
        'paymentable_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'tax_details',
        'total_amount',
        'coupon_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'tax_details' => 'array',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the owning paymentable model (Course, Bundle, CouponPackage, etc.)
     */
    public function paymentable()
    {
        return $this->morphTo();
    }

    /**
     * Payment kis user ne kiya
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Payment kis course ke liye tha
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }
}
