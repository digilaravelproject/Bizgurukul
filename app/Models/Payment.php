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
        'razorpay_order_id',
        'razorpay_payment_id',
        'amount',
        'status',
    ];

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
}
