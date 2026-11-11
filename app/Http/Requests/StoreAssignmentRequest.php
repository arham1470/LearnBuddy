<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized via policy in controller
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'course_section_id' => ['nullable', 'exists:course_sections,id'],
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'instructions' => ['nullable', 'string'],
            'max_points' => ['required', 'integer', 'min:1', 'max:1000'],
            'passing_points' => ['nullable', 'integer', 'min:1', 'lte:max_points'],
            'due_date' => ['nullable', 'date'],
            'allowed_file_types' => ['nullable', 'string', 'max:255'],
            'max_file_size_mb' => ['required', 'integer', 'min:1', 'max:100'],
            'is_published' => ['nullable', 'boolean'],
            'allow_late_submissions' => ['nullable', 'boolean'],
        ];
    }
}
