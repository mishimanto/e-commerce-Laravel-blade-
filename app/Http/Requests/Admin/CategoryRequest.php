<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => 'integer|min:0',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'featured_order' => 'nullable|integer|min:0|required_if:is_featured,1',
            'show_in_menu' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ];

        // Unique slug except when updating
        if ($this->isMethod('POST')) {
            $rules['slug'] = 'nullable|string|max:255|unique:categories';
        } else {
            $rules['slug'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($this->category)
            ];
        }

        // Prevent self-parenting
        if ($this->isMethod('PUT') && $this->parent_id) {
            $rules['parent_id'] = [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if ($value == $this->category->id) {
                        $fail('A category cannot be its own parent.');
                    }
                    
                    // Check for circular reference
                    if ($this->category->children->contains('id', $value)) {
                        $fail('Cannot set a child category as parent.');
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
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'slug.unique' => 'This slug has already been taken.',
            'parent_id.exists' => 'Selected parent category does not exist.',
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif, or webp format.',
            'image.max' => 'Image size cannot exceed 2MB.',
        ];
    }


    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();
        
        // Ensure featured_order is 0 if is_featured is false
        if (isset($validated['is_featured']) && !$validated['is_featured']) {
            $validated['featured_order'] = 0;
        }
        
        return $validated;
    }
}