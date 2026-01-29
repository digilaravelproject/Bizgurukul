<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

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

    // Get the correct URL based on lesson type
    protected function lessonFileUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->type === 'video') {
                $path = $this->hls_path ?? $this->video_path;
                return $path ? Storage::url($path) : null;
            }
            return $this->document_path ? Storage::url($this->document_path) : null;
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
}
