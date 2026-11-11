<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $batch = $this->route('batch');

        return $this->user()?->can('recordAttendance', $batch) ?? false;
    }

    public function rules(): array
    {
        return [
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'topic' => ['nullable', 'string', 'max:255'],
            'room_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}
