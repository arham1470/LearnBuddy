<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\User;

class AssignmentPolicy
{
    /**
     * Determine whether the user can view assignments of the course.
     */
    public function viewAny(User $user, ?Course $course = null): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $course ? $course->activeEnrollments()->where('user_id', $user->id)->exists() : false;
    }

    /**
     * Determine whether the user can view the assignment details.
     */
    public function view(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        if ($user->isAdmin() || ($course && $course->hasInstructor($user))) {
            return true;
        }

        if (! $assignment->is_published) {
            return false;
        }

        return $course ? $course->activeEnrollments()->where('user_id', $user->id)->exists() : false;
    }

    /**
     * Determine whether the user can create assignments for the course.
     */
    public function create(User $user, ?Course $course = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor()) {
            return $course ? ($course->hasInstructor($user) && $course->canInstructorManageContent($user)) : true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the assignment.
     */
    public function update(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course->hasInstructor($user) && $course->canInstructorManageContent($user);
    }

    /**
     * Determine whether the user can delete the assignment.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course->hasInstructor($user) && $course->canInstructorManageContent($user);
    }

    /**
     * Determine whether the student can submit a solution for the assignment.
     */
    public function submit(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        // Staff can't submit as students unless explicitly enrolled
        $isEnrolled = $course->activeEnrollments()->where('user_id', $user->id)->exists();
        if (! $isEnrolled) {
            return false;
        }

        return $assignment->canAcceptSubmission();
    }

    /**
     * Determine whether the user can view a specific submission.
     */
    public function viewSubmission(User $user, AssignmentSubmission $submission): bool
    {
        // Owner student can always view their own submission
        if ((int) $submission->user_id === (int) $user->id) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $course = $submission->assignment->course;

        return $user->isInstructor() && $course->hasInstructor($user);
    }

    /**
     * Determine whether the user can grade submissions for this assignment.
     */
    public function grade(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course->hasInstructor($user) && $course->canInstructorGrade($user);
    }
}
