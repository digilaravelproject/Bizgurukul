<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_published', // Publish status
        'demo_video_url', // Demo link
        'price',
        'thumbnail'
    ];

    /**
     * Relationship: Ek Course mein bahut saare Lessons hote hain.
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order_column', 'asc');
    }

    /**
     * Relationship: Ek Course mein bahut saare students enrolled ho sakte hain.
     * (Future use ke liye agar aapne enrollments table banayi)
     */
    public function progress()
    {
        return $this->hasManyThrough(VideoProgress::class, Lesson::class);
    }

    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'bundle_course');
    }

    public function coupons()
    {
        return $this->morphMany(Coupon::class, 'couponable');
    }
}
