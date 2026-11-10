<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Video\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MuxWebhookController extends Controller
{
    /**
     * Handle incoming signed Mux webhooks.
     */
    public function handle(Request $request, VideoService $videoService): JsonResponse
    {
        $rawPayload = (string) $request->getContent();
        $signatureHeader = (string) $request->header('mux-signature', '');

        $provider = $videoService->getProvider();

        if (! $provider->verifyWebhookSignature($rawPayload, $signatureHeader)) {
            Log::warning('Rejected Mux webhook: invalid or missing signature header', [
                'ip' => $request->ip(),
                'signature_header' => $signatureHeader,
            ]);

            return response()->json([
                'error' => 'invalid_signature',
                'message' => 'The provided mux-signature is invalid or has expired.',
            ], 401);
        }

        $event = $provider->parseWebhookEvent($rawPayload);

        try {
            $videoService->handleWebhookEvent($event);

            return response()->json([
                'received' => true,
                'type' => $event['type'] ?? 'unknown',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error handling verified Mux webhook event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'processing_error',
                'message' => 'Failed to process event.',
            ], 500);
        }
    }
}
