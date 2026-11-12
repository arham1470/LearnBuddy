<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Determine whether the user can view the certificate document.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin()
            || $user->id === $certificate->user_id
            || $certificate->course->instructors()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can revoke the certificate.
     */
    public function revoke(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin();
    }
}
