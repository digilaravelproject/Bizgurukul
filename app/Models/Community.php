<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $fillable = [
        'name',
        'description',
        'link',
        'button_text',
        'group_name',
        'is_active',
        'is_custom',
        'order_index',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_custom' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
