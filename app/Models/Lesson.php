<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['title', 'video_path', 'hls_path', 'order_column'];

    // Relationship: Is lesson ka progress kis user ka kitna hai
    public function progress()
    {
        return $this->hasOne(VideoProgress::class)->where('user_id', auth()->id());
    }
}
