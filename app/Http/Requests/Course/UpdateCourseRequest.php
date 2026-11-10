<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $this->user()->can('update', $course);
    }

    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:280', Rule::unique('courses', 'slug')->ignore($course->id)],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'thumbnail' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'preview_video_url' => ['nullable', 'url', 'max:1024'],
            'difficulty_level' => ['required', 'in:beginner,intermediate,advanced,all_levels'],
            'language' => ['required', 'string', 'max:50'],
            'duration_hours' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'learning_outcomes' => ['nullable', 'array'],
            'learning_outcomes.*' => ['nullable', 'string', 'max:255'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['nullable', 'string', 'max:255'],
            'instructor_ids' => ['nullable', 'array'],
            'instructor_ids.*' => ['exists:users,id'],
            'delivery_modes' => ['required', 'array', 'min:1'],
            'delivery_modes.*' => ['in:online,physical,hybrid'],
            'online_price' => ['nullable', 'numeric', 'min:0'],
            'online_access_days' => ['nullable', 'integer', 'min:1'],
            'physical_price' => ['nullable', 'numeric', 'min:0'],
            'physical_max_capacity' => ['nullable', 'integer', 'min:1'],
            'physical_location_id' => ['nullable', 'exists:physical_locations,id'],
            'hybrid_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_modes.required' => 'Please select at least one delivery option (Online, Physical, or Hybrid).',
            'thumbnail.max' => 'The course thumbnail must not exceed 2MB in size.',
            'thumbnail.mimes' => 'The thumbnail must be a valid image in JPEG, JPG, PNG, or WebP format.',
        ];
    }
}
