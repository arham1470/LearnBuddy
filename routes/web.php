<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\Management\CategoryController;
use App\Http\Controllers\Management\CourseController;
use App\Http\Controllers\Management\CurriculumController;
use App\Http\Controllers\PublicCourseController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [PublicCourseController::class, 'show'])->name('courses.show');
Route::get('/instructors/{instructor}', [InstructorProfileController::class, 'show'])->name('instructors.show');

Route::view('/design-system', 'design-system')->name('design-system');

// Health Check Endpoint
Route::get('/up/database', function () {
    try {
        DB::select('SELECT 1');
        $sslStatus = DB::select("SHOW STATUS LIKE 'Ssl_cipher'");
        $cipher = ! empty($sslStatus) ? ($sslStatus[0]->Value ?? $sslStatus[0]->value ?? null) : null;

        return response()->json([
            'status' => 'healthy',
            'driver' => DB::connection()->getDriverName(),
            'ssl_active' => ! empty($cipher),
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'status' => 'unhealthy',
            'message' => 'Database service unavailable',
        ], 503);
    }
});

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Email verification flow
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // User Profile Endpoint (protected with IDOR policy checks)
    Route::get('/users/{user}/profile', [UserProfileController::class, 'show'])->name('profile.show');

    // Student Dashboard
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');

    // Course Enrollment Flow
    Route::get('/courses/{course:slug}/enroll', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/courses/{course:slug}/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('/enrollments/{enrollment:enrollment_code}', [EnrollmentController::class, 'confirmation'])->name('enrollments.confirmation');

    // Student Learning Interface & Progress Tracking
    Route::get('/learn/courses/{course:slug}', [LearningController::class, 'index'])->name('learning.index');
    Route::get('/learn/courses/{course:slug}/lessons/{lesson:id}', [LearningController::class, 'show'])->name('learning.show');
    Route::post('/learn/courses/{course:slug}/lessons/{lesson:id}/progress', [LearningController::class, 'updateProgress'])->name('learning.progress');
    Route::get('/learn/courses/{course:slug}/lessons/{lesson:id}/resources/{resource:id}/download', [LearningController::class, 'downloadResource'])->name('learning.resources.download');

    /*
    |--------------------------------------------------------------------------
    | Course & Category Management Routes (Admin & Instructor only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,instructor')->prefix('management')->as('management.')->group(function () {
        // Course Management CRUD & Publication Workflow
        Route::resource('courses', CourseController::class);
        Route::post('courses/{course}/status', [CourseController::class, 'transitionStatus'])->name('courses.status');

        // Curriculum Management (Sections & Lessons)
        Route::post('courses/{course}/sections', [CurriculumController::class, 'storeSection'])->name('courses.sections.store');
        Route::delete('sections/{section}', [CurriculumController::class, 'destroySection'])->name('sections.destroy');
        Route::post('sections/{section}/lessons', [CurriculumController::class, 'storeLesson'])->name('sections.lessons.store');
        Route::delete('lessons/{lesson}', [CurriculumController::class, 'destroyLesson'])->name('lessons.destroy');

        // Category Management
        Route::resource('categories', CategoryController::class)->except(['show']);
    });
});
