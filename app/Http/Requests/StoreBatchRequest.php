<?php

namespace App\Http\Requests;

use App\Models\CourseBatch;
use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $this->user()?->can('create', [CourseBatch::class, $course]) ?? false;
    }

    public function rules(): array
    {
        return [
            'course_delivery_option_id' => ['required', 'exists:course_delivery_options,id'],
            'location_id' => ['required', 'exists:physical_locations,id'],
            'batch_code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:course_batches,batch_code'],
            'batch_name' => ['required', 'string', 'max:150'],
            'max_seats' => ['required', 'integer', 'min:1', 'max:500'],
            'registration_start_date' => ['nullable', 'date'],
            'registration_end_date' => ['nullable', 'date', 'after_or_equal:registration_start_date'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:upcoming,open_for_enrollment,in_progress,completed,cancelled'],
        ];
    }
}
