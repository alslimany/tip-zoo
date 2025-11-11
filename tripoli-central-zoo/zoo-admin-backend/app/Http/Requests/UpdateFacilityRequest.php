<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'location_x' => ['nullable', 'numeric', 'between:-180,180'],
            'location_y' => ['nullable', 'numeric', 'between:-90,90'],
            'description' => ['sometimes', 'string'],
            'opening_hours' => ['nullable', 'array'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:open,closed,maintenance'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'amenities' => ['nullable', 'array'],
            'is_accessible' => ['nullable', 'boolean'],
            'capacity' => ['nullable', 'integer', 'min:0'],
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
            'category_id.exists' => 'The selected category does not exist.',
            'status.in' => 'The status must be open, closed, or maintenance.',
            'contact_email.email' => 'The contact email must be a valid email address.',
        ];
    }
}
