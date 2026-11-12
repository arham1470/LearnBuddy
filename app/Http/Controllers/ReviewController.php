<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    /**
     * Submit an authentic student review for an enrolled course.
     */
    public function store(StoreReviewRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('create', [Review::class, $course]);

        $course->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => (int) $request->validated('rating'),
            'title' => $request->validated('title'),
            'comment' => $request->validated('comment'),
            'is_published' => true,
        ]);

        return back()->with('success', 'Thank you! Your course review and rating have been posted.');
    }

    /**
     * Update an existing review authored by the authenticated student.
     */
    public function update(StoreReviewRequest $request, Course $course, Review $review): RedirectResponse
    {
        Gate::authorize('update', $review);

        $review->update([
            'rating' => (int) $request->validated('rating'),
            'title' => $request->validated('title'),
            'comment' => $request->validated('comment'),
        ]);

        return back()->with('success', 'Your review has been updated.');
    }

    /**
     * Delete an existing review.
     */
    public function destroy(Course $course, Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'Your review has been removed.');
    }

    /**
     * Moderate inappropriate review content (Administrator only).
     */
    public function moderate(Request $request, Course $course, Review $review): RedirectResponse
    {
        Gate::authorize('moderate', Review::class);

        $isPublished = (bool) $request->input('is_published', false);
        $review->update(['is_published' => $isPublished]);

        $statusText = $isPublished ? 'published' : 'hidden from public view';

        return back()->with('success', "Review has been {$statusText}.");
    }
}
