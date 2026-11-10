<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Management\CategoryController;
use App\Http\Controllers\Management\CourseController;
use App\Http\Controllers\Management\CurriculumController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

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
