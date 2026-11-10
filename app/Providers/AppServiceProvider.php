<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicitly map policies
        Gate::policy(User::class, UserPolicy::class);

        // Define role-based authorization gates
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('instructor', fn (User $user) => $user->isInstructor() || $user->isAdmin());
        Gate::define('student', fn (User $user) => $user->isStudent());

        // Password security defaults
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });
    }
}
