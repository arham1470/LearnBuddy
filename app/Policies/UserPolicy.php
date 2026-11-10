<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the authenticated user can view any user records.
     */
    public function viewAny(User $authenticatedUser): bool
    {
        return $authenticatedUser->isAdmin();
    }

    /**
     * Determine whether the authenticated user can view the specific user model.
     * Prevents IDOR (Insecure Direct Object Reference) vulnerabilities.
     */
    public function view(User $authenticatedUser, User $model): bool
    {
        return $authenticatedUser->id === $model->id || $authenticatedUser->isAdmin();
    }

    /**
     * Determine whether the authenticated user can update the specific user model.
     */
    public function update(User $authenticatedUser, User $model): bool
    {
        return $authenticatedUser->id === $model->id || $authenticatedUser->isAdmin();
    }

    /**
     * Determine whether the authenticated user can delete the specific user model.
     * An administrator cannot delete their own account.
     */
    public function delete(User $authenticatedUser, User $model): bool
    {
        return $authenticatedUser->isAdmin() && $authenticatedUser->id !== $model->id;
    }
}
