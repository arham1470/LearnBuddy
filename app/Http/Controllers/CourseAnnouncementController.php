<?php

namespace App\Http\Controllers;

use App\Jobs\SendCourseAnnouncementJob;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseAnnouncementController extends Controller
{
    /**
     * Display course announcements for students, instructors, and admins.
     */
    public function index(Course $course): View
    {
        Gate::authorize('viewAny', [CourseAnnouncement::class, $course]);

        $course->load('category');
        $announcements = $course->announcements()
            ->with('author')
            ->recent()
            ->paginate(15);

        $canManage = auth()->user()->isAdmin()
            || $course->instructors()->where('users.id', auth()->id())->exists();

        return view('courses.announcements.index', compact('course', 'announcements', 'canManage'));
    }

    /**
     * Store and broadcast a new course announcement.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('create', [CourseAnnouncement::class, $course]);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $announcement = $course->announcements()->create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_pinned' => (bool) ($validated['is_pinned'] ?? false),
            'published_at' => now(),
        ]);

        // Dispatch queued announcement job to all enrolled students
        SendCourseAnnouncementJob::dispatch($announcement);

        return back()->with('success', 'Announcement published and queued for notification delivery.');
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Course $course, CourseAnnouncement $announcement): RedirectResponse
    {
        Gate::authorize('delete', $announcement);

        $announcement->delete();

        return back()->with('success', 'Announcement deleted successfully.');
    }
}
