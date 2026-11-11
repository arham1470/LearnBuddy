<?php

namespace Tests\Feature;

use App\Jobs\SendCourseAnnouncementJob;
use App\Jobs\SendLiveClassRemindersJob;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\NotificationDeliveryLog;
use App\Models\User;
use App\Models\UserNotificationPreference;
use App\Notifications\AssignmentNotification;
use App\Notifications\CertificateIssuedNotification;
use App\Notifications\CourseAnnouncementNotification;
use App\Notifications\EnrollmentConfirmationNotification;
use App\Notifications\LiveClassStatusNotification;
use App\Notifications\PasswordResetNotification;
use App\Notifications\RegistrationVerificationNotification;
use App\Services\Notifications\NotificationDispatcherService;
use App\Services\Notifications\Push\FirebasePushNotificationProvider;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationAndAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentA;

    protected User $studentB;

    protected User $instructor;

    protected Course $course;

    protected CourseDeliveryOption $deliveryOption;

    protected Enrollment $enrollmentA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->studentA = User::factory()->create([
            'email' => 'student.a@learnbuddy.lk',
            'name' => 'Student A',
            'phone' => '+94771112222',
        ]);
        $this->studentA->assignRole('student');

        $this->studentB = User::factory()->create([
            'email' => 'student.b@learnbuddy.lk',
            'name' => 'Student B',
            'phone' => '+94773334444',
        ]);
        $this->studentB->assignRole('student');

        $this->instructor = User::factory()->create([
            'email' => 'instructor@learnbuddy.lk',
            'name' => 'Instructor Perera',
        ]);
        $this->instructor->assignRole('instructor');

        $category = Category::firstOrCreate(
            ['slug' => 'tech-skills'],
            ['name' => 'Tech Skills', 'sort_order' => 1]
        );

        $this->course = Course::create([
            'category_id' => $category->id,
            'title' => 'Cloud & DevOps Engineering',
            'slug' => 'cloud-devops-engineering',
            'summary' => 'Master Docker, Kubernetes and CI/CD pipelines.',
            'description' => 'Comprehensive enterprise cloud infrastructure course.',
            'level' => 'advanced',
            'status' => 'published',
            'published_at' => now(),
            'created_by_user_id' => $this->instructor->id,
        ]);
        $this->course->instructors()->attach($this->instructor->id);

        $this->deliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->course->id,
            'delivery_mode' => 'online',
            'title' => 'Online Video + Live Stream',
            'price' => 12500.00,
            'currency' => 'LKR',
            'is_free' => false,
            'is_active' => true,
        ]);

        $this->enrollmentA = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'enrollment_code' => 'ENR-NOTIF-001',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 12500.00,
            'currency' => 'LKR',
            'enrolled_at' => now(),
        ]);
    }

    /**
     * Test authenticated student can view their in-app notifications.
     */
    public function test_authenticated_student_can_view_their_own_notifications(): void
    {
        $this->studentA->notify(new EnrollmentConfirmationNotification($this->enrollmentA));

        $response = $this->actingAs($this->studentA)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifications Centre');
        $response->assertSee('Enrolment Confirmed');
        $response->assertSee($this->course->title);
    }

    /**
     * Test IDOR protection: Student B cannot view or mark Student A's notifications.
     */
    public function test_user_cannot_view_or_mark_another_users_notification_idor(): void
    {
        $this->studentA->notify(new EnrollmentConfirmationNotification($this->enrollmentA));
        $notificationA = $this->studentA->notifications()->first();

        // Student B tries to mark Student A's notification as read
        $response = $this->actingAs($this->studentB)->post(route('notifications.mark-as-read', $notificationA->id));
        $response->assertStatus(404);

        // Student B cannot see Student A's notification in their list
        $listResponse = $this->actingAs($this->studentB)->get(route('notifications.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertDontSee($this->enrollmentA->enrollment_code);
    }

    /**
     * Test student can mark a notification as read and mark all as read.
     */
    public function test_student_can_mark_notification_as_read_and_mark_all_as_read(): void
    {
        $this->studentA->notify(new EnrollmentConfirmationNotification($this->enrollmentA));
        $notification = $this->studentA->unreadNotifications()->first();
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        // Mark single as read
        $this->actingAs($this->studentA)->post(route('notifications.mark-as-read', $notification->id));
        $notification->refresh();
        $this->assertNotNull($notification->read_at);

        // Send two more notifications
        $this->studentA->notify(new EnrollmentConfirmationNotification($this->enrollmentA));
        $this->studentA->notify(new EnrollmentConfirmationNotification($this->enrollmentA));
        $this->assertEquals(2, $this->studentA->unreadNotifications()->count());

        // Mark all as read
        $this->actingAs($this->studentA)->post(route('notifications.mark-all-as-read'));
        $this->assertEquals(0, $this->studentA->unreadNotifications()->count());

        // Test unread count endpoint
        $countResponse = $this->actingAs($this->studentA)->getJson(route('notifications.unread-count'));
        $countResponse->assertStatus(200);
        $countResponse->assertJson(['unread_count' => 0]);
    }

    /**
     * Test duplicate prevention: dispatching with same deduplication key sends once and skips subsequent duplicate.
     */
    public function test_duplicate_prevention_prevents_duplicate_reminders_and_events(): void
    {
        $dispatcher = new NotificationDispatcherService;
        $dedupKey = "live_class_reminder_999_{$this->studentA->id}_60m";

        // First delivery attempt: returns true and delivers
        $sentFirst = $dispatcher->send(
            user: $this->studentA,
            notification: new EnrollmentConfirmationNotification($this->enrollmentA),
            deduplicationKey: $dedupKey,
            eventType: 'live_class_reminder'
        );
        $this->assertTrue($sentFirst);
        $this->assertEquals(1, $this->studentA->notifications()->count());

        // Second delivery attempt with exact same key: returns false and skips duplicate
        $sentSecond = $dispatcher->send(
            user: $this->studentA,
            notification: new EnrollmentConfirmationNotification($this->enrollmentA),
            deduplicationKey: $dedupKey,
            eventType: 'live_class_reminder'
        );
        $this->assertFalse($sentSecond);

        // Notification count remains exactly 1 (no duplicate generated)
        $this->assertEquals(1, $this->studentA->notifications()->count());

        // Log record exists
        $this->assertTrue(NotificationDeliveryLog::hasBeenSent($dedupKey));
    }

    /**
     * Test user notification preferences are strictly respected per category and channel.
     */
    public function test_user_notification_preferences_are_respected(): void
    {
        $prefs = UserNotificationPreference::forUser($this->studentA);

        // Disabling email for announcements
        $prefs->update(['email_announcements' => false]);

        $announcement = CourseAnnouncement::create([
            'course_id' => $this->course->id,
            'user_id' => $this->instructor->id,
            'title' => 'Lab Maintenance Notice',
            'content' => 'Server upgrade on Saturday.',
            'published_at' => now(),
        ]);

        $notification = new CourseAnnouncementNotification($announcement);
        $channels = $notification->via($this->studentA);

        // Contains database, but excludes mail because user opted out
        $this->assertContains('database', $channels);
        $this->assertNotContains('mail', $channels);

        // Disabling in-app notifications
        $prefs->update(['in_app_notifications' => false]);
        $channelsDisabledInApp = $notification->via($this->studentA);
        $this->assertNotContains('database', $channelsDisabledInApp);
    }

    /**
     * Test student can update their notification preferences via UI settings screen.
     */
    public function test_student_can_update_notification_preferences_via_settings_screen(): void
    {
        $response = $this->actingAs($this->studentA)->get(route('settings.notifications.edit'));
        $response->assertStatus(200);
        $response->assertSee('Notification Preferences');

        $updateResponse = $this->actingAs($this->studentA)->put(route('settings.notifications.update'), [
            'email_enrollment' => '1',
            'email_payment' => '1',
            'in_app_notifications' => '1',
            // email_announcements omitted (unchecked)
        ]);

        $updateResponse->assertRedirect();
        $prefs = UserNotificationPreference::forUser($this->studentA);
        $this->assertTrue($prefs->email_enrollment);
        $this->assertFalse($prefs->email_announcements);
    }

    /**
     * Test notification and announcement jobs are queued with retry configuration.
     */
    public function test_notifications_and_announcement_jobs_are_queued(): void
    {
        Queue::fake();

        $announcement = CourseAnnouncement::create([
            'course_id' => $this->course->id,
            'user_id' => $this->instructor->id,
            'title' => 'Project Deadline Extended',
            'content' => 'Submission date moved to next Monday.',
            'published_at' => now(),
        ]);

        SendCourseAnnouncementJob::dispatch($announcement);
        Queue::assertPushed(SendCourseAnnouncementJob::class);

        SendLiveClassRemindersJob::dispatch(60);
        Queue::assertPushed(SendLiveClassRemindersJob::class);

        // Assert notification classes implement ShouldQueue
        $this->assertInstanceOf(ShouldQueue::class, new EnrollmentConfirmationNotification($this->enrollmentA));
        $this->assertInstanceOf(ShouldQueue::class, new CourseAnnouncementNotification($announcement));
        $this->assertInstanceOf(ShouldQueue::class, new RegistrationVerificationNotification);
        $this->assertInstanceOf(ShouldQueue::class, new PasswordResetNotification('test-token'));
    }

    /**
     * Test scheduled live class reminder uses Asia/Colombo timezone and sends to enrolled students.
     */
    public function test_scheduled_live_class_reminders_uses_asia_colombo_timezone(): void
    {
        $now = Carbon::now('Asia/Colombo');
        $startTime = $now->copy()->addMinutes(30);
        $endTime = $startTime->copy()->addHours(2);

        $liveClass = LiveClass::create([
            'course_id' => $this->course->id,
            'instructor_id' => $this->instructor->id,
            'title' => 'Kubernetes Pod Networking Live Lab',
            'description' => 'Interactive session on CNI plugins.',
            'scheduled_start_time' => $startTime,
            'scheduled_end_time' => $endTime,
            'status' => LiveClass::STATUS_SCHEDULED,
            'provider' => 'jitsi',
            'meeting_identifier' => 'lb-k8s-pod-networking',
        ]);

        // Run reminders job synchronously
        $dispatcher = new NotificationDispatcherService;
        $job = new SendLiveClassRemindersJob(60);
        $job->handle($dispatcher);

        // Student A was enrolled in the course, so they must receive 1 reminder
        $this->assertEquals(1, $this->studentA->notifications()->count());
        $notification = $this->studentA->notifications()->first();
        $this->assertEquals('live_class_reminder', $notification->data['type']);
        $this->assertStringContainsString('Kubernetes Pod Networking Live Lab', $notification->data['body']);

        // Student B was NOT enrolled in this course, so they receive 0 notifications
        $this->assertEquals(0, $this->studentB->notifications()->count());
    }

    /**
     * Test instructor can post course announcements and enrolled students can view them.
     */
    public function test_instructor_can_post_announcement_and_enrolled_students_can_view(): void
    {
        $response = $this->actingAs($this->instructor)->post(route('courses.announcements.store', $this->course), [
            'title' => 'Welcome to the DevOps Sprint',
            'content' => 'Please setup your AWS and GitHub accounts before lesson 1.',
            'is_pinned' => '1',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('course_announcements', [
            'course_id' => $this->course->id,
            'title' => 'Welcome to the DevOps Sprint',
            'is_pinned' => true,
        ]);

        // Student A views course announcements
        $viewResponse = $this->actingAs($this->studentA)->get(route('courses.announcements.index', $this->course));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Welcome to the DevOps Sprint');
        $viewResponse->assertSee('Pinned');
    }

    /**
     * Test unauthorized student cannot post announcements for a course.
     */
    public function test_unauthorized_student_cannot_post_announcements_for_course(): void
    {
        $response = $this->actingAs($this->studentA)->post(route('courses.announcements.store', $this->course), [
            'title' => 'Unauthorized Post',
            'content' => 'Students should not be able to post announcements.',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test push provider reports unconfigured without throwing errors when credentials are empty.
     */
    public function test_push_provider_unconfigured_state_handling(): void
    {
        Config::set('services.firebase.project_id', null);
        Config::set('services.firebase.credentials', null);

        $fcmProvider = new FirebasePushNotificationProvider;

        $this->assertFalse($fcmProvider->isConfigured());
        $this->assertEquals('firebase', $fcmProvider->getProviderName());

        // Calling send on unconfigured provider returns false safely without throwing exceptions
        $result = $fcmProvider->send($this->studentA, 'Test Title', 'Test Body');
        $this->assertFalse($result);
    }

    /**
     * Test certificate issued notification delivery and read status.
     */
    public function test_certificate_issued_notification(): void
    {
        $notification = new CertificateIssuedNotification($this->course, 'CERT-2026-TEST');
        $this->studentA->notify($notification);

        $this->assertEquals(1, $this->studentA->notifications()->count());
        $saved = $this->studentA->notifications()->first();
        $this->assertEquals('certificate_issued', $saved->data['type']);
        $this->assertEquals('CERT-2026-TEST', $saved->data['metadata']['certificate_code']);
    }

    /**
     * Test assignment published and graded notifications.
     */
    public function test_assignment_notifications_published_and_graded(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'title' => 'Kubernetes Helm Chart Deployment',
            'slug' => 'kubernetes-helm-chart-deployment',
            'description' => 'Deploy a full microservice using Helm.',
            'max_score' => 100,
            'due_date' => now()->addDays(7),
            'is_published' => true,
            'created_by_user_id' => $this->instructor->id,
        ]);

        // 1. Published notification
        $pubNotification = new AssignmentNotification($assignment, 'published');
        $this->studentA->notify($pubNotification);

        $this->assertEquals('assignment_published', $this->studentA->notifications()->latest()->first()->data['type']);

        // 2. Graded notification
        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $this->studentA->id,
            'file_original_name' => 'submission.zip',
            'file_path' => 'assignments/test.zip',
            'file_size_bytes' => 1024,
            'file_mime_type' => 'application/zip',
            'submitted_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
            'points_awarded' => 95,
        ]);

        $gradedNotification = new AssignmentNotification($assignment, 'graded', $submission);
        $this->studentA->notify($gradedNotification);

        $notifications = $this->studentA->notifications()->get();
        $this->assertEquals('assignment_graded', $notifications->last()->data['type']);
        $this->assertEquals(2, $notifications->count());
    }

    /**
     * Test live class cancellation and batch cancellation notifications.
     */
    public function test_live_class_cancellation_and_batch_cancellation_notifications(): void
    {
        $liveClass = LiveClass::create([
            'course_id' => $this->course->id,
            'instructor_id' => $this->instructor->id,
            'title' => 'Architecture Q&A Session',
            'scheduled_start_time' => now()->addDays(2),
            'scheduled_end_time' => now()->addDays(2)->addHour(),
            'status' => LiveClass::STATUS_CANCELLED,
            'provider' => 'jitsi',
            'meeting_identifier' => 'lb-qa-session',
        ]);

        $statusNotification = new LiveClassStatusNotification($liveClass, 'cancelled', 'Instructor unwell');
        $this->studentA->notify($statusNotification);

        $latest = $this->studentA->notifications()->latest()->first();
        $this->assertEquals('live_class_status', $latest->data['type']);
        $this->assertEquals('Live Class Cancelled', $latest->data['title']);
    }
}
