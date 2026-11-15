<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Handle incoming Stripe / MockGateway webhook.
     */
    public function handleStripe(Request $request): JsonResponse
    {
        $payload = $request->all();

        $result = $this->paymentService->processWebhook(
            payload: $payload,
            gatewayName: 'stripe',
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ], $result['code']);
    }

    /**
     * Handle incoming PayHere Instant Payment Notification (IPN) webhook.
     */
    public function handlePayHere(Request $request): Response|JsonResponse
    {
        $payload = $request->all();

        $result = $this->paymentService->processWebhook(
            payload: $payload,
            gatewayName: 'payhere',
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
            ], $result['code']);
        }

        return response($result['message'], $result['code']);
    }

    /**
     * Handle incoming mock payment gateway webhook (for testing/sandbox environments).
     */
    public function handleMock(Request $request): JsonResponse
    {
        $payload = $request->all();

        $result = $this->paymentService->processWebhook(
            payload: $payload,
            gatewayName: 'mock',
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ], $result['code']);
    }
}
