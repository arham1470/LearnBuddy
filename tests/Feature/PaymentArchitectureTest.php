<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PhysicalLocation;
use App\Models\User;
use App\Services\Payments\Gateways\PayHereGateway;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentA;

    protected User $studentB;

    protected Course $paidCourse;

    protected CourseDeliveryOption $onlineDeliveryOption;

    protected CourseDeliveryOption $physicalDeliveryOption;

    protected CourseBatch $openBatch;

    protected string $merchantId = '1234567';

    protected string $merchantSecret = 'SuperSecretMerchantKey456!';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        // Configure test PayHere credentials
        Config::set('payments.default', 'payhere');
        Config::set('payments.gateways.payhere.merchant_id', $this->merchantId);
        Config::set('payments.gateways.payhere.merchant_secret', $this->merchantSecret);
        Config::set('payments.gateways.payhere.sandbox', true);

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

        $instructor = User::factory()->create(['name' => 'Dr. Perera']);
        $instructor->assignRole('instructor');

        $category = Category::firstOrCreate(
            ['slug' => 'tech-skills'],
            ['name' => 'Tech Skills', 'sort_order' => 1]
        );

        $location = PhysicalLocation::firstOrCreate(
            ['code' => 'COL-MAIN'],
            [
                'name' => 'Colombo Campus Main',
                'province' => 'Western',
                'district' => 'Colombo',
                'city' => 'Colombo 03',
                'address_line_1' => 'Duplication Road',
                'max_capacity' => 40,
                'is_active' => true,
            ]
        );

        $this->paidCourse = Course::create([
            'category_id' => $category->id,
            'title' => 'Fullstack Laravel & Vue Masterclass',
            'slug' => 'fullstack-laravel-vue-masterclass',
            'summary' => 'Comprehensive enterprise web development.',
            'description' => 'Detailed curriculum covering backend, frontend, API security.',
            'level' => 'intermediate',
            'status' => 'published',
            'published_at' => now(),
            'created_by_user_id' => $instructor->id,
        ]);
        $this->paidCourse->instructors()->attach($instructor->id);

        $this->onlineDeliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->paidCourse->id,
            'delivery_mode' => 'online',
            'title' => 'Self-Paced Online Stream',
            'description' => 'Unlimited recorded video & live sessions.',
            'price' => 15000.00,
            'currency' => 'LKR',
            'is_free' => false,
            'is_active' => true,
            'access_period_days' => 365,
            'includes_recorded_lessons' => true,
            'includes_live_classes' => true,
        ]);

        $this->physicalDeliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->paidCourse->id,
            'delivery_mode' => 'physical',
            'title' => 'Colombo In-Person Lab Batch',
            'description' => 'On-campus lab sessions with hands-on mentoring.',
            'price' => 25000.00,
            'currency' => 'LKR',
            'is_free' => false,
            'is_active' => true,
            'access_period_days' => 180,
            'includes_physical_sessions' => true,
        ]);

        $this->openBatch = CourseBatch::create([
            'course_delivery_option_id' => $this->physicalDeliveryOption->id,
            'location_id' => $location->id,
            'batch_code' => 'BAT-COL-2026-01',
            'batch_name' => 'Colombo Weekend Batch',
            'status' => 'open_for_enrollment',
            'max_seats' => 25,
            'enrolled_seats' => 0,
            'start_date' => now()->addDays(14),
            'end_date' => now()->addDays(90),
        ]);
    }

    /**
     * Test authenticated student can access checkout screen with LKR pricing and provider transparency.
     */
    public function test_authenticated_student_can_view_checkout_for_course(): void
    {
        $response = $this->actingAs($this->studentA)->get(route('checkout.show', $this->paidCourse));

        $response->assertStatus(200);
        $response->assertSee('Complete Your');
        $response->assertSee('Enrolment Order');
        $response->assertSee('PayHere');
        $response->assertSee('LKR 15,000.00');
        $response->assertSee('Zero Card Data Storage');
    }

    /**
     * Test student can create order and checkout initiation generates valid PayHere hash & form fields.
     */
    public function test_student_can_create_order_and_initiate_checkout(): void
    {
        $response = $this->actingAs($this->studentA)->post(route('checkout.process', $this->paidCourse), [
            'delivery_option_id' => $this->onlineDeliveryOption->id,
            'billing_name' => 'Student A',
            'billing_email' => 'student.a@learnbuddy.lk',
            'billing_phone' => '+94 77 111 2222',
            'billing_address' => 'Galle Road, Colombo 03',
            'billing_city' => 'Colombo',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('checkout.redirect');

        // Order created in database
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
        ]);

        // Enrollment created with pending_payment status (not active yet)
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
        ]);

        // Audit log created
        $this->assertDatabaseHas('payment_audit_logs', [
            'user_id' => $this->studentA->id,
            'event_type' => 'order_created',
        ]);
    }

    /**
     * Test valid PayHere webhook activates enrollment and completes order server-side.
     */
    public function test_valid_payhere_webhook_activates_enrollment_and_marks_order_completed(): void
    {
        // 1. Student places order
        $order = Order::create([
            'order_number' => 'ORD-TEST-0001',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
            'billing_name' => 'Student A',
            'billing_email' => 'student.a@learnbuddy.lk',
            'billing_phone' => '+94771112222',
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'enrollment_code' => 'ENR-TEST-0001',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
            'is_free' => false,
        ]);
        $order->update(['enrollment_id' => $enrollment->id]);

        // 2. Generate valid PayHere webhook payload
        $payhereGateway = new PayHereGateway(config('payments.gateways.payhere'));
        $paymentId = '320025140500';
        $amountFormatted = '15000.00';
        $statusCode = '2'; // Success
        $currency = 'LKR';

        $md5sig = $payhereGateway->generateIpnHash(
            $this->merchantId,
            $order->order_number,
            $amountFormatted,
            $currency,
            $statusCode,
            $this->merchantSecret
        );

        $payload = [
            'merchant_id' => $this->merchantId,
            'order_id' => $order->order_number,
            'payment_id' => $paymentId,
            'payhere_amount' => $amountFormatted,
            'payhere_currency' => $currency,
            'status_code' => $statusCode,
            'md5sig' => $md5sig,
            'method' => 'VISA',
            'status_message' => 'Successfully completed the payment.',
        ];

        // 3. Post webhook (server-to-server)
        $response = $this->post(route('webhooks.payhere'), $payload);

        $response->assertStatus(200);

        // 4. Verify order completed and enrollment active
        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);

        $enrollment->refresh();
        $this->assertEquals(Enrollment::STATUS_ACTIVE, $enrollment->status);
        $this->assertNotNull($enrollment->enrolled_at);

        // 5. Verify payment recorded with receipt
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(Payment::STATUS_SUCCESSFUL, $payment->status);
        $this->assertEquals($paymentId, $payment->gateway_payment_id);
        $this->assertNotNull($payment->receipt_number);
        $this->assertStringStartsWith('REC-', $payment->receipt_number);

        // 6. Verify audit logs
        $this->assertDatabaseHas('payment_audit_logs', [
            'order_id' => $order->id,
            'event_type' => 'payment_successful',
            'status_before' => Order::STATUS_PENDING,
            'status_after' => Order::STATUS_COMPLETED,
        ]);
    }

    /**
     * Test invalid or forged webhook signature is rejected with 400 and order remains pending.
     */
    public function test_invalid_webhook_signature_is_rejected_and_order_remains_pending(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-0002',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'enrollment_code' => 'ENR-TEST-0002',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
        ]);
        $order->update(['enrollment_id' => $enrollment->id]);

        $forgedPayload = [
            'merchant_id' => $this->merchantId,
            'order_id' => $order->order_number,
            'payment_id' => 'FORGED-999',
            'payhere_amount' => '15000.00',
            'payhere_currency' => 'LKR',
            'status_code' => '2',
            'md5sig' => 'FORGED_INVALID_SIGNATURE_HASH',
            'method' => 'VISA',
        ];

        $response = $this->post(route('webhooks.payhere'), $forgedPayload);

        $response->assertStatus(400);

        $order->refresh();
        $this->assertEquals(Order::STATUS_PENDING, $order->status);

        $enrollment->refresh();
        $this->assertEquals(Enrollment::STATUS_PENDING_PAYMENT, $enrollment->status);

        $this->assertDatabaseHas('payment_audit_logs', [
            'event_type' => 'webhook_signature_failed',
        ]);
    }

    /**
     * Test duplicate / replay webhook event is safely ignored without re-activating or double crediting.
     */
    public function test_duplicate_replay_webhook_is_ignored_idempotently_without_double_activation(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-0003',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'enrollment_code' => 'ENR-TEST-0003',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
        ]);
        $order->update(['enrollment_id' => $enrollment->id]);

        $payhereGateway = new PayHereGateway(config('payments.gateways.payhere'));
        $paymentId = '320025140503';
        $md5sig = $payhereGateway->generateIpnHash(
            $this->merchantId,
            $order->order_number,
            '15000.00',
            'LKR',
            '2',
            $this->merchantSecret
        );

        $payload = [
            'merchant_id' => $this->merchantId,
            'order_id' => $order->order_number,
            'payment_id' => $paymentId,
            'payhere_amount' => '15000.00',
            'payhere_currency' => 'LKR',
            'status_code' => '2',
            'md5sig' => $md5sig,
            'method' => 'VISA',
        ];

        // 1st Webhook delivery: succeeds
        $firstResponse = $this->post(route('webhooks.payhere'), $payload);
        $firstResponse->assertStatus(200);
        $this->assertEquals(1, Payment::where('order_id', $order->id)->count());

        // 2nd Webhook delivery (duplicate / network replay): must be ignored
        $secondResponse = $this->post(route('webhooks.payhere'), $payload);
        $secondResponse->assertStatus(200);

        // Assert payment record count remains exactly 1 (no duplicate insertion)
        $this->assertEquals(1, Payment::where('order_id', $order->id)->count());

        // Assert audit log captures duplicate ignored event
        $this->assertDatabaseHas('payment_audit_logs', [
            'order_id' => $order->id,
            'event_type' => 'duplicate_webhook_ignored',
        ]);
    }

    /**
     * Test amount or currency mismatch is rejected and does not activate enrollment.
     */
    public function test_amount_or_currency_mismatch_is_rejected_and_payment_not_activated(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-0004',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'enrollment_code' => 'ENR-TEST-0004',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
        ]);
        $order->update(['enrollment_id' => $enrollment->id]);

        $payhereGateway = new PayHereGateway(config('payments.gateways.payhere'));
        // Webhook with tampered low amount (100.00 instead of 15000.00)
        $tamperedAmount = '100.00';
        $md5sig = $payhereGateway->generateIpnHash(
            $this->merchantId,
            $order->order_number,
            $tamperedAmount,
            'LKR',
            '2',
            $this->merchantSecret
        );

        $payload = [
            'merchant_id' => $this->merchantId,
            'order_id' => $order->order_number,
            'payment_id' => '320025140504',
            'payhere_amount' => $tamperedAmount,
            'payhere_currency' => 'LKR',
            'status_code' => '2',
            'md5sig' => $md5sig,
            'method' => 'VISA',
        ];

        $response = $this->post(route('webhooks.payhere'), $payload);

        $response->assertStatus(422);

        $order->refresh();
        $this->assertNotEquals(Order::STATUS_COMPLETED, $order->status);

        $enrollment->refresh();
        $this->assertEquals(Enrollment::STATUS_PENDING_PAYMENT, $enrollment->status);

        $this->assertDatabaseHas('payment_audit_logs', [
            'order_id' => $order->id,
            'event_type' => 'amount_or_currency_mismatch',
        ]);
    }

    /**
     * Test IDOR protection: Student B cannot view Student A's receipt or pay Student A's order.
     */
    public function test_unauthorised_student_cannot_view_another_students_order_or_receipt_idor(): void
    {
        $orderA = Order::create([
            'order_number' => 'ORD-TEST-0005',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_COMPLETED,
            'billing_name' => 'Student A',
        ]);

        Payment::create([
            'order_id' => $orderA->id,
            'user_id' => $this->studentA->id,
            'payment_gateway' => 'payhere',
            'transaction_reference' => 'TXN-TEST-0005',
            'gateway_payment_id' => '320025140505',
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Payment::STATUS_SUCCESSFUL,
            'receipt_number' => 'REC-20261111-TEST5',
            'idempotency_key' => 'IDEMP-TEST-5',
        ]);

        // Student B tries to access Student A's receipt
        $response = $this->actingAs($this->studentB)->get(route('receipts.show', $orderA));
        $response->assertStatus(403);

        // Student B tries to pay Student A's order
        $payResponse = $this->actingAs($this->studentB)->get(route('orders.pay', $orderA));
        $payResponse->assertStatus(403);

        // Student A can access their own receipt
        $ownResponse = $this->actingAs($this->studentA)->get(route('receipts.show', $orderA));
        $ownResponse->assertStatus(200);
        $ownResponse->assertSee('REC-20261111-TEST5');
        $ownResponse->assertSee('LKR 15,000.00');
    }

    /**
     * Test failed webhook marks order as failed and keeps enrollment inactive.
     */
    public function test_failed_webhook_marks_order_failed_and_does_not_activate_enrollment(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-0006',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'enrollment_code' => 'ENR-TEST-0006',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
        ]);
        $order->update(['enrollment_id' => $enrollment->id]);

        $payhereGateway = new PayHereGateway(config('payments.gateways.payhere'));
        $md5sig = $payhereGateway->generateIpnHash(
            $this->merchantId,
            $order->order_number,
            '15000.00',
            'LKR',
            '-2', // Failed
            $this->merchantSecret
        );

        $payload = [
            'merchant_id' => $this->merchantId,
            'order_id' => $order->order_number,
            'payment_id' => '320025140506',
            'payhere_amount' => '15000.00',
            'payhere_currency' => 'LKR',
            'status_code' => '-2',
            'md5sig' => $md5sig,
            'method' => 'VISA',
            'status_message' => 'Insufficient funds or card declined.',
        ];

        $response = $this->post(route('webhooks.payhere'), $payload);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::STATUS_FAILED, $order->status);

        $enrollment->refresh();
        $this->assertEquals(Enrollment::STATUS_PENDING_PAYMENT, $enrollment->status);

        $this->assertDatabaseHas('payment_audit_logs', [
            'order_id' => $order->id,
            'event_type' => 'payment_failed',
            'status_after' => Order::STATUS_FAILED,
        ]);
    }

    /**
     * Test refund/chargeback webhook revokes enrollment access.
     */
    public function test_refund_webhook_revokes_enrollment_access(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-0007',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_COMPLETED,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'enrollment_code' => 'ENR-TEST-0007',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
            'enrolled_at' => now(),
        ]);
        $order->update(['enrollment_id' => $enrollment->id]);

        $payhereGateway = new PayHereGateway(config('payments.gateways.payhere'));
        $md5sig = $payhereGateway->generateIpnHash(
            $this->merchantId,
            $order->order_number,
            '15000.00',
            'LKR',
            '-3', // Chargedback
            $this->merchantSecret
        );

        $payload = [
            'merchant_id' => $this->merchantId,
            'order_id' => $order->order_number,
            'payment_id' => '320025140507',
            'payhere_amount' => '15000.00',
            'payhere_currency' => 'LKR',
            'status_code' => '-3',
            'md5sig' => $md5sig,
            'method' => 'VISA',
            'status_message' => 'Dispute / Chargeback processed.',
        ];

        $response = $this->post(route('webhooks.payhere'), $payload);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::STATUS_REFUNDED, $order->status);

        $enrollment->refresh();
        $this->assertEquals(Enrollment::STATUS_CANCELLED, $enrollment->status);

        $this->assertDatabaseHas('payment_audit_logs', [
            'order_id' => $order->id,
            'event_type' => 'payment_refunded',
            'status_after' => Order::STATUS_REFUNDED,
        ]);
    }

    /**
     * Test verification that card numbers and CVV codes are NEVER persisted in any database table.
     */
    public function test_card_numbers_and_cvv_are_never_persisted_in_database(): void
    {
        $orderColumns = Schema::getColumnListing('orders');
        $paymentColumns = Schema::getColumnListing('payments');
        $auditColumns = Schema::getColumnListing('payment_audit_logs');

        $forbidden = ['card_number', 'pan', 'cvv', 'cvc', 'security_code', 'pin', 'card_expiry'];

        foreach ($forbidden as $column) {
            $this->assertNotContains($column, $orderColumns, "Forbidden card column [{$column}] found in orders table.");
            $this->assertNotContains($column, $paymentColumns, "Forbidden card column [{$column}] found in payments table.");
            $this->assertNotContains($column, $auditColumns, "Forbidden card column [{$column}] found in payment_audit_logs table.");
        }
    }

    /**
     * Test mock payment gateway webhook processing for testing/sandbox.
     */
    public function test_mock_gateway_webhook_processing(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-MOCK-0001',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'enrollment_code' => 'ENR-MOCK-0001',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
        ]);
        $order->update(['enrollment_id' => $enrollment->id]);

        $payload = [
            'order_id' => $order->order_number,
            'payment_id' => 'MOCK-PAY-12345',
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => 'successful',
            'signature' => 'valid_mock_signature',
        ];

        $response = $this->post(route('webhooks.mock'), $payload);

        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);
    }

    /**
     * Test student cannot checkout if already actively enrolled in course.
     */
    public function test_student_cannot_checkout_if_already_enrolled(): void
    {
        Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'enrollment_code' => 'ENR-ACTIVE-001',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->studentA)->get(route('checkout.show', $this->paidCourse));
        $response->assertRedirect();
        $response->assertSessionHas('info', 'You already have an active enrollment for this course.');
    }

    /**
     * Test physical batch capacity prevents overbooking during order creation.
     */
    public function test_cannot_create_order_for_fully_booked_physical_batch(): void
    {
        $this->openBatch->update([
            'max_seats' => 20,
            'enrolled_seats' => 20,
        ]);

        $response = $this->actingAs($this->studentA)->post(route('checkout.process', $this->paidCourse), [
            'delivery_option_id' => $this->physicalDeliveryOption->id,
            'course_batch_id' => $this->openBatch->id,
            'billing_name' => 'Student A',
            'billing_email' => 'student.a@learnbuddy.lk',
            'billing_phone' => '+94771112222',
        ]);

        $response->assertSessionHasErrors(['course_batch_id']);
        $this->assertEquals(0, Order::where('user_id', $this->studentA->id)->count());
    }

    /**
     * Test student payment history index lists past orders.
     */
    public function test_student_payment_history_lists_past_orders(): void
    {
        Order::create([
            'order_number' => 'ORD-HIST-0001',
            'user_id' => $this->studentA->id,
            'course_id' => $this->paidCourse->id,
            'course_delivery_option_id' => $this->onlineDeliveryOption->id,
            'amount' => 15000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_COMPLETED,
        ]);

        $response = $this->actingAs($this->studentA)->get(route('receipts.index'));

        $response->assertStatus(200);
        $response->assertSee('ORD-HIST-0001');
        $response->assertSee('LKR 15,000.00');
    }
}
