<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'string', 'in:single_choice,true_false'],
            'marks' => ['required', 'integer', 'min:1', 'max:100'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.text' => ['required', 'string', 'max:500'],
            'correct_option_index' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $options = $this->input('options', []);
            $correctIndex = (int) $this->input('correct_option_index', -1);

            if (! isset($options[$correctIndex])) {
                $validator->errors()->add('correct_option_index', 'Please specify a valid correct option choice.');
            }
        });
    }
}
