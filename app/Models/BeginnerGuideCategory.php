<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeginnerGuideCategory extends Model
{
    protected $fillable = ['name', 'slug', 'order_column', 'status'];

    public function videos()
    {
        return $this->hasMany(BeginnerGuideVideo::class, 'category_id')->orderBy('order_column');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }
}
