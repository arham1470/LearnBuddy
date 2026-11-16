<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\LiveClass;
use App\Models\User;

class LiveClassPolicy
{
    /**
     * Determine whether the user can view a course's live classes list.
     */
    public function viewAny(User $user, ?Course $course = null): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $course ? $course->activeEnrollments()->where('user_id', $user->id)->exists() : false;
    }

    /**
     * Determine whether the user can view the live class details/briefing.
     */
    public function view(User $user, LiveClass $liveClass): bool
    {
        if ($user->isAdmin() || (int) $liveClass->instructor_id === (int) $user->id || $liveClass->course->hasInstructor($user)) {
            return true;
        }

        return $liveClass->course->activeEnrollments()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can schedule live classes.
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
     * Determine whether the user can update or reschedule the live class.
     */
    public function update(User $user, LiveClass $liveClass): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor()
            && ((int) $liveClass->instructor_id === (int) $user->id || $liveClass->course->hasInstructor($user))
            && $liveClass->course->canInstructorManageContent($user);
    }

    /**
     * Determine whether the user can cancel the live class.
     */
    public function cancel(User $user, LiveClass $liveClass): bool
    {
        return $this->update($user, $liveClass);
    }

    /**
     * Determine whether the user can delete the live class record.
     */
    public function delete(User $user, LiveClass $liveClass): bool
    {
        return $this->update($user, $liveClass);
    }

    /**
     * Determine whether the user can join the interactive live meeting room.
     */
    public function join(User $user, LiveClass $liveClass): bool
    {
        if ($liveClass->isCancelled() || $liveClass->isCompleted()) {
            return false;
        }

        // Host / Instructor / Admin
        if ($user->isAdmin() || (int) $liveClass->instructor_id === (int) $user->id || $liveClass->course->hasInstructor($user)) {
            return $liveClass->canBeJoinedBy($user);
        }

        // Student authorization check
        if (! $user->isStudent()) {
            return false;
        }

        return $liveClass->canBeJoinedBy($user);
    }
}
