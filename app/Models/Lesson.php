<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'video_path',
        'hls_path',
        'document_path',
        'thumbnail',
        'order_column'
    ];

    protected $appends = ['lesson_file_url', 'thumbnail_url', 'admin_video_url']; // Added admin_video_url

    // Consistency for thumbnail
    protected function thumbnail(): Attribute
    {
        return Attribute::get(function ($value) {
            if (!$value) return null;
            $path = ltrim($value, '/');
            return str_starts_with($path, 'storage/') ? '/' . $path : '/storage/' . $path;
        });
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail; // The thumbnail attribute already handles the path
    }

    // Get the correct URL based on lesson type
    protected function lessonFileUrl(): Attribute
    {
        return Attribute::get(function () {
            $path = null;
            if ($this->type === 'video') {
                $path = $this->hls_path ?? $this->video_path;
            } else {
                $path = $this->document_path;
            }

            if (!$path) return null;
            $path = ltrim($path, '/');
            return str_starts_with($path, 'storage/') ? '/' . $path : '/storage/' . $path;
        });
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Auth-based progress relationship
    public function progress()
    {
        return $this->hasOne(VideoProgress::class);
    }

    /**
     * Direct MP4 URL for Admin Panel Preview (Bypasses HLS Encryption)
     * Optimized for large files up to 5GB (supports byte-range requests)
     */
    protected function adminVideoUrl(): Attribute
    {
        return Attribute::get(function () {
            $path = $this->video_path; // Fetch the original MP4 path

            if (!$path) return null;

            $path = ltrim($path, '/');
            return str_starts_with($path, 'storage/') ? '/' . $path : '/storage/' . $path;
        });
    }
}
