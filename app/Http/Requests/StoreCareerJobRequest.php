<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCareerJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles this
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|image|max:2048',
            'career_job_title_id' => 'required|exists:career_job_titles,id',
            'career_job_location_id' => 'required|exists:career_job_locations,id',
            'career_job_experience_id' => 'required|exists:career_job_experiences,id',
            'career_job_salary_id' => 'nullable|exists:career_job_salaries,id',
            'description' => 'required|string',
            'apply_link' => 'required|url',
            'posted_on' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
            'skills' => 'required|array',
            'skills.*' => 'exists:career_job_skills,id',
        ];
    }
}
