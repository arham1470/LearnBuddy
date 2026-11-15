<?php

namespace App\Services\Video;

use App\Services\Video\Exceptions\VideoProviderException;
use Illuminate\Support\Facades\Http;
use OpenSSLAsymmetricKey;

class MuxVideoProvider implements VideoProviderInterface
{
    protected ?string $tokenId;

    protected ?string $tokenSecret;

    protected ?string $signingKeyId;

    protected ?string $signingKeyPrivate;

    protected ?string $webhookSecret;

    protected string $baseUrl;

    protected string $corsOrigin;

    public function __construct(?array $config = null)
    {
        $cfg = $config ?? config('video.mux', []);

        $this->tokenId = $cfg['token_id'] ?? null;
        $this->tokenSecret = $cfg['token_secret'] ?? null;
        $this->signingKeyId = $cfg['signing_key_id'] ?? null;
        $this->signingKeyPrivate = $cfg['signing_key_private'] ?? null;
        $this->webhookSecret = $cfg['webhook_secret'] ?? null;
        $this->baseUrl = rtrim($cfg['base_url'] ?? 'https://api.mux.com', '/');
        $this->corsOrigin = $cfg['cors_origin'] ?? '*';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->tokenId)
            && ! empty($this->tokenSecret)
            && ! empty($this->signingKeyId)
            && ! empty($this->signingKeyPrivate);
    }

    public function getProviderName(): string
    {
        return 'mux';
    }

    public function createDirectUploadUrl(array $options = []): array
    {
        if (! $this->isConfigured()) {
            throw VideoProviderException::notConfigured($this->getProviderName());
        }

        $payload = [
            'cors_origin' => $this->corsOrigin,
            'new_asset_settings' => [
                'playback_policy' => ['signed'],
                'encoding_tier' => 'baseline', // Free input tier on Mux
            ],
        ];

        if (! empty($options['passthrough'])) {
            $payload['new_asset_settings']['passthrough'] = (string) $options['passthrough'];
        }

        $response = Http::withBasicAuth($this->tokenId, $this->tokenSecret)
            ->acceptJson()
            ->post("{$this->baseUrl}/video/v1/uploads", $payload);

        if (! $response->successful()) {
            throw VideoProviderException::apiError($response->body(), $response->status());
        }

        $data = $response->json('data') ?? [];

        if (empty($data['url']) || empty($data['id'])) {
            throw VideoProviderException::apiError('Invalid response payload from Mux upload endpoint.');
        }

        return [
            'upload_url' => $data['url'],
            'upload_id' => $data['id'],
        ];
    }

    public function generateSignedPlaybackToken(string $playbackId, array $customClaims = [], ?int $expiresInSeconds = null): string
    {
        if (! $this->isConfigured()) {
            throw VideoProviderException::notConfigured($this->getProviderName());
        }

        $privateKey = $this->parsePrivateKey($this->signingKeyPrivate);
        if (! $privateKey) {
            throw new VideoProviderException('Mux private signing key could not be parsed by OpenSSL.');
        }

        $ttl = $expiresInSeconds ?? (int) config('video.token_ttl', 600);
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
            'kid' => $this->signingKeyId,
        ];

        $payload = array_merge([
            'sub' => $playbackId,
            'aud' => 'v',
            'exp' => time() + $ttl,
        ], $customClaims);

        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));
        $dataToSign = "{$encodedHeader}.{$encodedPayload}";

        $signature = '';
        $success = openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (! $success) {
            throw new VideoProviderException('Failed to sign Mux playback JWT token: '.openssl_error_string());
        }

        $encodedSignature = $this->base64UrlEncode($signature);

        return "{$dataToSign}.{$encodedSignature}";
    }

    public function buildPlaybackUrl(string $playbackId, string $token): string
    {
        return "https://stream.mux.com/{$playbackId}.m3u8?token={$token}";
    }

    public function verifyWebhookSignature(string $rawPayload, string $signatureHeader, ?string $secret = null): bool
    {
        $signingSecret = $secret ?? $this->webhookSecret;

        if (empty($signingSecret) || empty($signatureHeader)) {
            return false;
        }

        $pairs = explode(',', $signatureHeader);
        $timestamp = null;
        $signatures = [];

        foreach ($pairs as $pair) {
            $parts = explode('=', trim($pair), 2);
            if (count($parts) !== 2) {
                continue;
            }

            [$key, $value] = $parts;
            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 'v1') {
                $signatures[] = $value;
            }
        }

        if (empty($timestamp) || empty($signatures)) {
            return false;
        }

        // Tolerance window: 5 minutes (300 seconds) to prevent replay attacks
        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', "{$timestamp}.{$rawPayload}", $signingSecret);

        foreach ($signatures as $sig) {
            if (hash_equals($expectedSignature, $sig)) {
                return true;
            }
        }

        return false;
    }

    public function parseWebhookEvent(string $rawPayload): array
    {
        $decoded = json_decode($rawPayload, true);
        if (! is_array($decoded)) {
            return [];
        }

        $type = $decoded['type'] ?? 'unknown';
        $data = $decoded['data'] ?? [];

        $playbackId = null;
        if (! empty($data['playback_ids']) && is_array($data['playback_ids'])) {
            // Prefer signed playback policy
            $signedPlayback = collect($data['playback_ids'])->firstWhere('policy', 'signed');
            $playbackId = $signedPlayback['id'] ?? ($data['playback_ids'][0]['id'] ?? null);
        }

        $errorMessage = null;
        if (! empty($data['errors']['messages']) && is_array($data['errors']['messages'])) {
            $errorMessage = implode('; ', $data['errors']['messages']);
        }

        $assetId = str_starts_with($type, 'video.upload.')
            ? ($data['asset_id'] ?? null)
            : ($data['id'] ?? ($data['asset_id'] ?? null));

        return [
            'type' => $type,
            'asset_id' => $assetId,
            'upload_id' => $data['upload_id'] ?? ($data['id'] ?? null),
            'playback_id' => $playbackId,
            'duration_seconds' => isset($data['duration']) ? (float) $data['duration'] : null,
            'aspect_ratio' => $data['aspect_ratio'] ?? null,
            'status' => $data['status'] ?? null,
            'error_message' => $errorMessage,
            'raw' => $data,
        ];
    }

    protected function parsePrivateKey(string $key): OpenSSLAsymmetricKey|false
    {
        $trimmed = trim($key);

        if (str_starts_with($trimmed, '-----BEGIN')) {
            return openssl_pkey_get_private($trimmed);
        }

        $decoded = base64_decode($trimmed, true);
        if ($decoded && str_starts_with($decoded, '-----BEGIN')) {
            return openssl_pkey_get_private($decoded);
        }

        if ($decoded) {
            $pemPkcs1 = "-----BEGIN RSA PRIVATE KEY-----\n".chunk_split($trimmed, 64, "\n")."-----END RSA PRIVATE KEY-----\n";
            $res = openssl_pkey_get_private($pemPkcs1);
            if ($res) {
                return $res;
            }

            $pemPkcs8 = "-----BEGIN PRIVATE KEY-----\n".chunk_split($trimmed, 64, "\n")."-----END PRIVATE KEY-----\n";

            return openssl_pkey_get_private($pemPkcs8);
        }

        return openssl_pkey_get_private($trimmed);
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function getUpload(string $uploadId): ?array
    {
        if (! $this->isConfigured()) {
            throw VideoProviderException::notConfigured($this->getProviderName());
        }

        $response = Http::withBasicAuth($this->tokenId, $this->tokenSecret)
            ->acceptJson()
            ->get("{$this->baseUrl}/video/v1/uploads/{$uploadId}");

        if (! $response->successful()) {
            return null;
        }

        return $response->json('data');
    }

    public function getAsset(string $assetId): ?array
    {
        if (! $this->isConfigured()) {
            throw VideoProviderException::notConfigured($this->getProviderName());
        }

        $response = Http::withBasicAuth($this->tokenId, $this->tokenSecret)
            ->acceptJson()
            ->get("{$this->baseUrl}/video/v1/assets/{$assetId}");

        if (! $response->successful()) {
            return null;
        }

        return $response->json('data');
    }
}
