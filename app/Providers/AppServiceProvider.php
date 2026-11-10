<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\CoursePolicy;
use App\Policies\UserPolicy;
use App\Services\Video\VideoProviderInterface;
use App\Services\Video\VideoService;
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
        $this->app->singleton(VideoService::class, function () {
            return new VideoService;
        });

        $this->app->bind(VideoProviderInterface::class, function ($app) {
            return $app->make(VideoService::class)->getProvider();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicitly map policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);

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
