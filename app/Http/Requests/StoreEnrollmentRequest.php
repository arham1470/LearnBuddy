<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delivery_option_id' => ['required', 'integer', 'exists:course_delivery_options,id'],
            'course_batch_id' => ['nullable', 'integer', 'exists:course_batches,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'delivery_option_id.required' => 'Please select an online or physical delivery option.',
            'delivery_option_id.exists' => 'The selected delivery option is invalid or no longer active.',
            'course_batch_id.exists' => 'The selected physical batch is invalid or does not exist.',
        ];
    }
}
