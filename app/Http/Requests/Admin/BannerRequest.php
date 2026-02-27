<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Banner;

class BannerRequest extends FormRequest
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
        $bannerId = $this->route('banner') ? $this->route('banner')->id : null;
        
        return [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:100',
            'position' => 'required|string|in:' . implode(',', array_keys(Banner::POSITIONS)),
            'type' => 'required|string|in:' . implode(',', array_keys(Banner::TYPES)),
            'target' => 'required|string|in:' . implode(',', array_keys(Banner::TARGETS)),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable|boolean',
            'settings' => 'nullable|json',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Banner title is required',
            'title.max' => 'Title must not exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, webp',
            'image.max' => 'Image size must not exceed 5MB',
            'mobile_image.image' => 'Mobile image must be an image',
            'mobile_image.mimes' => 'Mobile image must be a file of type: jpeg, png, jpg, gif, webp',
            'mobile_image.max' => 'Mobile image size must not exceed 5MB',
            'link.url' => 'Please enter a valid URL',
            'position.in' => 'Invalid position selected',
            'type.in' => 'Invalid type selected',
            'target.in' => 'Invalid target selected',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
            'priority.integer' => 'Priority must be a number',
            'priority.min' => 'Priority must be at least 0',
            'priority.max' => 'Priority must not exceed 999',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('settings') && is_string($this->settings)) {
            $this->merge([
                'settings' => json_decode($this->settings, true)
            ]);
        }

        // Set default values
        $this->merge([
            'is_active' => $this->has('is_active') ? (bool) $this->is_active : true,
            'priority' => $this->priority ?? 0,
        ]);
    }
}