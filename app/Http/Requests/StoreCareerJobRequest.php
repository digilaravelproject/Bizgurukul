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
            'career_job_title_id' => 'required|string|max:255',
            'career_job_location_id' => 'required|string|max:255',
            'career_job_experience_id' => 'required|string|max:255',
            'career_job_salary_id' => 'nullable|string|max:255',
            'description' => 'required|string',
            'apply_link' => 'required|url',
            'posted_on' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
            'skills' => 'required|array',
            'skills.*' => 'string|max:255',
        ];
    }
}
