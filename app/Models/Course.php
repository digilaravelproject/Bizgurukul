<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Kaun-kaun se columns fill ho sakte hain
    protected $fillable = [
        'title',
        'description',
        'thumbnail'
    ];

    /**
     * Relationship: Ek Course mein bahut saare Lessons hote hain.
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order_column', 'asc');
    }

    /**
     * Relationship: Ek Course mein bahut saare students enrolled ho sakte hain.
     * (Future use ke liye agar aapne enrollments table banayi)
     */
    public function progress()
    {
        return $this->hasManyThrough(VideoProgress::class, Lesson::class);
    }
}
