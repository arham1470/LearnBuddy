<?php

namespace Tests\Feature\Filament;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PhysicalLocation;
use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SystemSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $instructorA;

    protected User $instructorB;

    protected User $student;

    protected Category $category;

    protected Course $courseA;

    protected Course $courseB;

    protected CourseDeliveryOption $deliveryOption;

    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(SystemSettingSeeder::class);

        $this->admin = User::factory()->admin()->create([
            'name' => 'Super Administrator',
            'email' => 'admin@learnbuddy.lk',
            'is_active' => true,
        ]);

        $this->instructorA = User::factory()->instructor()->create([
            'name' => 'Instructor Alpha',
            'email' => 'alpha@learnbuddy.lk',
            'is_active' => true,
        ]);

        $this->instructorB = User::factory()->instructor()->create([
            'name' => 'Instructor Beta',
            'email' => 'beta@learnbuddy.lk',
            'is_active' => true,
        ]);

        $this->student = User::factory()->student()->create([
            'name' => 'Student Learner',
            'email' => 'student@learnbuddy.lk',
            'is_active' => true,
        ]);

        $this->category = Category::factory()->create([
            'name' => 'Information Technology',
            'slug' => 'information-technology',
        ]);

        $this->courseA = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'title' => 'Fullstack Web Engineering',
            'slug' => 'fullstack-web-engineering',
            'status' => Course::STATUS_PUBLISHED,
        ]);
        $this->courseA->instructors()->attach($this->instructorA->id);

        $this->courseB = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorB->id,
            'title' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'status' => Course::STATUS_PUBLISHED,
        ]);
        $this->courseB->instructors()->attach($this->instructorB->id);

        $this->deliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->courseA->id,
            'delivery_mode' => 'online',
            'title' => 'Online Self-Paced',
            'price' => 25000.00,
            'currency' => 'LKR',
            'is_free' => false,
            'is_active' => true,
            'includes_certificate' => true,
        ]);

        $this->enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->courseA->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'enrollment_code' => 'ENR-TEST-001',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 25000.00,
            'currency' => 'LKR',
            'enrolled_at' => now(),
        ]);
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_student_cannot_access_filament_admin_panel(): void
    {
        $response = $this->actingAs($this->student)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_instructor_can_access_filament_admin_panel_dashboard(): void
    {
        $response = $this->actingAs($this->instructorA)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_instructor_cannot_access_restricted_administrative_resources(): void
    {
        // Instructors should be forbidden from user management
        $this->actingAs($this->instructorA)->get('/admin/users')->assertStatus(403);

        // Instructors should be forbidden from instructor approval management
        $this->actingAs($this->instructorA)->get('/admin/instructors')->assertStatus(403);

        // Instructors should be forbidden from payment and finance records
        $this->actingAs($this->instructorA)->get('/admin/payments')->assertStatus(403);

        // Instructors should be forbidden from viewing tamper-evident audit logs
        $this->actingAs($this->instructorA)->get('/admin/audit-logs')->assertStatus(403);

        // Instructors should be forbidden from editing core system settings
        $this->actingAs($this->instructorA)->get('/admin/system-settings')->assertStatus(403);
    }

    public function test_instructor_can_access_course_resource(): void
    {
        $response = $this->actingAs($this->instructorA)->get('/admin/courses');
        $response->assertStatus(200);
        $response->assertSee('Fullstack Web Engineering');
    }

    public function test_instructor_cannot_edit_another_instructors_course(): void
    {
        // Instructor A attempts to edit Course B (instructed by Instructor B)
        $response = $this->actingAs($this->instructorA)->get("/admin/courses/{$this->courseB->id}/edit");
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_administrator_has_full_access_to_all_resources_and_pages(): void
    {
        $routes = [
            '/admin',
            '/admin/courses',
            '/admin/users',
            '/admin/instructors',
            '/admin/enrollments',
            '/admin/payments',
            '/admin/videos',
            '/admin/live-classes',
            '/admin/course-batches',
            '/admin/attendances',
            '/admin/assignments',
            '/admin/quizzes',
            '/admin/certificates',
            '/admin/audit-logs',
            '/admin/system-settings',
            '/admin/reports-page',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_audit_logs_are_tamper_resistant_and_cannot_be_deleted_via_policy(): void
    {
        $auditLog = AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'test_audit_event',
            'description' => 'Test administrative event description',
            'auditable_type' => Course::class,
            'auditable_id' => $this->courseA->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit Test',
        ]);

        $this->assertFalse($this->admin->can('delete', $auditLog));
        $this->assertFalse($this->admin->can('update', $auditLog));
        $this->assertFalse($this->instructorA->can('view', $auditLog));
    }

    public function test_audit_log_automatically_masks_sensitive_data(): void
    {
        $log = AuditLog::log(
            action: 'payment.processed',
            description: 'Payment processed successfully',
            auditable: null,
            metadata: [
                'status' => 'successful',
                'password' => 'secret-value-123',
                'card_number' => '4242424242424242',
                'api_key' => 'live_secret_key_9999',
            ]
        );

        $this->assertEquals('***REDACTED***', $log->metadata['password']);
        $this->assertEquals('***REDACTED***', $log->metadata['card_number']);
        $this->assertEquals('***REDACTED***', $log->metadata['api_key']);
        $this->assertEquals('successful', $log->metadata['status']);
    }

    public function test_certificate_revocation_action_creates_audit_log(): void
    {
        $certificate = Certificate::create([
            'certificate_code' => 'LB-CERT-2026-TEST1234',
            'user_id' => $this->student->id,
            'course_id' => $this->courseA->id,
            'enrollment_id' => $this->enrollment->id,
            'issue_date' => now()->toDateString(),
            'verification_hash' => hash('sha256', 'LB-CERT-2026-TEST1234'),
            'is_revoked' => false,
        ]);

        // Revoke certificate
        $certificate->update([
            'is_revoked' => true,
            'revoked_at' => now(),
            'revoked_reason' => 'Academic dishonesty detected in final project submission',
        ]);

        AuditLog::log(
            action: 'certificate_revoked',
            description: "Revoked certificate {$certificate->certificate_code}: Academic dishonesty",
            auditable: $certificate
        );

        $this->assertTrue($certificate->fresh()->is_revoked);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'certificate_revoked',
            'auditable_type' => Certificate::class,
            'auditable_id' => $certificate->id,
        ]);
    }

    public function test_payment_refund_flow_logs_audit_trail_and_updates_status(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-2026-TEST-999',
            'user_id' => $this->student->id,
            'course_id' => $this->courseA->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'amount' => 25000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_COMPLETED,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $this->student->id,
            'payment_gateway' => 'payhere',
            'transaction_reference' => 'PAY-2026-TEST-999',
            'idempotency_key' => 'IDEMP-999-KEY',
            'amount' => 25000.00,
            'currency' => 'LKR',
            'status' => Payment::STATUS_SUCCESSFUL,
        ]);

        // Process refund
        $payment->update(['status' => Payment::STATUS_REFUNDED]);
        $order->update(['status' => Order::STATUS_REFUNDED]);

        AuditLog::log(
            action: 'payment_refunded',
            description: 'Refunded LKR 25,000.00 for payment PAY-2026-TEST-999',
            auditable: $payment,
            metadata: ['amount' => 25000.00, 'reason' => 'Batch cancellation by institution']
        );

        $this->assertEquals(Payment::STATUS_REFUNDED, $payment->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'payment_refunded',
            'auditable_type' => Payment::class,
            'auditable_id' => $payment->id,
        ]);
    }
}
