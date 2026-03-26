<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralResource extends Model
{
    protected $fillable = ['category_id', 'title', 'link_url', 'icon', 'status', 'order_column'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
