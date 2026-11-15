<?php

namespace App\Services\Payments;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentAuditLog;
use App\Models\User;
use App\Notifications\EnrollmentConfirmationNotification;
use App\Notifications\PaymentConfirmationNotification;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        protected PaymentManager $paymentManager
    ) {}

    /**
     * Create or retrieve a pending Order for a user enrolling in a course delivery option.
     *
     * @param  array<string, mixed>  $billingDetails
     *
     * @throws ValidationException
     */
    public function createOrderForEnrollment(
        User $user,
        Course $course,
        CourseDeliveryOption $deliveryOption,
        ?CourseBatch $batch = null,
        array $billingDetails = []
    ): Order {
        if (! $course->isPublished()) {
            throw ValidationException::withMessages([
                'course' => 'This course is currently not published for enrollment.',
            ]);
        }

        if ($deliveryOption->course_id !== $course->id || ! $deliveryOption->is_active) {
            throw ValidationException::withMessages([
                'delivery_option_id' => 'The selected delivery option is invalid or inactive.',
            ]);
        }

        // Check if user already has an active enrollment
        $activeEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', Enrollment::STATUS_ACTIVE)
            ->first();

        if ($activeEnrollment) {
            throw ValidationException::withMessages([
                'enrollment' => 'You are already enrolled in this course.',
            ]);
        }

        return DB::transaction(function () use ($user, $course, $deliveryOption, $batch, $billingDetails) {
            // Physical batch validation and locking if applicable
            if ($deliveryOption->delivery_mode === 'physical' || $batch) {
                if (! $batch) {
                    throw ValidationException::withMessages([
                        'course_batch_id' => 'A valid physical campus batch must be selected.',
                    ]);
                }

                $lockedBatch = CourseBatch::where('id', $batch->id)
                    ->where('course_delivery_option_id', $deliveryOption->id)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedBatch) {
                    throw ValidationException::withMessages([
                        'course_batch_id' => 'The selected physical batch is not valid for this delivery option.',
                    ]);
                }

                if ($lockedBatch->isFull() || $lockedBatch->enrolled_seats >= $lockedBatch->max_seats) {
                    throw ValidationException::withMessages([
                        'course_batch_id' => "Physical batch '{$lockedBatch->batch_code}' is fully booked.",
                    ]);
                }

                if ($lockedBatch->status !== 'open_for_enrollment') {
                    throw ValidationException::withMessages([
                        'course_batch_id' => 'The selected physical batch is not open for enrollment.',
                    ]);
                }
            }

            // Delivery option max capacity check
            if ($deliveryOption->max_capacity) {
                $activeCount = Enrollment::where('course_delivery_option_id', $deliveryOption->id)
                    ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_PENDING_PAYMENT])
                    ->lockForUpdate()
                    ->count();

                if ($activeCount >= $deliveryOption->max_capacity) {
                    throw ValidationException::withMessages([
                        'delivery_option_id' => 'This delivery option has reached maximum capacity.',
                    ]);
                }
            }

            // Check or create pending enrollment
            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_PENDING_PAYMENT,
                ],
                [
                    'course_delivery_option_id' => $deliveryOption->id,
                    'course_batch_id' => $batch?->id,
                    'enrollment_code' => 'ENR-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                    'delivery_mode' => $deliveryOption->delivery_mode,
                    'price_paid' => (float) $deliveryOption->price,
                    'currency' => $deliveryOption->currency ?: 'LKR',
                    'is_free' => false,
                ]
            );

            // Create Order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_delivery_option_id' => $deliveryOption->id,
                'course_batch_id' => $batch?->id,
                'enrollment_id' => $enrollment->id,
                'amount' => (float) $deliveryOption->price,
                'currency' => strtoupper($deliveryOption->currency ?: 'LKR'),
                'status' => Order::STATUS_PENDING,
                'billing_name' => $billingDetails['billing_name'] ?? $user->name,
                'billing_email' => $billingDetails['billing_email'] ?? $user->email,
                'billing_phone' => $billingDetails['billing_phone'] ?? $user->phone,
                'notes' => $billingDetails['notes'] ?? null,
            ]);

            PaymentAuditLog::logEvent(
                eventType: 'order_created',
                order: $order,
                statusBefore: null,
                statusAfter: Order::STATUS_PENDING,
                payload: [
                    'amount' => $order->amount,
                    'currency' => $order->currency,
                    'delivery_mode' => $deliveryOption->delivery_mode,
                    'batch_id' => $batch?->id,
                ],
                userId: $user->id
            );

            return $order;
        });
    }

    /**
     * Initiate checkout with the configured payment gateway.
     *
     * @return array<string, mixed>
     */
    public function initiateCheckout(Order $order, ?string $gatewayName = null): array
    {
        $gateway = $this->paymentManager->gateway($gatewayName);

        $customer = [
            'name' => $order->billing_name ?: $order->user->name,
            'email' => $order->billing_email ?: $order->user->email,
            'phone' => $order->billing_phone ?: $order->user->phone,
            'address' => $order->user->address_line_1 ?: 'Sri Lanka',
            'city' => $order->user->city ?: 'Colombo',
        ];

        $checkoutData = $gateway->initiatePayment($order, $customer);

        PaymentAuditLog::logEvent(
            eventType: 'checkout_initiated',
            order: $order,
            statusBefore: $order->status,
            statusAfter: $order->status,
            payload: [
                'gateway' => $gateway->getGatewayName(),
                'is_sandbox' => $gateway->isSandbox(),
                'action_url' => $checkoutData['action_url'],
            ],
            userId: $order->user_id
        );

        return [
            'gateway' => $gateway->getGatewayName(),
            'is_sandbox' => $gateway->isSandbox(),
            'is_configured' => $gateway->isConfigured(),
            'action_url' => $checkoutData['action_url'],
            'method' => $checkoutData['method'],
            'fields' => $checkoutData['fields'],
        ];
    }

    /**
     * Directly query the gateway for payment status and settle the order if confirmed.
     * Useful for seamless client-return verification (e.g. Stripe / MockGateway redirect).
     */
    public function syncAndSettleOrderFromGateway(Order $order, string $gatewayPaymentId, ?string $gatewayName = null): bool
    {
        $gatewayName = $gatewayName ?: (string) config('payments.default', 'stripe');
        $gateway = $this->paymentManager->gateway($gatewayName);

        if ($gateway instanceof \App\Services\Payments\Gateways\StripeGateway) {
            $intent = $gateway->getPaymentIntent($gatewayPaymentId);

            if (! $intent) {
                return false;
            }

            $status = strtolower((string) ($intent['status'] ?? ''));

            if ($status === 'succeeded') {
                $payload = [
                    'data' => [
                        'object' => array_merge($intent, [
                            'metadata' => array_merge($intent['metadata'] ?? [], [
                                'order_id' => $order->order_number,
                            ]),
                        ]),
                    ],
                    'type' => 'payment_intent.succeeded',
                ];

                $result = $this->processWebhook(
                    payload: $payload,
                    gatewayName: 'stripe',
                    ipAddress: '127.0.0.1',
                    userAgent: 'ClientReturnVerification/1.0'
                );

                return $result['status'] === 'success' || ($result['code'] ?? 0) === 200;
            }
        }

        return false;
    }

    /**
     * Settle a direct card payment (for in-app card demo or direct checkout).
     */
    public function recordSuccessfulDirectPayment(
        Order $order,
        string $paymentMethod = 'card',
        string $last4 = '4242',
        ?string $gatewayName = null
    ): Payment {
        $gatewayName = $gatewayName ?: (string) config('payments.default', 'stripe');

        return DB::transaction(function () use ($order, $paymentMethod, $last4, $gatewayName) {
            $statusBefore = $order->status;
            $receiptNumber = Payment::generateReceiptNumber();
            $fakePaymentId = 'pi_demo_'.Str::random(16);
            $transactionRef = 'TXN-'.strtoupper(Str::random(10));
            $idempotencyKey = 'IDEMP-'.$gatewayName.'-'.$fakePaymentId.'-successful';

            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'payment_gateway' => $gatewayName,
                'transaction_reference' => $transactionRef,
                'gateway_payment_id' => $fakePaymentId,
                'amount' => $order->amount,
                'currency' => $order->currency,
                'status' => Payment::STATUS_SUCCESSFUL,
                'payment_method' => $paymentMethod.' (•••• '.$last4.')',
                'receipt_number' => $receiptNumber,
                'gateway_response_code' => 'succeeded',
                'gateway_response_message' => 'Direct Card Payment Authorized',
                'idempotency_key' => $idempotencyKey,
                'raw_payload' => [
                    'last4' => $last4,
                    'brand' => $paymentMethod,
                    'auth_mode' => '3ds_verified',
                    'authorized_at' => now()->toIso8601String(),
                ],
                'paid_at' => now(),
            ]);

            $order->update(['status' => Order::STATUS_COMPLETED]);

            // Activate Enrollment
            $enrollment = $order->enrollment_id
                ? Enrollment::find($order->enrollment_id)
                : Enrollment::where('user_id', $order->user_id)->where('course_id', $order->course_id)->first();

            if ($enrollment) {
                $expiresAt = ($order->deliveryOption && $order->deliveryOption->access_period_days)
                    ? now()->addDays($order->deliveryOption->access_period_days)
                    : null;

                $enrollment->update([
                    'status' => Enrollment::STATUS_ACTIVE,
                    'price_paid' => $order->amount,
                    'currency' => $order->currency,
                    'enrolled_at' => now(),
                    'expires_at' => $expiresAt,
                ]);

                if (! $order->enrollment_id) {
                    $order->update(['enrollment_id' => $enrollment->id]);
                }

                if ($order->course_batch_id && $order->batch) {
                    $order->batch->increment('enrolled_seats');
                }
            }

            PaymentAuditLog::logEvent(
                eventType: 'payment_successful',
                order: $order,
                payment: $payment,
                statusBefore: $statusBefore,
                statusAfter: Order::STATUS_COMPLETED,
                payload: ['last4' => $last4, 'method' => $paymentMethod],
                userId: $order->user_id,
                ipAddress: request()->ip(),
                userAgent: request()->userAgent()
            );

            return $payment;
        });
    }

    /**
     * Process an incoming server-to-server webhook / IPN callback safely with row locks and idempotency.
     *
     * @param  array<string, mixed>  $payload
     * @return array{status: string, code: int, message: string, order?: Order, payment?: Payment}
     */
    public function processWebhook(
        array $payload,
        ?string $gatewayName = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): array {
        $gateway = $this->paymentManager->gateway($gatewayName);

        // 1. Signature Verification
        if (! $gateway->verifyWebhookSignature($payload)) {
            PaymentAuditLog::logEvent(
                eventType: 'webhook_signature_failed',
                payload: ['payload' => $payload, 'gateway' => $gateway->getGatewayName()],
                ipAddress: $ipAddress,
                userAgent: $userAgent
            );

            return [
                'status' => 'rejected',
                'code' => 400,
                'message' => 'Invalid webhook signature.',
            ];
        }

        // 2. Parse payload
        $parsed = $gateway->parseWebhookPayload($payload);
        $orderNumber = $parsed['order_number'];

        // 3. Database transaction with row lock
        return DB::transaction(function () use ($parsed, $gateway, $orderNumber, $ipAddress, $userAgent) {
            $order = Order::where('order_number', $orderNumber)
                ->lockForUpdate()
                ->first();

            if (! $order) {
                PaymentAuditLog::logEvent(
                    eventType: 'order_not_found',
                    payload: $parsed,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );

                return [
                    'status' => 'not_found',
                    'code' => 404,
                    'message' => "Order [{$orderNumber}] not found.",
                ];
            }

            // 4. Idempotency & Duplicate Check
            $existingPayment = Payment::where('order_id', $order->id)
                ->where('gateway_payment_id', $parsed['gateway_payment_id'])
                ->first();

            if ($existingPayment && $existingPayment->status === $parsed['status']) {
                PaymentAuditLog::logEvent(
                    eventType: 'duplicate_webhook_ignored',
                    order: $order,
                    payment: $existingPayment,
                    statusBefore: $order->status,
                    statusAfter: $order->status,
                    payload: $parsed,
                    userId: $order->user_id,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );

                return [
                    'status' => 'ignored',
                    'code' => 200,
                    'message' => 'Duplicate webhook event ignored.',
                    'order' => $order,
                    'payment' => $existingPayment,
                ];
            }

            // 5. Amount and Currency Tamper Protection
            $amountDifference = abs((float) $order->amount - (float) $parsed['amount']);
            if ($amountDifference > 0.01 || strtoupper($order->currency) !== strtoupper($parsed['currency'])) {
                PaymentAuditLog::logEvent(
                    eventType: 'amount_or_currency_mismatch',
                    order: $order,
                    statusBefore: $order->status,
                    statusAfter: Order::STATUS_FAILED,
                    payload: array_merge($parsed, [
                        'order_amount' => $order->amount,
                        'order_currency' => $order->currency,
                    ]),
                    userId: $order->user_id,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );

                return [
                    'status' => 'rejected',
                    'code' => 422,
                    'message' => 'Webhook amount or currency does not match order.',
                ];
            }

            // 6. Record Payment
            $isSuccess = ($parsed['status'] === Payment::STATUS_SUCCESSFUL);
            $receiptNumber = $isSuccess ? Payment::generateReceiptNumber() : null;
            $transactionRef = 'TXN-'.($parsed['gateway_payment_id'] ?: Str::random(10));
            $idempotencyKey = 'IDEMP-'.$gateway->getGatewayName().'-'.$parsed['gateway_payment_id'].'-'.$parsed['status'];

            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'payment_gateway' => $gateway->getGatewayName(),
                'transaction_reference' => $transactionRef,
                'gateway_payment_id' => $parsed['gateway_payment_id'],
                'amount' => $parsed['amount'],
                'currency' => $parsed['currency'],
                'status' => $parsed['status'],
                'payment_method' => $parsed['payment_method'],
                'receipt_number' => $receiptNumber,
                'gateway_response_code' => $parsed['response_code'],
                'gateway_response_message' => $parsed['response_message'],
                'idempotency_key' => $idempotencyKey,
                'raw_payload' => $parsed['raw_payload'],
                'paid_at' => $isSuccess ? now() : null,
            ]);

            // 7. Transition order & activate enrollment
            if ($isSuccess) {
                $statusBefore = $order->status;
                $order->update(['status' => Order::STATUS_COMPLETED]);

                // Activate Enrollment
                $enrollment = $order->enrollment_id
                    ? Enrollment::find($order->enrollment_id)
                    : Enrollment::where('user_id', $order->user_id)->where('course_id', $order->course_id)->first();

                if ($enrollment) {
                    $expiresAt = ($order->deliveryOption && $order->deliveryOption->access_period_days)
                        ? now()->addDays($order->deliveryOption->access_period_days)
                        : null;

                    $enrollment->update([
                        'status' => Enrollment::STATUS_ACTIVE,
                        'price_paid' => $order->amount,
                        'currency' => $order->currency,
                        'enrolled_at' => now(),
                        'expires_at' => $expiresAt,
                    ]);

                    if (! $order->enrollment_id) {
                        $order->update(['enrollment_id' => $enrollment->id]);
                    }

                    // Increment batch seat counter if physical batch
                    if ($order->course_batch_id && $order->batch) {
                        $order->batch->increment('enrolled_seats');
                    }
                }

                PaymentAuditLog::logEvent(
                    eventType: 'payment_successful',
                    order: $order,
                    payment: $payment,
                    statusBefore: $statusBefore,
                    statusAfter: Order::STATUS_COMPLETED,
                    payload: $parsed,
                    userId: $order->user_id,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );

                // Dispatch payment confirmation and enrollment confirmation notifications
                $dispatcher = app(NotificationDispatcherService::class);
                $user = $order->user;
                if ($user) {
                    $dispatcher->send(
                        user: $user,
                        notification: new PaymentConfirmationNotification($order, $payment),
                        deduplicationKey: "payment_confirmed_{$payment->id}_{$user->id}",
                        eventType: 'payment_confirmation'
                    );

                    if ($enrollment) {
                        $dispatcher->send(
                            user: $user,
                            notification: new EnrollmentConfirmationNotification($enrollment),
                            deduplicationKey: "enrollment_confirmed_{$enrollment->id}_{$user->id}",
                            eventType: 'enrollment_confirmation'
                        );
                    }
                }

                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Payment verified and enrollment activated.',
                    'order' => $order,
                    'payment' => $payment,
                ];
            }

            if ($parsed['status'] === Payment::STATUS_CANCELLED) {
                $statusBefore = $order->status;
                $order->update(['status' => Order::STATUS_CANCELLED]);

                PaymentAuditLog::logEvent(
                    eventType: 'payment_cancelled',
                    order: $order,
                    payment: $payment,
                    statusBefore: $statusBefore,
                    statusAfter: Order::STATUS_CANCELLED,
                    payload: $parsed,
                    userId: $order->user_id,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );

                return [
                    'status' => 'cancelled',
                    'code' => 200,
                    'message' => 'Payment marked cancelled.',
                    'order' => $order,
                    'payment' => $payment,
                ];
            }

            if ($parsed['status'] === Payment::STATUS_REFUNDED || $parsed['status'] === Payment::STATUS_CHARGEDBACK) {
                $statusBefore = $order->status;
                $order->update(['status' => Order::STATUS_REFUNDED]);
                $payment->update([
                    'refunded_at' => now(),
                    'refund_amount' => $parsed['amount'],
                ]);

                if ($order->enrollment) {
                    $order->enrollment->update([
                        'status' => Enrollment::STATUS_CANCELLED,
                        'cancellation_reason' => 'Payment refunded or charged back.',
                    ]);
                }

                PaymentAuditLog::logEvent(
                    eventType: 'payment_refunded',
                    order: $order,
                    payment: $payment,
                    statusBefore: $statusBefore,
                    statusAfter: Order::STATUS_REFUNDED,
                    payload: $parsed,
                    userId: $order->user_id,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );

                return [
                    'status' => 'refunded',
                    'code' => 200,
                    'message' => 'Payment refunded and enrollment revoked.',
                    'order' => $order,
                    'payment' => $payment,
                ];
            }

            // Otherwise, payment failed
            $statusBefore = $order->status;
            $order->update(['status' => Order::STATUS_FAILED]);

            PaymentAuditLog::logEvent(
                eventType: 'payment_failed',
                order: $order,
                payment: $payment,
                statusBefore: $statusBefore,
                statusAfter: Order::STATUS_FAILED,
                payload: $parsed,
                userId: $order->user_id,
                ipAddress: $ipAddress,
                userAgent: $userAgent
            );

            return [
                'status' => 'failed',
                'code' => 200,
                'message' => 'Payment failed.',
                'order' => $order,
                'payment' => $payment,
            ];
        });
    }
}
