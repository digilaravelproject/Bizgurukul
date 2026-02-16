<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Bundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'website_price',
        'affiliate_price',
        'discount_type',
        'discount_value',
        'commission_type',
        'commission_value',
        'final_price',
        'thumbnail',
        'is_published'
    ];

    protected $casts = [
        'website_price' => 'decimal:2',
        'affiliate_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    protected $appends = ['thumbnail_url'];

    protected function thumbnail(): Attribute
    {
        return Attribute::get(function ($value) {
            return $value ? Storage::url($value) : null;
        });
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null; // Logic handled by accessor above actually
    }

    public function courses()
    {
        return $this->morphedByMany(Course::class, 'item', 'bundle_items')
            ->withPivot('order_column')
            ->orderBy('bundle_items.order_column');
    }

    public function childBundles()
    {
        return $this->morphedByMany(Bundle::class, 'item', 'bundle_items')
            ->withPivot('order_column')
            ->orderBy('bundle_items.order_column');
    }

    public function getAllCoursesFlat()
    {
        $allCourses = $this->courses;
        foreach ($this->childBundles as $child) {
            $allCourses = $allCourses->merge($child->getAllCoursesFlat());
        }
        return $allCourses->unique('id');
    }
}
