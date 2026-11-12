<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can create a review for the course.
     */
    public function create(User $user, Course $course): bool
    {
        // Must have an active or completed enrollment
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->exists();

        if (! $isEnrolled) {
            return false;
        }

        // Must not already have an existing review (prevent duplicate reviews)
        return ! Review::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can update their review.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin() || $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can moderate the review (hide/publish).
     */
    public function moderate(User $user): bool
    {
        return $user->isAdmin();
    }
}
