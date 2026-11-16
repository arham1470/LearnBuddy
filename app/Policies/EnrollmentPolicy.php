<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can view any enrollments.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    /**
     * Determine whether the user can view the enrollment confirmation / details.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Student can view only their own enrollment
        if ($user->id === $enrollment->user_id) {
            return true;
        }

        // Assigned instructors of the course can view student enrollments for their course
        return $enrollment->course->hasInstructor($user);
    }

    /**
     * Determine whether the user can update the enrollment.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the enrollment.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can cancel the enrollment.
     */
    public function cancel(User $user, Enrollment $enrollment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Student can cancel their own pending enrollment
        return $user->id === $enrollment->user_id && $enrollment->isPendingPayment();
    }
}
