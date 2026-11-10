<?php

namespace App\Services\Video;

interface VideoProviderInterface
{
    /**
     * Determine whether the provider has complete and valid credentials configured.
     */
    public function isConfigured(): bool;

    /**
     * Get the canonical provider name.
     */
    public function getProviderName(): string;

    /**
     * Create a secure direct-upload session with private signed playback settings.
     * Returns associative array containing 'upload_url' and 'upload_id'.
     *
     * @param  array<string, mixed>  $options
     * @return array{upload_url: string, upload_id: string}
     */
    public function createDirectUploadUrl(array $options = []): array;

    /**
     * Generate a cryptographically signed short-lived playback token for a private asset.
     *
     * @param  array<string, mixed>  $customClaims
     */
    public function generateSignedPlaybackToken(string $playbackId, array $customClaims = [], ?int $expiresInSeconds = null): string;

    /**
     * Build the HLS streaming URL (.m3u8) with the signed playback token attached.
     */
    public function buildPlaybackUrl(string $playbackId, string $token): string;

    /**
     * Verify the HMAC / cryptographic signature on an incoming webhook request.
     */
    public function verifyWebhookSignature(string $rawPayload, string $signatureHeader, ?string $secret = null): bool;

    /**
     * Parse and normalize webhook event payload into standard fields.
     *
     * @return array<string, mixed>
     */
    public function parseWebhookEvent(string $rawPayload): array;
}
