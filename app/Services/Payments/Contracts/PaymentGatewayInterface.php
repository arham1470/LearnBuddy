<?php

namespace App\Services\Payments\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Get the identifier name of the payment gateway.
     */
    public function getGatewayName(): string;

    /**
     * Check if the gateway is running in sandbox mode.
     */
    public function isSandbox(): bool;

    /**
     * Check if the gateway credentials are fully configured.
     */
    public function isConfigured(): bool;

    /**
     * Prepare payment initialization parameters for frontend checkout redirect/POST.
     *
     * @param  array<string, mixed>  $customer
     * @return array{action_url: string, method: string, fields: array<string, mixed>}
     */
    public function initiatePayment(Order $order, array $customer = []): array;

    /**
     * Verify the cryptographic webhook/IPN signature against provider secrets.
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifyWebhookSignature(array $payload): bool;

    /**
     * Parse raw webhook payload into standardized domain payment attributes.
     *
     * @param  array<string, mixed>  $payload
     * @return array{
     *     order_number: string,
     *     gateway_payment_id: string,
     *     amount: float,
     *     currency: string,
     *     status: string,
     *     payment_method: ?string,
     *     response_code: ?string,
     *     response_message: ?string,
     *     raw_payload: array<string, mixed>
     * }
     */
    public function parseWebhookPayload(array $payload): array;

    /**
     * Initiate a programmatic refund if supported by provider.
     *
     * @return array{success: bool, refund_id: ?string, message: string}
     */
    public function refund(Payment $payment, float $amount, string $reason): array;
}
