<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Models\User;

class CourseAnnouncementPolicy
{
    /**
     * Determine whether the user can view announcements for a course.
     */
    public function viewAny(User $user, Course $course): bool
    {
        return $user->isAdmin()
            || $course->instructors()->where('users.id', $user->id)->exists()
            || $user->isEnrolledIn($course);
    }

    /**
     * Determine whether the user can create announcements for a course.
     */
    public function create(User $user, Course $course): bool
    {
        return $user->isAdmin()
            || $course->instructors()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the announcement.
     */
    public function update(User $user, CourseAnnouncement $announcement): bool
    {
        return $user->isAdmin() || $user->id === $announcement->user_id;
    }

    /**
     * Determine whether the user can delete the announcement.
     */
    public function delete(User $user, CourseAnnouncement $announcement): bool
    {
        return $user->isAdmin() || $user->id === $announcement->user_id;
    }
}
