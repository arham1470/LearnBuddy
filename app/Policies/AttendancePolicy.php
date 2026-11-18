<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $attendance->user_id) {
            return true;
        }

        $batch = $attendance->session?->batch;
        $course = $batch?->course ?? $batch?->deliveryOption?->course;

        return $course ? $course->hasInstructor($user) : false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $batch = $attendance->session?->batch;
        $course = $batch?->course ?? $batch?->deliveryOption?->course;

        return $course ? $course->hasInstructor($user) : false;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }
}
