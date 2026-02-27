<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
        $rules = [
            // Contact Information
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            
            // Shipping Address
            'shipping_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
            
            // Shipping Method
            'shipping_method' => 'required|string',
            
            // Payment Method
            'payment_method' => 'required|string|in:cash_on_delivery,sslcommerz,stripe,bkash,nagad',
            
            // Optional
            'notes' => 'nullable|string|max:500',
            'create_account' => 'nullable|boolean',
            'different_billing' => 'nullable|boolean',
        ];

        // Billing address validation (if different from shipping)
        if ($this->boolean('different_billing')) {
            $rules = array_merge($rules, [
                'billing_name' => 'required|string|max:255',
                'billing_address' => 'required|string|max:500',
                'billing_city' => 'required|string|max:100',
                'billing_state' => 'required|string|max:100',
                'billing_zip' => 'required|string|max:20',
                'billing_country' => 'required|string|max:100',
            ]);
        }

        // Password validation for account creation
        if ($this->boolean('create_account')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone number is required.',
            'shipping_name.required' => 'Shipping name is required.',
            'shipping_address.required' => 'Shipping address is required.',
            'shipping_city.required' => 'City is required.',
            'shipping_state.required' => 'State/Division is required.',
            'shipping_zip.required' => 'Postal code is required.',
            'shipping_method.required' => 'Please select a shipping method.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Selected payment method is not available.',
            'password.required' => 'Password is required to create an account.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('create_account') && $this->create_account === 'on') {
            $this->merge(['create_account' => true]);
        }

        if ($this->has('different_billing') && $this->different_billing === 'on') {
            $this->merge(['different_billing' => true]);
        }
    }
}