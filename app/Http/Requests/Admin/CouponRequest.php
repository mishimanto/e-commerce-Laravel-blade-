<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['fixed', 'percentage', 'free_shipping'])],
            'value' => 'required_if:type,fixed,percentage|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'applicable_users' => 'nullable|array',
            'excluded_products' => 'nullable|array',
            'excluded_categories' => 'nullable|array',
        ];

        // Unique code except when updating
        if ($this->isMethod('POST')) {
            $rules['code'] = 'nullable|string|max:50|unique:coupons';
        } else {
            $rules['code'] = [
                'nullable',
                'string',
                'max:50',
                Rule::unique('coupons')->ignore($this->coupon)
            ];
        }

        // Validation for percentage type
        if ($this->type === 'percentage') {
            $rules['value'] = 'required|numeric|min:0|max:100';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'code.unique' => 'This coupon code already exists.',
            'value.required_if' => 'Discount value is required.',
            'value.max' => 'Percentage discount cannot exceed 100%.',
            'expires_at.after' => 'Expiry date must be after start date.',
        ];
    }
}