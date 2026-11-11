<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $batch = $this->route('batch');

        return $this->user()?->can('recordAttendance', $batch) ?? false;
    }

    public function rules(): array
    {
        return [
            'attendances' => ['required', 'array'],
            'attendances.*.user_id' => ['required', 'exists:users,id'],
            'attendances.*.status' => ['required', 'string', 'in:present,absent,late,excused'],
            'attendances.*.remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
