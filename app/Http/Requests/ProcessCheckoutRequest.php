<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'delivery_option_id' => ['required', 'integer', 'exists:course_delivery_options,id'],
            'course_batch_id' => ['nullable', 'integer', 'exists:course_batches,id'],
            'billing_name' => ['required', 'string', 'max:150'],
            'billing_email' => ['required', 'email', 'max:150'],
            'billing_phone' => ['required', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_option_id.required' => 'Please select a course delivery option.',
            'billing_name.required' => 'Your full name is required for payment billing.',
            'billing_email.required' => 'A valid email address is required for payment confirmation.',
            'billing_phone.required' => 'A contact phone number is required.',
        ];
    }
}
