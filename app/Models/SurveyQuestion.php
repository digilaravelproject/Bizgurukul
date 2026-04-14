<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'question',
        'type',
        'is_active',
        'is_required',
    ];

    public function options()
    {
        return $this->hasMany(SurveyQuestionOption::class, 'question_id');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'question_id');
    }
}
