<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLiveClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        $liveClass = $this->route('live_class');

        return $this->user()?->can('update', $liveClass) ?? false;
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
            'status' => ['required', 'string', 'in:scheduled,live,completed,cancelled'],
            'meeting_password' => ['nullable', 'string', 'max:100'],
        ];
    }
}
