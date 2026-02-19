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
        'amount',
        'status',
    ];

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
