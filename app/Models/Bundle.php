<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'price', 'thumbnail', 'is_published'];

    /**
     * Relationship: A bundle belongs to many courses.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'bundle_course')->withTimestamps();
    }
}
