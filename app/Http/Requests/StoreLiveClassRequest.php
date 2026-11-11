<?php

namespace App\Http\Requests;

use App\Models\LiveClass;
use Illuminate\Foundation\Http\FormRequest;

class StoreLiveClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $this->user()?->can('create', [LiveClass::class, $course]) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'instructor_id' => ['required', 'exists:users,id'],
            'batch_id' => ['nullable', 'exists:course_batches,id'],
            'scheduled_start_time' => ['required', 'date'],
            'scheduled_end_time' => ['required', 'date', 'after:scheduled_start_time'],
            'access_permission' => ['required', 'string', 'in:enrolled_students,batch_students_only,instructors_only'],
            'meeting_password' => ['nullable', 'string', 'max:100'],
        ];
    }
}
