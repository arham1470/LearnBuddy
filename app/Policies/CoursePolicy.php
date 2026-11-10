<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view any courses in the management dashboard.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    /**
     * Determine whether the user can view the specific course management screen.
     */
    public function view(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor() && $course->hasInstructor($user)) {
            return true;
        }

        // Unpublished courses must not be exposed to unauthorized users
        return false;
    }

    /**
     * Determine whether the user can create courses.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    /**
     * Determine whether the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor() && $course->hasInstructor($user) && $course->canInstructorManageContent($user)) {
            // Instructors can freely edit draft and review courses
            return in_array($course->status, [Course::STATUS_DRAFT, Course::STATUS_REVIEW]);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // An instructor can only delete their own draft course
        if ($user->isInstructor() && $course->isPrimaryInstructor($user) && $course->isDraft()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can transition a course to published status.
     */
    public function publish(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can submit a draft course for review.
     */
    public function submitForReview(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course->hasInstructor($user) && $course->isDraft();
    }

    /**
     * Determine whether the user can archive the course.
     */
    public function archive(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course->isPrimaryInstructor($user) && $course->isPublished();
    }
}
