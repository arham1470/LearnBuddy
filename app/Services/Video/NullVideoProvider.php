<?php

namespace App\Services\Video;

use App\Services\Video\Exceptions\VideoProviderException;

class NullVideoProvider implements VideoProviderInterface
{
    public function isConfigured(): bool
    {
        return false;
    }

    public function getProviderName(): string
    {
        return 'null';
    }

    public function createDirectUploadUrl(array $options = []): array
    {
        throw VideoProviderException::notConfigured($this->getProviderName());
    }

    public function generateSignedPlaybackToken(string $playbackId, array $customClaims = [], ?int $expiresInSeconds = null): string
    {
        throw VideoProviderException::notConfigured($this->getProviderName());
    }

    public function buildPlaybackUrl(string $playbackId, string $token): string
    {
        return '';
    }

    public function verifyWebhookSignature(string $rawPayload, string $signatureHeader, ?string $secret = null): bool
    {
        return false;
    }

    public function parseWebhookEvent(string $rawPayload): array
    {
        return [];
    }
}
