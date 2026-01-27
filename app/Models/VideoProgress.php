<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    // Table name define kar dete hain
    protected $table = 'video_progress';

    protected $fillable = ['user_id', 'lesson_id', 'last_watched_second', 'is_completed'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
