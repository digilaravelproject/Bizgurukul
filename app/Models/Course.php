<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'title',
        'description',
        'is_published',
        'demo_video_url',
        'thumbnail',
        'website_price',
        'affiliate_price',
        'discount_type',
        'discount_value',
        'commission_type',
        'commission_value',
        'final_price',
        'certificate_enabled',
        'certificate_type',
        'completion_percentage',
        'quiz_required',
        'certificate_criteria',
        'completion_threshold'
    ];
    protected $appends = ['thumbnail_url'];
    // Storage agnostic Thumbnail URL
    protected function thumbnail(): Attribute
    {
        return Attribute::get(function ($value) {
            // if (!$value)
            //     return asset('images/default-course.png');
            // return Storage::url($value);
            return $value ? Storage::url($value) : null;
        });
    }

    // Storage agnostic Demo Video URL
    protected function demoVideoUrl(): Attribute
    {
        return Attribute::get(function ($value) {
            if (!$value)
                return null;
            return Storage::url($value);
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order_column', 'asc');
    }
    public function resources()
    {
        return $this->hasMany(CourseResource::class);
    }
    public function bundles()
    {
        return $this->morphToMany(Bundle::class, 'item', 'bundle_items');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user')->withTimestamps();
    }


    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')->withTimestamps();
    }
    public function getThumbnailUrlAttribute()
    {
        // $this->thumbnail already returns Storage::url()
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Check karne ke liye ki current user ne course kharida hai ya nahi
    public function isPurchasedBy($userId)
    {
        // Check direct purchase
        $hasDirectPurchase = $this->payments()->where('user_id', $userId)->where('status', 'success')->exists();
        if ($hasDirectPurchase) {
            return true;
        }

        // Check if user owns any bundle containing this course
        $user = \App\Models\User::find($userId);
        if ($user) {
            foreach ($user->bundles as $bundle) {
                if ($bundle->getAllCoursesFlat()->contains('id', $this->id)) {
                    return true;
                }
            }
        }

        return false;
    }
}
