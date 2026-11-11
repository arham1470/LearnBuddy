<?php

namespace App\Http\Requests;

use App\Models\AssignmentSubmission;
use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var AssignmentSubmission $submission */
        $submission = $this->route('submission');
        $maxPoints = $submission && $submission->assignment ? $submission->assignment->max_points : 1000;

        return [
            'points_awarded' => ['required', 'integer', 'min:0', 'max:'.$maxPoints],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
