<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'schedule' => ['nullable', 'array'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:scheduled,cancelled,completed'],
            'facility_id' => ['nullable', 'exists:facilities,id'],
            'animal_id' => ['nullable', 'exists:animals,id'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'recurrence' => ['nullable', 'array'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'requires_booking' => ['nullable', 'boolean'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'age_restriction' => ['nullable', 'string', 'max:255'],
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
            'status.in' => 'The status must be scheduled, cancelled, or completed.',
            'end_time.after' => 'The end time must be after the start time.',
            'facility_id.exists' => 'The selected facility does not exist.',
            'animal_id.exists' => 'The selected animal does not exist.',
        ];
    }
}
