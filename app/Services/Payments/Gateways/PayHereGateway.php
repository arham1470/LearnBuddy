<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayHereGateway implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? config('payments.gateways.payhere', []);
    }

    public function getGatewayName(): string
    {
        return 'payhere';
    }

    public function isSandbox(): bool
    {
        return (bool) ($this->config['sandbox'] ?? true);
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['merchant_id']) && ! empty($this->config['merchant_secret']);
    }

    public function initiatePayment(Order $order, array $customer = []): array
    {
        $merchantId = (string) ($this->config['merchant_id'] ?? '');
        $merchantSecret = (string) ($this->config['merchant_secret'] ?? '');
        $formattedAmount = number_format((float) $order->amount, 2, '.', '');
        $currency = strtoupper($order->currency ?? 'LKR');

        // PayHere checkout hash generation
        $hash = $this->generateCheckoutHash($merchantId, $order->order_number, $formattedAmount, $currency, $merchantSecret);

        $nameParts = explode(' ', (string) ($customer['name'] ?? $order->billing_name ?? 'Student'), 2);
        $firstName = $nameParts[0] ?? 'Student';
        $lastName = $nameParts[1] ?? 'Customer';

        $fields = [
            'merchant_id' => $merchantId,
            'return_url' => $this->config['return_url'] ?? route('checkout.success', $order),
            'cancel_url' => $this->config['cancel_url'] ?? route('checkout.cancel', $order),
            'notify_url' => $this->config['notify_url'] ?? route('webhooks.payhere'),
            'order_id' => $order->order_number,
            'items' => $order->course->title ?? 'LearnBuddy Course Enrolment',
            'currency' => $currency,
            'amount' => $formattedAmount,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $customer['email'] ?? $order->billing_email ?? '',
            'phone' => $customer['phone'] ?? $order->billing_phone ?? '+94 77 000 0000',
            'address' => $customer['address'] ?? 'Sri Lanka',
            'city' => $customer['city'] ?? 'Colombo',
            'country' => 'Sri Lanka',
            'hash' => $hash,
            'custom_1' => (string) $order->id,
            'custom_2' => (string) $order->user_id,
        ];

        return [
            'action_url' => $this->config['checkout_url'] ?? 'https://sandbox.payhere.lk/pay/checkout',
            'method' => 'POST',
            'fields' => $fields,
        ];
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        $merchantId = (string) ($this->config['merchant_id'] ?? '');
        $merchantSecret = (string) ($this->config['merchant_secret'] ?? '');

        if (empty($merchantId) || empty($merchantSecret)) {
            Log::warning('PayHere signature verification skipped: merchant credentials not configured.');

            return false;
        }

        $payloadMerchantId = (string) ($payload['merchant_id'] ?? '');
        if ($payloadMerchantId !== $merchantId) {
            Log::warning('PayHere webhook merchant_id mismatch', [
                'expected' => $merchantId,
                'received' => $payloadMerchantId,
            ]);

            return false;
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $amount = (string) ($payload['payhere_amount'] ?? '');
        $currency = (string) ($payload['payhere_currency'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $receivedSig = strtoupper((string) ($payload['md5sig'] ?? ''));

        $expectedSig = $this->generateIpnHash($merchantId, $orderId, $amount, $currency, $statusCode, $merchantSecret);

        return hash_equals($expectedSig, $receivedSig);
    }

    public function parseWebhookPayload(array $payload): array
    {
        $statusCode = (int) ($payload['status_code'] ?? -99);

        $status = match ($statusCode) {
            2 => Payment::STATUS_SUCCESSFUL,
            0 => Payment::STATUS_PENDING,
            -1 => Payment::STATUS_CANCELLED,
            -2 => Payment::STATUS_FAILED,
            -3 => Payment::STATUS_CHARGEDBACK,
            default => Payment::STATUS_FAILED,
        };

        return [
            'order_number' => (string) ($payload['order_id'] ?? ''),
            'gateway_payment_id' => (string) ($payload['payment_id'] ?? ''),
            'amount' => (float) ($payload['payhere_amount'] ?? 0.0),
            'currency' => strtoupper((string) ($payload['payhere_currency'] ?? 'LKR')),
            'status' => $status,
            'payment_method' => $payload['method'] ?? $payload['payment_method'] ?? null,
            'response_code' => (string) ($payload['status_code'] ?? ''),
            'response_message' => $payload['status_message'] ?? null,
            'raw_payload' => $payload,
        ];
    }

    public function refund(Payment $payment, float $amount, string $reason): array
    {
        // PayHere programmatic refund requires OAuth Bearer token using app_id and app_secret
        $appId = (string) ($this->config['app_id'] ?? '');
        $appSecret = (string) ($this->config['app_secret'] ?? '');

        if (empty($appId) || empty($appSecret) || empty($payment->gateway_payment_id)) {
            return [
                'success' => false,
                'refund_id' => null,
                'message' => 'PayHere refund API credentials (app_id / app_secret) or payment_id not configured.',
            ];
        }

        try {
            $tokenResponse = Http::asForm()
                ->withBasicAuth($appId, $appSecret)
                ->post('https://api.payhere.lk/merchant/v1/oauth/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $tokenResponse->successful()) {
                return [
                    'success' => false,
                    'refund_id' => null,
                    'message' => 'Failed to obtain PayHere OAuth authorization token for refund.',
                ];
            }

            $accessToken = $tokenResponse->json('access_token');

            $refundResponse = Http::withToken($accessToken)
                ->post('https://api.payhere.lk/merchant/v1/payment/refund', [
                    'payment_id' => $payment->gateway_payment_id,
                    'description' => $reason,
                ]);

            if ($refundResponse->successful() && $refundResponse->json('status') === 1) {
                return [
                    'success' => true,
                    'refund_id' => (string) ($refundResponse->json('data.refund_id') ?? 'PH-REF-'.time()),
                    'message' => 'Refund processed successfully.',
                ];
            }

            return [
                'success' => false,
                'refund_id' => null,
                'message' => $refundResponse->json('msg') ?? 'PayHere refund request failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('PayHere refund error: '.$e->getMessage());

            return [
                'success' => false,
                'refund_id' => null,
                'message' => 'Exception during refund processing: '.$e->getMessage(),
            ];
        }
    }

    public function generateCheckoutHash(string $merchantId, string $orderId, string $amount, string $currency, string $merchantSecret): string
    {
        return strtoupper(
            md5(
                $merchantId.
                $orderId.
                $amount.
                $currency.
                strtoupper(md5($merchantSecret))
            )
        );
    }

    public function generateIpnHash(string $merchantId, string $orderId, string $amount, string $currency, string $statusCode, string $merchantSecret): string
    {
        return strtoupper(
            md5(
                $merchantId.
                $orderId.
                $amount.
                $currency.
                $statusCode.
                strtoupper(md5($merchantSecret))
            )
        );
    }
}
