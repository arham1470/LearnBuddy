<?php

namespace App\Policies;

use App\Models\SystemSetting;
use App\Models\User;

class SystemSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, SystemSetting $setting): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, SystemSetting $setting): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SystemSetting $setting): bool
    {
        return $user->isAdmin();
    }
}
