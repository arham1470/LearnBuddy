<?php

namespace App\Http\Requests;

use App\Models\CourseBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        $batch = $this->route('batch');

        return $this->user()?->can('update', $batch) ?? false;
    }

    public function rules(): array
    {
        /** @var CourseBatch $batch */
        $batch = $this->route('batch');
        $minSeats = $batch ? $batch->enrolled_seats : 1;

        return [
            'course_delivery_option_id' => ['required', 'exists:course_delivery_options,id'],
            'location_id' => ['required', 'exists:physical_locations,id'],
            'batch_code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('course_batches', 'batch_code')->ignore($batch)],
            'batch_name' => ['required', 'string', 'max:150'],
            'max_seats' => ['required', 'integer', 'min:'.max(1, $minSeats), 'max:500'],
            'registration_start_date' => ['nullable', 'date'],
            'registration_end_date' => ['nullable', 'date', 'after_or_equal:registration_start_date'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:upcoming,open_for_enrollment,in_progress,completed,cancelled'],
            'change_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
