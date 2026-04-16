<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeginnerGuideVideo extends Model
{
    protected $fillable = [
        'title',
        'category_id',
        'category', // keeping for compatibility until logic updated
        'description',
        'resources',
        'video_path',
        'bunny_video_id',
        'bunny_embed_url',
        'order_column'
    ];

    public function category_rel()
    {
        return $this->belongsTo(BeginnerGuideCategory::class, 'category_id');
    }

    // Accessor for video URL
    public function getVideoUrlAttribute()
    {
        return $this->bunny_video_id ?: ($this->bunny_embed_url ?: ($this->video_path ? \Illuminate\Support\Facades\Storage::url($this->video_path) : null));
    }
}
