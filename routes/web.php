<?php

use App\Http\Controllers\Admin\AdminApprovalController;
use App\Http\Controllers\Admin\AdminAssignmentController;
use App\Http\Controllers\Admin\AdminBatchController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseAnnouncementController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorApplicationController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\LiveClassController;
use App\Http\Controllers\Management\AssignmentManagementController;
use App\Http\Controllers\Management\CategoryController;
use App\Http\Controllers\Management\CourseBatchManagementController;
use App\Http\Controllers\Management\CourseController;
use App\Http\Controllers\Management\CurriculumController;
use App\Http\Controllers\Management\LessonVideoUploadController;
use App\Http\Controllers\Management\LiveClassManagementController;
use App\Http\Controllers\Management\PhysicalLocationController;
use App\Http\Controllers\Management\QuizManagementController;
use App\Http\Controllers\Management\UserApprovalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\PublicCourseController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentBatchController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VideoPlaybackController;
use App\Http\Controllers\Webhook\MuxWebhookController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
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

// Become an Instructor Public Page & Application
Route::get('/become-instructor', [InstructorApplicationController::class, 'create'])->name('become-instructor');
Route::post('/become-instructor', [InstructorApplicationController::class, 'store'])->name('become-instructor.store');

// Public Certificate Verification
Route::get('/certificates/verify/{hash}', [CertificateController::class, 'verify'])->name('certificates.verify');
Route::get('/certificates/verify', [CertificateController::class, 'search'])->name('certificates.search');

Route::view('/design-system', 'design-system')->name('design-system');

