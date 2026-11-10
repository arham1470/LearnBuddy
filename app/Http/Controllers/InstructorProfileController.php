<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\View\View;

class InstructorProfileController extends Controller
{
    /**
     * Display the public profile of a verified instructor.
     */
    public function show(User $instructor): View
    {
        // Confirm user is an instructor or has authored courses
        if (! $instructor->hasRole('instructor') && ! $instructor->hasRole('admin') && $instructor->taughtCourses()->doesntExist()) {
            abort(404, 'Instructor profile not found.');
        }

        $instructor->load([
            'roles',
        ]);

        $publishedCourses = Course::query()
            ->published()
            ->where(function ($q) use ($instructor) {
                $q->where('created_by_user_id', $instructor->id)
                    ->orWhereHas('instructors', fn ($iq) => $iq->where('user_id', $instructor->id));
            })
            ->with([
                'category',
                'instructors',
                'deliveryOptions' => fn ($q) => $q->where('is_active', true),
                'publishedReviews',
            ])
            ->latest('published_at')
            ->get();

        return view('instructors.show', compact('instructor', 'publishedCourses'));
    }
}
