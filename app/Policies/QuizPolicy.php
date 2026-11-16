<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;

class QuizPolicy
{
    /**
     * Determine whether the user can view quizzes of the course.
     */
    public function viewAny(User $user, ?Course $course = null): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $course ? $course->activeEnrollments()->where('user_id', $user->id)->exists() : false;
    }

    /**
     * Determine whether the user can view the quiz landing/details screen.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        $course = $quiz->course;

        if ($user->isAdmin() || ($course && $course->hasInstructor($user))) {
            return true;
        }

        if (! $quiz->is_published) {
            return false;
        }

        return $course ? $course->activeEnrollments()->where('user_id', $user->id)->exists() : false;
    }

    /**
     * Determine whether the user can create quizzes for the course.
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
     * Determine whether the user can update the quiz.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        $course = $quiz->course;

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course->hasInstructor($user) && $course->canInstructorManageContent($user);
    }

    /**
     * Determine whether the user can delete the quiz.
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        $course = $quiz->course;

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $course->hasInstructor($user) && $course->canInstructorManageContent($user);
    }

    /**
     * Determine whether the user can attempt the quiz.
     */
    public function attempt(User $user, Quiz $quiz): bool
    {
        $course = $quiz->course;

        $isEnrolled = $course->activeEnrollments()->where('user_id', $user->id)->exists();
        if (! $isEnrolled) {
            return false;
        }

        return $quiz->canUserAttempt($user);
    }

    /**
     * Determine whether the user can view a specific attempt and results.
     */
    public function viewAttempt(User $user, QuizAttempt $attempt): bool
    {
        // Owner student can view their own attempt
        if ((int) $attempt->user_id === (int) $user->id) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $course = $attempt->quiz->course;

        return $user->isInstructor() && $course->hasInstructor($user);
    }
}
