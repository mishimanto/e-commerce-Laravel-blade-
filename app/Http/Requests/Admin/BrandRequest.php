<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => 'integer|min:0',
            'status' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ];

        // Unique slug except when updating
        if ($this->isMethod('POST')) {
            $rules['slug'] = 'nullable|string|max:255|unique:brands';
        } else {
            $rules['slug'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brands')->ignore($this->brand)
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Brand name is required.',
            'name.max' => 'Brand name cannot exceed 255 characters.',
            'slug.unique' => 'This slug has already been taken.',
            'website.url' => 'Please enter a valid website URL.',
            'logo.image' => 'File must be an image.',
            'logo.mimes' => 'Logo must be jpeg, png, jpg, gif, or webp format.',
            'logo.max' => 'Logo size cannot exceed 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('status') && $this->status === 'on') {
            $this->merge(['status' => true]);
        }

        if ($this->has('sort_order') && empty($this->sort_order)) {
            $this->merge(['sort_order' => 0]);
        }

        // Add https:// to website if missing
        if ($this->filled('website') && !preg_match('/^https?:\/\//', $this->website)) {
            $this->merge(['website' => 'https://' . $this->website]);
        }
    }
}