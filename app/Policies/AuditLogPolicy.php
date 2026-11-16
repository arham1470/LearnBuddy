<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, AuditLog $log): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false; // Audit logs are generated programmatically only
    }

    public function update(User $user, AuditLog $log): bool
    {
        return false; // Tamper-resistant: audit logs cannot be edited
    }

    public function delete(User $user, AuditLog $log): bool
    {
        return false; // Tamper-resistant: audit logs cannot be deleted
    }
}
