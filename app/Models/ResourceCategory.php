<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceCategory extends Model
{
    protected $fillable = ['name', 'status', 'order_column'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function resources()
    {
        return $this->hasMany(GeneralResource::class, 'category_id')->orderBy('order_column');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
