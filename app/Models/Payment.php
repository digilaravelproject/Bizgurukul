<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment protection
    protected $fillable = [
        'user_id',
        'course_id',
        'bundle_id',
        'paymentable_type',
        'paymentable_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'payment_gateway',
        'gateway_order_id',
        'gateway_payment_id',
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
     * The user who made this payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The course this payment was for
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    /**
     * Get the formatted invoice number (e.g., Dec/25-26/115)
     */
    public function getInvoiceNoAttribute()
    {
        $date = $this->created_at ?? now();
        $month = $date->format('M');

        $year = (int) $date->format('Y');
        $monthNum = (int) $date->format('n');

        if ($monthNum <= 3) {
            $fyStart = $year - 1;
            $fyEnd = $year;
        } else {
            $fyStart = $year;
            $fyEnd = $year + 1;
        }

        $fyRange = substr($fyStart, -2) . '-' . substr($fyEnd, -2);

        // Sequence starts from 115 (ID + 114)
        $sequence = $this->id + 114;

        return "{$month}/{$fyRange}/{$sequence}";
    }
}
