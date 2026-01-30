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
        // Get the ID for unique check if updating
        $id = $this->input('id');

        return [
            'code'           => 'required|string|max:50|unique:coupons,code,' . $id,
            'coupon_type'    => 'required|in:general,specific',
            'type'           => 'required|in:fixed,percentage',
            'value'          => 'required|numeric|min:0',
            'expiry_date'    => 'nullable|date',
            'usage_limit'    => 'required|integer|min:1',
            'courses'        => 'nullable|array',
            'courses.*'      => 'integer|exists:courses,id',
            'bundles'        => 'nullable|array',
            'bundles.*'      => 'integer|exists:bundles,id',
        ];
    }

    public function messages()
    {
        return [
            'code.unique' => 'This coupon code is already in use.',
            'value.required' => 'Please enter a discount value.',
        ];
    }
}
