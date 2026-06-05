<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CareerJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_logo',
        'career_job_title_id',
        'career_job_location_id',
        'career_job_experience_id',
        'career_job_salary_id',
        'description',
        'apply_link',
        'posted_on',
        'is_active',
    ];

    protected $casts = [
        'posted_on' => 'date:d M, Y',
        'is_active' => 'boolean',
    ];

    public function title(): BelongsTo
    {
        return $this->belongsTo(CareerJobTitle::class, 'career_job_title_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(CareerJobLocation::class, 'career_job_location_id');
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(CareerJobExperience::class, 'career_job_experience_id');
    }

    public function salary(): BelongsTo
    {
        return $this->belongsTo(CareerJobSalary::class, 'career_job_salary_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(CareerJobSkill::class, 'career_job_career_job_skill');
    }

    public function clicks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CareerJobClick::class, 'career_job_id');
    }

    public function views(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CareerJobClick::class, 'career_job_id')->where('action_type', 'view');
    }

    public function applies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CareerJobClick::class, 'career_job_id')->where('action_type', 'apply');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
