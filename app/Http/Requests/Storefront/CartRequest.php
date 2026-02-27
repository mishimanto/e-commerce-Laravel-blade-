<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
            'attributes' => 'nullable|array',
        ];

        // Variant validation (if provided)
        if ($this->has('variant_id')) {
            $rules['variant_id'] = 'required|exists:product_variants,id';
            
            // Check if variant belongs to product
            $rules['variant_id'] = [
                'required',
                'exists:product_variants,id',
                function ($attribute, $value, $fail) {
                    $variant = \App\Models\ProductVariant::find($value);
                    if ($variant && $variant->product_id != $this->product_id) {
                        $fail('This variant does not belong to the selected product.');
                    }
                }
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'variant_id.required' => 'Variant selection is required.',
            'variant_id.exists' => 'Selected variant does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 100.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check stock availability
            if ($this->has('product_id')) {
                $product = \App\Models\Product::find($this->product_id);
                
                if ($product) {
                    $maxQuantity = $this->has('variant_id') 
                        ? \App\Models\ProductVariant::find($this->variant_id)?->stock 
                        : $product->stock;
                    
                    if ($maxQuantity !== null && $this->quantity > $maxQuantity) {
                        $validator->errors()->add(
                            'quantity', 
                            "Only {$maxQuantity} units are available in stock."
                        );
                    }
                    
                    if ($maxQuantity == 0) {
                        $validator->errors()->add(
                            'product_id', 
                            'This product is out of stock.'
                        );
                    }
                }
            }
        });
    }
}