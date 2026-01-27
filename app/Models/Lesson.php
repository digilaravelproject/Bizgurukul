<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    // course_id ko yahan add karna zaroori hai
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video_path',
        'hls_path',
        'order_column'
    ];

    public function progress()
    {
        return $this->hasOne(VideoProgress::class)->where('user_id', auth()->id());
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
