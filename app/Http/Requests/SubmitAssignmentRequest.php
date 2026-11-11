<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubmitAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Assignment $assignment */
        $assignment = $this->route('assignment');

        $maxKilobytes = $assignment ? ($assignment->max_file_size_mb * 1024) : 10240;

        return [
            'file' => [
                'required',
                'file',
                'max:'.$maxKilobytes,
            ],
            'student_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            /** @var Assignment $assignment */
            $assignment = $this->route('assignment');

            if (! $assignment) {
                return;
            }

            if (! $assignment->canAcceptSubmission()) {
                $validator->errors()->add('file', 'Submissions are closed for this assignment.');

                return;
            }

            if ($this->hasFile('file') && $this->file('file')->isValid()) {
                $file = $this->file('file');
                $ext = strtolower($file->getClientOriginalExtension());
                $allowed = $assignment->allowedFileTypesArray();

                if (! empty($allowed) && ! in_array($ext, $allowed, true)) {
                    $validator->errors()->add(
                        'file',
                        "The uploaded file format (.{$ext}) is not permitted. Allowed formats: ".implode(', ', $allowed)
                    );
                }
            }
        });
    }
}
