<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_x' => ['nullable', 'numeric', 'between:-180,180'],
            'location_y' => ['nullable', 'numeric', 'between:-90,90'],
            'description' => ['required', 'string'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'facts' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,maintenance'],
            'featured' => ['nullable', 'boolean'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string', 'max:255'],
            'habitat' => ['nullable', 'string', 'max:255'],
            'conservation_status' => ['nullable', 'string', 'max:255'],
            'diet' => ['nullable', 'array'],
            'age' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'feeding_times' => ['nullable', 'array'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The animal name is required.',
            'species.required' => 'The species is required.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'status.in' => 'The status must be active, inactive, or maintenance.',
        ];
    }
}
