<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CurriculumController extends Controller
{
    /**
     * Store a new curriculum section for a course.
     */
    public function storeSection(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('update', $course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $maxSort = $course->sections()->max('sort_order') ?? 0;

        $course->sections()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $maxSort + 1,
            'is_published' => true,
        ]);

        return redirect()->route('management.courses.show', $course)
            ->with('status', 'Curriculum section added successfully!');
    }

    /**
     * Delete a curriculum section.
     */
    public function destroySection(CourseSection $section): RedirectResponse
    {
        Gate::authorize('update', $section->course);

        $course = $section->course;
        $section->delete();

        return redirect()->route('management.courses.show', $course)
            ->with('status', 'Section and its lessons removed.');
    }

    /**
     * Store a new lesson under a course section.
     */
    public function storeLesson(Request $request, CourseSection $section): RedirectResponse
    {
        Gate::authorize('update', $section->course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:video,article,live_class,quiz,assignment'],
            'duration_minutes' => ['required', 'integer', 'min:0'],
            'is_preview' => ['nullable', 'boolean'],
            'content' => ['nullable', 'string'],
        ]);

        $maxSort = $section->lessons()->max('sort_order') ?? 0;

        $section->lessons()->create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.uniqid(),
            'type' => $validated['type'],
            'duration_minutes' => $validated['duration_minutes'],
            'is_preview' => $request->boolean('is_preview'),
            'content' => $validated['content'] ?? null,
            'sort_order' => $maxSort + 1,
            'is_published' => true,
        ]);

        return redirect()->route('management.courses.show', $section->course)
            ->with('status', 'Lesson created successfully!');
    }

    /**
     * Delete a lesson.
     */
    public function destroyLesson(Lesson $lesson): RedirectResponse
    {
        Gate::authorize('update', $lesson->section->course);

        $course = $lesson->section->course;
        $lesson->delete();

        return redirect()->route('management.courses.show', $course)
            ->with('status', 'Lesson removed successfully.');
    }
}
