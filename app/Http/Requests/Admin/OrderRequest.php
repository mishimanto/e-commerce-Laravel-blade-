<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                'pending', 'processing', 'confirmed', 'shipped', 
                'delivered', 'completed', 'cancelled', 'refunded', 'failed'
            ])],
            'payment_status' => ['required', Rule::in([
                'pending', 'paid', 'failed', 'refunded', 'cancelled'
            ])],
            'tracking_number' => 'nullable|string|max:100',
            'shipping_courier' => 'nullable|string|max:100',
            'admin_notes' => 'nullable|string',
            'estimated_delivery' => 'nullable|date|after:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Order status is required.',
            'status.in' => 'Invalid order status selected.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status selected.',
            'estimated_delivery.after' => 'Estimated delivery must be a future date.',
        ];
    }
}