// Public Storage Asset Fallback (Ensures uploaded course media displays across local Windows/XAMPP environments)
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/'.$path);
    if (! file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*')->name('storage.local');

// Support & Contact Desk (Public & Authenticated)
Route::get('/support', [SupportController::class, 'create'])->name('support.create');
Route::post('/support', [SupportController::class, 'store'])->name('support.store')->middleware('throttle:10,1');
Route::get('/contact', [SupportController::class, 'create'])->name('contact');

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

    // 6-digit Email OTP Verification Flow
    Route::get('/verify-otp', [OtpVerificationController::class, 'show'])->name('otp.verify.show');
    Route::post('/verify-otp', [OtpVerificationController::class, 'verify'])->name('otp.verify.submit');
    Route::post('/verify-otp/resend', [OtpVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('otp.verify.resend');
    Route::get('/pending-approval', [OtpVerificationController::class, 'pendingApproval'])->name('approval.notice');
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

    // Secure Checkout & Payments
    Route::get('/courses/{course:slug}/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/courses/{course:slug}/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/orders/{order:order_number}/pay', [CheckoutController::class, 'payOrder'])->name('orders.pay');
    Route::get('/orders/{order:order_number}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/orders/{order:order_number}/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // Payment History & Receipts
    Route::get('/my-receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/orders/{order:order_number}/receipt', [ReceiptController::class, 'show'])->name('receipts.show');

    // In-App Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/go', [NotificationController::class, 'readAndRedirect'])->name('notifications.go');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Notification Preferences
    Route::get('/settings/notifications', [NotificationPreferenceController::class, 'edit'])->name('settings.notifications.edit');
    Route::put('/settings/notifications', [NotificationPreferenceController::class, 'update'])->name('settings.notifications.update');

    // Course Announcements
    Route::get('/courses/{course:slug}/announcements', [CourseAnnouncementController::class, 'index'])->name('courses.announcements.index');
    Route::post('/courses/{course:slug}/announcements', [CourseAnnouncementController::class, 'store'])->name('courses.announcements.store');
    Route::delete('/courses/{course:slug}/announcements/{announcement}', [CourseAnnouncementController::class, 'destroy'])->name('courses.announcements.destroy');

    // Student Learning Interface & Progress Tracking
    Route::get('/learn/courses/{course:slug}', [LearningController::class, 'index'])->name('learning.index');
    Route::get('/learn/courses/{course:slug}/lessons/{lesson:id}', [LearningController::class, 'show'])->name('learning.show');
    Route::get('/learn/courses/{course:slug}/lessons/{lesson:id}/playback', [VideoPlaybackController::class, 'getPlaybackData'])->name('learning.playback');
    Route::post('/learn/courses/{course:slug}/lessons/{lesson:id}/progress', [LearningController::class, 'updateProgress'])->name('learning.progress');
    Route::get('/learn/courses/{course:slug}/lessons/{lesson:id}/resources/{resource:id}/download', [LearningController::class, 'downloadResource'])->name('learning.resources.download');

    // Student Assignments & Submissions
    Route::get('/courses/{course:slug}/assignments/{assignment:slug}', [AssignmentController::class, 'show'])->name('courses.assignments.show');
    Route::post('/courses/{course:slug}/assignments/{assignment:slug}/submit', [AssignmentController::class, 'submit'])->name('courses.assignments.submit');
    Route::get('/assignments/submissions/{submission}/download', [AssignmentController::class, 'downloadSubmission'])->name('assignments.submissions.download');

    // Student Quizzes & Attempts
    Route::get('/courses/{course:slug}/quizzes/{quiz:slug}', [QuizController::class, 'show'])->name('courses.quizzes.show');
    Route::post('/courses/{course:slug}/quizzes/{quiz:slug}/start', [QuizController::class, 'start'])->name('courses.quizzes.start');
    Route::get('/courses/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}', [QuizController::class, 'take'])->name('courses.quizzes.take');
    Route::post('/courses/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}/submit', [QuizController::class, 'submit'])->name('courses.quizzes.submit');
    Route::get('/courses/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}/result', [QuizController::class, 'result'])->name('courses.quizzes.result');

    // Student Physical Batch Schedule & Attendance
    Route::get('/batches/{batch}', [StudentBatchController::class, 'show'])->name('student.batches.show');

    // Student Live Classes & Interactive Sessions
    Route::get('/courses/{course:slug}/live-classes', [LiveClassController::class, 'index'])->name('courses.live-classes.index');
    Route::get('/courses/{course:slug}/live-classes/{live_class}', [LiveClassController::class, 'show'])->name('courses.live-classes.show');
    Route::get('/courses/{course:slug}/live-classes/{live_class}/join', [LiveClassController::class, 'join'])->name('courses.live-classes.join');
    Route::post('/courses/{course:slug}/live-classes/{live_class}/end', [LiveClassController::class, 'end'])->name('courses.live-classes.end');

    // Course Certificates
    Route::get('/courses/{course:slug}/certificate/eligibility', [CertificateController::class, 'eligibility'])->name('certificates.eligibility');
    Route::post('/courses/{course:slug}/certificate/claim', [CertificateController::class, 'claim'])->name('certificates.claim');
    Route::get('/certificates/{certificate:certificate_code}', [CertificateController::class, 'show'])->name('certificates.show');

    // Course Reviews & Ratings
    Route::post('/courses/{course:slug}/reviews', [ReviewController::class, 'store'])->name('courses.reviews.store');
    Route::put('/courses/{course:slug}/reviews/{review}', [ReviewController::class, 'update'])->name('courses.reviews.update');
    Route::delete('/courses/{course:slug}/reviews/{review}', [ReviewController::class, 'destroy'])->name('courses.reviews.destroy');
    Route::patch('/courses/{course:slug}/reviews/{review}/moderate', [ReviewController::class, 'moderate'])->name('courses.reviews.moderate');

    /*
    |--------------------------------------------------------------------------
    | Course & Category Management Routes (Admin & Instructor only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,instructor')->prefix('management')->as('management.')->group(function () {
        // Physical Campus Locations (Admin only management)
        Route::resource('locations', PhysicalLocationController::class)->except(['show']);

        // Course Batches, Schedules, and Attendance Management
        Route::get('courses/{course}/batches', [CourseBatchManagementController::class, 'index'])->name('courses.batches.index');
        Route::get('courses/{course}/batches/create', [CourseBatchManagementController::class, 'create'])->name('courses.batches.create');
        Route::post('courses/{course}/batches', [CourseBatchManagementController::class, 'store'])->name('courses.batches.store');
        Route::get('courses/{course}/batches/{batch}', [CourseBatchManagementController::class, 'show'])->name('courses.batches.show');
        Route::get('courses/{course}/batches/{batch}/edit', [CourseBatchManagementController::class, 'edit'])->name('courses.batches.edit');
        Route::put('courses/{course}/batches/{batch}', [CourseBatchManagementController::class, 'update'])->name('courses.batches.update');
        Route::delete('courses/{course}/batches/{batch}', [CourseBatchManagementController::class, 'destroy'])->name('courses.batches.destroy');
        Route::post('courses/{course}/batches/{batch}/cancel', [CourseBatchManagementController::class, 'cancel'])->name('courses.batches.cancel');
        Route::post('courses/{course}/batches/{batch}/schedules', [CourseBatchManagementController::class, 'storeSchedule'])->name('courses.batches.schedules.store');
        Route::delete('courses/{course}/batches/{batch}/schedules/{schedule}', [CourseBatchManagementController::class, 'destroySchedule'])->name('courses.batches.schedules.destroy');
        Route::post('courses/{course}/batches/{batch}/sessions', [CourseBatchManagementController::class, 'storeSession'])->name('courses.batches.sessions.store');
        Route::get('courses/{course}/batches/{batch}/sessions/{session}/attendance', [CourseBatchManagementController::class, 'attendanceSheet'])->name('courses.batches.sessions.attendance');
        Route::post('courses/{course}/batches/{batch}/sessions/{session}/attendance', [CourseBatchManagementController::class, 'recordAttendance'])->name('courses.batches.sessions.attendance.store');

        // Course Management CRUD & Publication Workflow
        Route::resource('courses', CourseController::class);
        Route::post('courses/{course}/status', [CourseController::class, 'transitionStatus'])->name('courses.status');

        // Curriculum Management (Sections & Lessons)
        Route::post('courses/{course}/sections', [CurriculumController::class, 'storeSection'])->name('courses.sections.store');
        Route::delete('sections/{section}', [CurriculumController::class, 'destroySection'])->name('sections.destroy');
        Route::post('sections/{section}/lessons', [CurriculumController::class, 'storeLesson'])->name('sections.lessons.store');
        Route::delete('lessons/{lesson}', [CurriculumController::class, 'destroyLesson'])->name('lessons.destroy');
        Route::post('courses/{course}/lessons/{lesson}/video-upload', [LessonVideoUploadController::class, 'createUploadSession'])->name('lessons.video-upload');
        Route::post('courses/{course}/lessons/{lesson}/video-sync', [LessonVideoUploadController::class, 'syncUploadStatus'])->name('lessons.video-sync');

        // Assignment Management
        Route::get('courses/{course}/assignments', [AssignmentManagementController::class, 'index'])->name('courses.assignments.index');
        Route::get('courses/{course}/assignments/create', [AssignmentManagementController::class, 'create'])->name('courses.assignments.create');
        Route::post('courses/{course}/assignments', [AssignmentManagementController::class, 'store'])->name('courses.assignments.store');
        Route::get('courses/{course}/assignments/{assignment}/edit', [AssignmentManagementController::class, 'edit'])->name('courses.assignments.edit');
        Route::put('courses/{course}/assignments/{assignment}', [AssignmentManagementController::class, 'update'])->name('courses.assignments.update');
        Route::delete('courses/{course}/assignments/{assignment}', [AssignmentManagementController::class, 'destroy'])->name('courses.assignments.destroy');
        Route::get('courses/{course}/assignments/{assignment}/submissions', [AssignmentManagementController::class, 'submissions'])->name('courses.assignments.submissions');
        Route::post('courses/{course}/assignments/{assignment}/submissions/{submission}/grade', [AssignmentManagementController::class, 'grade'])->name('courses.assignments.grade');

        // Quiz Management
        Route::get('courses/{course}/quizzes', [QuizManagementController::class, 'index'])->name('courses.quizzes.index');
        Route::get('courses/{course}/quizzes/create', [QuizManagementController::class, 'create'])->name('courses.quizzes.create');
        Route::post('courses/{course}/quizzes', [QuizManagementController::class, 'store'])->name('courses.quizzes.store');
        Route::get('courses/{course}/quizzes/{quiz}/edit', [QuizManagementController::class, 'edit'])->name('courses.quizzes.edit');
        Route::put('courses/{course}/quizzes/{quiz}', [QuizManagementController::class, 'update'])->name('courses.quizzes.update');
        Route::delete('courses/{course}/quizzes/{quiz}', [QuizManagementController::class, 'destroy'])->name('courses.quizzes.destroy');
        Route::get('courses/{course}/quizzes/{quiz}/questions', [QuizManagementController::class, 'questions'])->name('courses.quizzes.questions');
        Route::post('courses/{course}/quizzes/{quiz}/questions', [QuizManagementController::class, 'storeQuestion'])->name('courses.quizzes.questions.store');
        Route::delete('courses/{course}/quizzes/{quiz}/questions/{question}', [QuizManagementController::class, 'destroyQuestion'])->name('courses.quizzes.questions.destroy');
        Route::get('courses/{course}/quizzes/{quiz}/attempts', [QuizManagementController::class, 'attempts'])->name('courses.quizzes.attempts');

        // Live Class Management
        Route::get('courses/{course}/live-classes', [LiveClassManagementController::class, 'index'])->name('courses.live-classes.index');
        Route::get('courses/{course}/live-classes/create', [LiveClassManagementController::class, 'create'])->name('courses.live-classes.create');
        Route::post('courses/{course}/live-classes', [LiveClassManagementController::class, 'store'])->name('courses.live-classes.store');
        Route::get('courses/{course}/live-classes/{live_class}', [LiveClassManagementController::class, 'show'])->name('courses.live-classes.show');
        Route::get('courses/{course}/live-classes/{live_class}/edit', [LiveClassManagementController::class, 'edit'])->name('courses.live-classes.edit');
        Route::put('courses/{course}/live-classes/{live_class}', [LiveClassManagementController::class, 'update'])->name('courses.live-classes.update');
        Route::post('courses/{course}/live-classes/{live_class}/end', [LiveClassManagementController::class, 'end'])->name('courses.live-classes.end');
        Route::post('courses/{course}/live-classes/{live_class}/cancel', [LiveClassManagementController::class, 'cancel'])->name('courses.live-classes.cancel');
        Route::post('courses/{course}/live-classes/{live_class}/reminder', [LiveClassManagementController::class, 'triggerReminder'])->name('courses.live-classes.reminder');
        Route::delete('courses/{course}/live-classes/{live_class}', [LiveClassManagementController::class, 'destroy'])->name('courses.live-classes.destroy');

        // Category Management
        Route::resource('categories', CategoryController::class)->except(['show']);
    });

    /*
    |--------------------------------------------------------------------------
    | Administrator-Only User Approval Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('management')->as('management.')->group(function () {
        Route::get('users/approvals', [UserApprovalController::class, 'index'])->name('users.approvals');
        Route::post('users/{user}/approve', [UserApprovalController::class, 'approve'])->name('users.approve');
        Route::post('users/{user}/reject', [UserApprovalController::class, 'reject'])->name('users.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | LearnBuddy Super Admin Dashboard & Administration
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->as('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Academic & Courses
        Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        // Campus & Batches
        Route::get('/batches', [AdminBatchController::class, 'index'])->name('batches.index');
        Route::resource('locations', AdminLocationController::class)->except(['show']);

        // Student Learning & Progress
        Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');

        // Assessments & Grading
        Route::get('/assignments', [AdminAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/quizzes', [AdminQuizController::class, 'index'])->name('quizzes.index');

        // Financial & Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');

        // User & Security
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/approvals', [AdminApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{user}/approve', [AdminApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{user}/reject', [AdminApprovalController::class, 'reject'])->name('approvals.reject');

        // Support Desk
        Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');
        Route::post('/support/{supportMessage}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');
        Route::post('/support/{supportMessage}/toggle-status', [AdminSupportController::class, 'toggleStatus'])->name('support.toggle-status');
        Route::delete('/support/{supportMessage}', [AdminSupportController::class, 'destroy'])->name('support.destroy');

        // Analytics & System
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    });
});

/*
|--------------------------------------------------------------------------
| Provider Webhooks (CSRF exempt)
|--------------------------------------------------------------------------
*/
Route::post('/api/webhooks/video/mux', [MuxWebhookController::class, 'handle'])->name('webhooks.video.mux');
Route::post('/api/webhooks/payments/stripe', [PaymentWebhookController::class, 'handleStripe'])->name('webhooks.stripe');
Route::post('/api/webhooks/payments/payhere', [PaymentWebhookController::class, 'handlePayHere'])->name('webhooks.payhere');
Route::post('/api/webhooks/payments/mock', [PaymentWebhookController::class, 'handleMock'])->name('webhooks.mock');
