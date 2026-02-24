<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'short_title' => 'nullable|string|max:50',
            'target_amount' => 'required|numeric|min:0',
            'reward_type' => 'required|in:cash,gift,trip,gadget,custom',
            'reward_description' => 'required|string',
            'reward_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'priority' => 'required|integer|min:0',
            'status' => 'boolean',
        ];
    }
}
