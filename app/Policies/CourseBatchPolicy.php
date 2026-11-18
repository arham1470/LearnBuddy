<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\User;

class CourseBatchPolicy
{
    /**
     * Determine whether the user can view batches of the course.
     */
    public function viewAny(User $user, ?Course $course = null): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $course ? $course->activeEnrollments()->where('user_id', $user->id)->exists() : false;
    }

    /**
     * Determine whether the user can view the batch details and schedule.
     */
    public function view(User $user, CourseBatch $batch): bool
    {
        $course = $batch->course ?? $batch->deliveryOption?->course;

        if ($user->isAdmin() || ($course && $course->hasInstructor($user))) {
            return true;
        }

        return $batch->enrollments()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create batches for the course.
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
     * Determine whether the user can update the batch.
     */
    public function update(User $user, CourseBatch $batch): bool
    {
        $course = $batch->course ?? $batch->deliveryOption?->course;

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course && $course->hasInstructor($user) && $course->canInstructorManageContent($user);
    }

    /**
     * Determine whether the user can delete the batch.
     */
    public function delete(User $user, CourseBatch $batch): bool
    {
        $course = $batch->course ?? $batch->deliveryOption?->course;

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course && $course->hasInstructor($user) && $course->canInstructorManageContent($user);
    }

    /**
     * Determine whether the user can record attendance for this batch.
     */
    public function recordAttendance(User $user, CourseBatch $batch): bool
    {
        $course = $batch->course ?? $batch->deliveryOption?->course;

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course && $course->hasInstructor($user) && ($course->canInstructorGrade($user) || $course->canInstructorManageContent($user));
    }

    /**
     * Determine whether the user can view attendance records for a student.
     * Enforces strict student privacy.
     */
    public function viewStudentAttendance(User $user, CourseBatch $batch, User $targetStudent): bool
    {
        // Students can only view their own attendance
        if ((int) $user->id === (int) $targetStudent->id) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $course = $batch->course ?? $batch->deliveryOption?->course;

        return $user->isInstructor() && $course && $course->hasInstructor($user);
    }
}
