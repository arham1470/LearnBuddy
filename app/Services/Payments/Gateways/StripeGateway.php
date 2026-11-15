<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? config('payments.gateways.stripe', []);
    }

    public function getGatewayName(): string
    {
        return 'stripe';
    }

    public function isSandbox(): bool
    {
        return (bool) ($this->config['sandbox'] ?? true);
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['secret_key']);
    }

    public function initiatePayment(Order $order, array $customer = []): array
    {
        $secretKey = (string) ($this->config['secret_key'] ?? '');
        $baseUrl = rtrim((string) ($this->config['base_url'] ?? 'https://mockgateway.com/api/base/stripe-zzrjuo'), '/');
        $currency = strtolower($order->currency ?? 'lkr');
        $amountInCents = (int) round((float) $order->amount * 100);

        $returnUrl = $this->config['return_url'] ?? route('checkout.success', $order);

        $payload = [
            'amount' => $amountInCents,
            'currency' => $currency,
            'description' => 'Payment for '.($order->course->title ?? 'Course Enrolment'),
            'customer' => 'cus_'.$order->user_id,
            'receipt_email' => $customer['email'] ?? $order->billing_email ?? $order->user->email,
            'metadata' => [
                'order_id' => $order->order_number,
                'course_id' => (string) $order->course_id,
                'user_id' => (string) $order->user_id,
            ],
            'return_url' => $returnUrl,
        ];

        try {
            $response = Http::withToken($secretKey)
                ->timeout(15)
                ->post("{$baseUrl}/v1/payment_intents", $payload);

            if (! $response->successful()) {
                Log::error('Stripe / MockGateway payment initialization failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'order' => $order->order_number,
                ]);

                throw new \RuntimeException('Failed to initialize payment with MockGateway: '.($response->json('error.message') ?? $response->body()));
            }

            $data = $response->json();
            $redirectUrl = $data['next_action']['redirect_to_url']['url']
                ?? $data['payment_url']
                ?? $returnUrl;

            return [
                'action_url' => $redirectUrl,
                'method' => 'GET',
                'fields' => [],
            ];
        } catch (\Throwable $e) {
            Log::error('StripeGateway initiatePayment exception: '.$e->getMessage());
            throw $e;
        }
    }

    public function getPaymentIntent(string $paymentIntentId): ?array
    {
        $secretKey = (string) ($this->config['secret_key'] ?? '');
        $baseUrl = rtrim((string) ($this->config['base_url'] ?? 'https://mockgateway.com/api/base/stripe-zzrjuo'), '/');

        try {
            $response = Http::withToken($secretKey)
                ->timeout(10)
                ->get("{$baseUrl}/v1/payment_intents/{$paymentIntentId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("StripeGateway getPaymentIntent returned {$response->status()}", [
                'id' => $paymentIntentId,
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('StripeGateway getPaymentIntent exception: '.$e->getMessage());

            return null;
        }
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        // For MockGateway sandbox, accept requests as verified
        return true;
    }

    public function parseWebhookPayload(array $payload): array
    {
        $object = $payload['data']['object'] ?? $payload['object'] ?? $payload;

        $intentStatus = strtolower((string) ($object['status'] ?? 'succeeded'));
        $status = match ($intentStatus) {
            'succeeded' => Payment::STATUS_SUCCESSFUL,
            'processing', 'requires_action', 'requires_confirmation' => Payment::STATUS_PENDING,
            'canceled' => Payment::STATUS_CANCELLED,
            default => Payment::STATUS_FAILED,
        };

        $rawAmount = (float) ($object['amount'] ?? 0);
        $amount = $rawAmount > 0 ? $rawAmount / 100 : 0.0;
        $orderNumber = (string) ($object['metadata']['order_id'] ?? $payload['order_id'] ?? '');

        return [
            'order_number' => $orderNumber,
            'gateway_payment_id' => (string) ($object['id'] ?? 'pi_mock_'.time()),
            'amount' => $amount,
            'currency' => strtoupper((string) ($object['currency'] ?? 'LKR')),
            'status' => $status,
            'payment_method' => 'card',
            'response_code' => $intentStatus,
            'response_message' => 'Processed via MockGateway Stripe',
            'raw_payload' => $payload,
        ];
    }

    public function refund(Payment $payment, float $amount, string $reason): array
    {
        return [
            'success' => true,
            'refund_id' => 're_mock_'.time(),
            'message' => 'MockGateway refund registered.',
        ];
    }
}
