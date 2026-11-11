<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\LiveClass;
use App\Models\PhysicalLocation;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Policies\AssignmentPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CourseBatchPolicy;
use App\Policies\CoursePolicy;
use App\Policies\LiveClassPolicy;
use App\Policies\PhysicalLocationPolicy;
use App\Policies\QuizPolicy;
use App\Policies\UserPolicy;
use App\Services\LiveClass\Contracts\LiveClassProviderInterface;
use App\Services\LiveClass\Providers\JitsiLiveClassProvider;
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

        $this->app->singleton(LiveClassProviderInterface::class, function () {
            return new JitsiLiveClassProvider;
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
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(AssignmentSubmission::class, AssignmentPolicy::class);
        Gate::policy(Quiz::class, QuizPolicy::class);
        Gate::policy(QuizAttempt::class, QuizPolicy::class);
        Gate::policy(PhysicalLocation::class, PhysicalLocationPolicy::class);
        Gate::policy(CourseBatch::class, CourseBatchPolicy::class);
        Gate::policy(LiveClass::class, LiveClassPolicy::class);

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
