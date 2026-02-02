<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id;

        return [
            'code' => 'required|string|max:50|unique:coupons,code,' . $id,
            'coupon_type' => 'required|in:general,specific',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'usage_limit' => 'required|integer|min:1',
            'courses' => 'nullable|array|required_if:coupon_type,specific,bundles,null',
            'courses.*' => 'exists:courses,id',
            'bundles' => 'nullable|array|required_if:coupon_type,specific,courses,null',
            'bundles.*' => 'exists:bundles,id',
        ];
    }

    public function messages()
    {
        return [
            'courses.required_if' => 'Please select at least one course or bundle for specific coupons.',
            'bundles.required_if' => 'Please select at least one course or bundle for specific coupons.',
        ];
    }
}
