<?php

namespace App\Policies;

use App\Models\PhysicalLocation;
use App\Models\User;

class PhysicalLocationPolicy
{
    /**
     * Determine whether the user can view any locations.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    /**
     * Determine whether the user can view the location details.
     */
    public function view(User $user, PhysicalLocation $location): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    /**
     * Determine whether the user can create locations.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the location.
     */
    public function update(User $user, PhysicalLocation $location): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the location.
     */
    public function delete(User $user, PhysicalLocation $location): bool
    {
        return $user->isAdmin();
    }
}
