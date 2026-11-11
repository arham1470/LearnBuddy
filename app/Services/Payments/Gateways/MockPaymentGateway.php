<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGatewayInterface;

class MockPaymentGateway implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getGatewayName(): string
    {
        return 'mock';
    }

    public function isSandbox(): bool
    {
        return true;
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function initiatePayment(Order $order, array $customer = []): array
    {
        return [
            'action_url' => route('checkout.success', $order),
            'method' => 'GET',
            'fields' => [
                'order_id' => $order->order_number,
                'amount' => number_format((float) $order->amount, 2, '.', ''),
                'currency' => $order->currency,
                'signature' => hash_hmac('sha256', $order->order_number.$order->amount, 'mock_secret'),
            ],
        ];
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        // For testing and simulation: signature must match or equal 'valid_mock_signature'
        $sig = $payload['signature'] ?? '';
        $expected = hash_hmac('sha256', ($payload['order_id'] ?? '').($payload['amount'] ?? ''), 'mock_secret');

        return hash_equals($expected, $sig) || $sig === 'valid_mock_signature';
    }

    public function parseWebhookPayload(array $payload): array
    {
        $status = match (strtolower((string) ($payload['status'] ?? 'successful'))) {
            'successful', 'success', 'paid' => Payment::STATUS_SUCCESSFUL,
            'pending' => Payment::STATUS_PENDING,
            'cancelled', 'canceled' => Payment::STATUS_CANCELLED,
            'refunded' => Payment::STATUS_REFUNDED,
            'chargedback' => Payment::STATUS_CHARGEDBACK,
            default => Payment::STATUS_FAILED,
        };

        return [
            'order_number' => (string) ($payload['order_id'] ?? ''),
            'gateway_payment_id' => (string) ($payload['payment_id'] ?? 'MOCK-PAY-'.time()),
            'amount' => (float) ($payload['amount'] ?? 0.0),
            'currency' => strtoupper((string) ($payload['currency'] ?? 'LKR')),
            'status' => $status,
            'payment_method' => $payload['payment_method'] ?? 'TEST_CARD',
            'response_code' => (string) ($payload['response_code'] ?? '1'),
            'response_message' => $payload['response_message'] ?? 'Mock payment processed successfully.',
            'raw_payload' => $payload,
        ];
    }

    public function refund(Payment $payment, float $amount, string $reason): array
    {
        return [
            'success' => true,
            'refund_id' => 'MOCK-REF-'.time(),
            'message' => 'Mock refund completed.',
        ];
    }
}
