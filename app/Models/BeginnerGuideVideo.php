<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeginnerGuideVideo extends Model
{
    protected $fillable = [
        'title',
        'category',
        'description',
        'resources',
        'video_path',
        'order_column'
    ];

    // Accessor for video URL
    public function getVideoUrlAttribute()
    {
        return $this->video_path ? \Illuminate\Support\Facades\Storage::url($this->video_path) : null;
    }
}
