<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\Gateways\StripeGateway;
use App\Services\Payments\PaymentService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StripeMockGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Course $course;
    protected CourseDeliveryOption $deliveryOption;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        Config::set('payments.default', 'stripe');
        Config::set('payments.gateways.stripe.secret_key', 'sk_test_dummy_key_123');
        Config::set('payments.gateways.stripe.base_url', 'https://mockgateway.com/api/base/stripe-zzrjuo');

        $this->student = User::factory()->create([
            'email' => 'student.stripe@learnbuddy.lk',
            'name' => 'Stripe Student',
            'phone' => '+94770001122',
        ]);
        $this->student->assignRole('student');

        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');

        $category = Category::firstOrCreate(['slug' => 'it'], ['name' => 'Information Technology', 'sort_order' => 1]);

        $this->course = Course::create([
            'category_id' => $category->id,
            'title' => 'Advanced Cloud Architecture',
            'slug' => 'advanced-cloud-architecture',
            'summary' => 'Cloud engineering masterclass',
            'description' => 'Comprehensive cloud syllabus',
            'level' => 'advanced',
            'status' => 'published',
            'published_at' => now(),
            'created_by_user_id' => $instructor->id,
        ]);
        $this->course->instructors()->attach($instructor->id);

        $this->deliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->course->id,
            'delivery_mode' => 'online',
            'title' => 'Online Cloud Access',
            'price' => 5000.00,
            'currency' => 'LKR',
            'is_free' => false,
            'is_active' => true,
            'access_period_days' => 90,
        ]);
    }

    public function test_stripe_gateway_initiates_payment_and_returns_mockgateway_url(): void
    {
        Http::fake([
            'https://mockgateway.com/api/base/stripe-zzrjuo/v1/payment_intents' => Http::response([
                'id' => 'pi_test_12345',
                'amount' => 500000,
                'currency' => 'lkr',
                'status' => 'requires_payment_method',
                'next_action' => [
                    'type' => 'redirect_to_url',
                    'redirect_to_url' => [
                        'return_url' => 'http://127.0.0.1:8000/orders/ORD-TEST-001/success',
                        'url' => 'https://mockgateway.com/pg/pay/hosted-session-uuid',
                    ],
                ],
            ], 200),
        ]);

        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'order_number' => 'ORD-TEST-001',
            'amount' => 5000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
            'billing_name' => $this->student->name,
            'billing_email' => $this->student->email,
        ]);

        $gateway = new StripeGateway(config('payments.gateways.stripe'));
        $data = $gateway->initiatePayment($order);

        $this->assertEquals('https://mockgateway.com/pg/pay/hosted-session-uuid', $data['action_url']);
        $this->assertEquals('GET', $data['method']);
    }

    public function test_stripe_webhook_settles_order_and_activates_enrollment(): void
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'order_number' => 'ORD-STRIPE-WEBHOOK-01',
            'amount' => 5000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
            'billing_name' => $this->student->name,
            'billing_email' => $this->student->email,
        ]);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'enrollment_code' => 'ENR-STRIPE-001',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
        ]);

        $payload = [
            'id' => 'evt_test_123',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_mock_intent_999',
                    'amount' => 500000,
                    'currency' => 'lkr',
                    'status' => 'succeeded',
                    'metadata' => [
                        'order_id' => $order->order_number,
                    ],
                ],
            ],
        ];

        $response = $this->postJson(route('webhooks.stripe'), $payload);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(Payment::STATUS_SUCCESSFUL, $payment->status);
        $this->assertNotNull($payment->receipt_number);

        $enrollment = Enrollment::where('user_id', $this->student->id)->where('course_id', $this->course->id)->first();
        $this->assertEquals(Enrollment::STATUS_ACTIVE, $enrollment->status);
        $this->assertEquals(5000.00, (float) $enrollment->price_paid);
    }

    public function test_checkout_success_client_return_syncs_with_mockgateway(): void
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'order_number' => 'ORD-STRIPE-RETURN-01',
            'amount' => 5000.00,
            'currency' => 'LKR',
            'status' => Order::STATUS_PENDING,
            'billing_name' => $this->student->name,
            'billing_email' => $this->student->email,
        ]);

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'enrollment_code' => 'ENR-STRIPE-RET-001',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_PENDING_PAYMENT,
        ]);

        Http::fake([
            'https://mockgateway.com/api/base/stripe-zzrjuo/v1/payment_intents/pi_confirmed_123' => Http::response([
                'id' => 'pi_confirmed_123',
                'amount' => 500000,
                'currency' => 'lkr',
                'status' => 'succeeded',
                'metadata' => [
                    'order_id' => $order->order_number,
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->student)->get(route('checkout.success', [
            'order' => $order,
            'payment_intent' => 'pi_confirmed_123',
            'redirect_status' => 'succeeded',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Welcome! Enrolment Confirmed');

        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(Payment::STATUS_SUCCESSFUL, $payment->status);
    }

    public function test_direct_card_entry_flow_successfully_settles_and_redirects_to_success(): void
    {
        $response = $this->actingAs($this->student)->post(route('checkout.process', $this->course), [
            'delivery_option_id' => $this->deliveryOption->id,
            'billing_name' => 'Stripe Student',
            'billing_email' => 'student.stripe@learnbuddy.lk',
            'billing_phone' => '+94770001122',
            'payment_flow' => 'card',
            'card_number' => '4242 4242 4242 4242',
            'card_expiry' => '12/28',
            'card_cvc' => '123',
            'card_name' => 'STRIPE STUDENT',
            'simulation_scenario' => 'success',
        ]);

        $order = Order::where('user_id', $this->student->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);

        $response->assertRedirect(route('checkout.success', $order));

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(Payment::STATUS_SUCCESSFUL, $payment->status);
        $this->assertStringContainsString('4242', $payment->payment_method);
        $this->assertNotNull($payment->receipt_number);

        $enrollment = Enrollment::where('user_id', $this->student->id)->where('course_id', $this->course->id)->first();
        $this->assertEquals(Enrollment::STATUS_ACTIVE, $enrollment->status);
    }

    public function test_direct_card_entry_flow_with_declined_card_shows_validation_error(): void
    {
        $response = $this->actingAs($this->student)->post(route('checkout.process', $this->course), [
            'delivery_option_id' => $this->deliveryOption->id,
            'billing_name' => 'Stripe Student',
            'billing_email' => 'student.stripe@learnbuddy.lk',
            'billing_phone' => '+94770001122',
            'payment_flow' => 'card',
            'card_number' => '4000 0000 0000 0002',
            'card_expiry' => '05/27',
            'card_cvc' => '404',
            'card_name' => 'STRIPE STUDENT',
            'simulation_scenario' => 'decline',
        ]);

        $response->assertSessionHasErrors(['card_number']);
    }

    public function test_checkout_screen_renders_payment_details_modal_and_presets(): void
    {
        $response = $this->actingAs($this->student)->get(route('checkout.show', $this->course));

        $response->assertStatus(200);
        $response->assertSee('Payment Details & Card Information');
        $response->assertSee('Test Card Scenarios:');
        $response->assertSee('Approved (4242)');
        $response->assertSee('3D Secure (3022)');
        $response->assertSee('Decline (0002)');
        $response->assertSee('Pay');
        $response->assertSee('Securely');
    }
}
