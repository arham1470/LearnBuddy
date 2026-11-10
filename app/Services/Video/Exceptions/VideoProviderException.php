<?php

namespace App\Services\Video\Exceptions;

use RuntimeException;

class VideoProviderException extends RuntimeException
{
    public static function notConfigured(string $provider): self
    {
        return new self("Video provider [{$provider}] is not configured. Missing required credentials in .env.");
    }

    public static function apiError(string $message, int $statusCode = 500): self
    {
        return new self("Video provider API error ({$statusCode}): {$message}");
    }

    public static function invalidSignature(): self
    {
        return new self('Invalid or missing provider webhook signature.');
    }
}
