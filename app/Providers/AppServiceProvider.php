<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Models\CourseBatch;
use App\Models\LiveClass;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PhysicalLocation;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Review;
use App\Models\User;
use App\Policies\AssignmentPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CertificatePolicy;
use App\Policies\CourseAnnouncementPolicy;
use App\Policies\CourseBatchPolicy;
use App\Policies\CoursePolicy;
use App\Policies\LiveClassPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PhysicalLocationPolicy;
use App\Policies\QuizPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\UserPolicy;
use App\Services\LiveClass\Contracts\LiveClassProviderInterface;
use App\Services\LiveClass\Providers\JitsiLiveClassProvider;
use App\Services\Notifications\Push\FirebasePushNotificationProvider;
use App\Services\Notifications\Push\PushNotificationProviderInterface;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\PaymentManager;
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

        $this->app->singleton(PaymentManager::class, function () {
            return new PaymentManager;
        });

        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            return $app->make(PaymentManager::class)->gateway();
        });

        $this->app->singleton(PushNotificationProviderInterface::class, function () {
            return new FirebasePushNotificationProvider;
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
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(CourseAnnouncement::class, CourseAnnouncementPolicy::class);
        Gate::policy(Certificate::class, CertificatePolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);

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
