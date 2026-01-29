<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CourseResource extends Model
{
    protected $fillable = ['course_id', 'title', 'file_path', 'file_type'];

    protected function filePath(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? Storage::url($value) : null);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
