<?php

namespace App\Services\LiveClass\Providers;

use App\Models\LiveClass;
use App\Models\User;
use App\Services\LiveClass\Contracts\LiveClassProviderInterface;
use Illuminate\Support\Str;

class JitsiLiveClassProvider implements LiveClassProviderInterface
{
    protected ?string $domain;

    protected ?string $appId;

    protected ?string $apiKeyId;

    protected ?string $privateKey;

    protected ?string $secret;

    protected string $roomPrefix;

    public function __construct(?array $config = null)
    {
        $cfg = $config ?? config('live_meeting.jitsi', []);

        $this->domain = $cfg['domain'] ?? null;
        $this->appId = $cfg['app_id'] ?? null;
        $this->apiKeyId = $cfg['api_key_id'] ?? null;
        $this->privateKey = $cfg['private_key'] ?? null;
        $this->secret = $cfg['secret'] ?? null;
        $this->roomPrefix = $cfg['room_prefix'] ?? 'learnbuddy';
    }

    public function getName(): string
    {
        return 'jitsi';
    }

    public function isConfigured(): bool
    {
        // For 8x8 JaaS (Jitsi as a Service)
        if ($this->domain === '8x8.vc' || (! empty($this->domain) && str_contains($this->domain, '8x8'))) {
            return ! empty($this->appId) && ! empty($this->apiKeyId) && ! empty($this->privateKey);
        }

        // For self-hosted Jitsi instance with secret or custom domain
        if (! empty($this->domain)) {
            return ! empty($this->secret) || ! empty($this->appId);
        }

        return false;
    }

    public function getUnconfiguredReason(): ?string
    {
        if ($this->isConfigured()) {
            return null;
        }

        return 'The Jitsi Meet live meeting provider is not configured in this environment. '.
            'Hosted commercial integration requires 8x8 JaaS credentials (Free Dev Tier: 25 MAUs; Basic: $99/mo for up to 300 MAUs) '.
            'with JITSI_DOMAIN=8x8.vc, JITSI_APP_ID, JITSI_API_KEY_ID, and JITSI_PRIVATE_KEY. '.
            'Alternatively, configure a dedicated self-hosted Jitsi Meet instance with JITSI_DOMAIN and JITSI_SECRET. '.
            '(Note: Public meet.jit.si removed anonymous room embedding in August 2023 and does not support production commercial embedding).';
    }

    public function createMeetingRoom(LiveClass $liveClass): array
    {
        $randomSlug = strtolower(Str::random(10));
        $roomCore = "{$this->roomPrefix}-c{$liveClass->course_id}-{$randomSlug}";

        // 8x8 JaaS requires format: "<appId>/<roomName>"
        $identifier = (! empty($this->appId) && ($this->domain === '8x8.vc' || str_contains((string) $this->domain, '8x8')))
            ? "{$this->appId}/{$roomCore}"
            : $roomCore;

        return [
            'meeting_identifier' => $identifier,
            'room_name' => $roomCore,
            'provider' => $this->getName(),
        ];
    }

    public function getClientMeetingConfig(LiveClass $liveClass, User $user, bool $isModerator): array
    {
        $domain = $this->domain ?: '8x8.vc';
        $isJaaS = ($domain === '8x8.vc' || str_contains((string) $domain, '8x8')) && ! empty($this->appId);

        // Ensure 8x8 JaaS room names are properly namespaced with "<appId>/<roomName>"
        $roomName = $liveClass->meeting_identifier;
        if ($isJaaS && ! str_starts_with($roomName, "{$this->appId}/")) {
            $roomName = "{$this->appId}/{$roomName}";
        }

        $jwt = $this->generateJwtToken($liveClass, $user, $isModerator);

        // Define participant toolbar buttons strictly by role:
        // NEVER expose administrative meeting controls (kick, mute-everyone, security, recording) to students!
        $moderatorToolbar = [
            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
            'fodeviceselection', 'hangup', 'chat', 'raisehand', 'videoquality',
            'filmstrip', 'tileview', 'select-background', 'mute-everyone', 'security',
        ];

        $studentToolbar = [
            'microphone', 'camera', 'chat', 'raisehand', 'desktop',
            'fullscreen', 'tileview', 'hangup', 'videoquality',
        ];

        return [
            'provider' => $this->getName(),
            'is_configured' => $this->isConfigured(),
            'unconfigured_reason' => $this->getUnconfiguredReason(),
            'domain' => $domain,
            'room_name' => $roomName,
            'jwt' => $jwt,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'is_moderator' => $isModerator,
            'toolbar_buttons' => $isModerator ? $moderatorToolbar : $studentToolbar,
            'settings' => [
                'subject' => "{$liveClass->course->title}: {$liveClass->title}",
                'startWithAudioMuted' => ! $isModerator,
                'startWithVideoMuted' => false,
                'disableRemoteMute' => ! $isModerator,
                'hideConferenceSubject' => false,
            ],
        ];
    }

    protected function generateJwtToken(LiveClass $liveClass, User $user, bool $isModerator): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        // Generate JWT if 8x8 JaaS or self-hosted secret is present
        $now = time();
        $exp = $now + 7200; // 2 hour token

        $header = [
            'alg' => $this->privateKey ? 'RS256' : 'HS256',
            'typ' => 'JWT',
        ];

        if ($this->apiKeyId) {
            $header['kid'] = $this->apiKeyId;
        }

        $isJaaS = ($this->domain === '8x8.vc' || str_contains((string) $this->domain, '8x8')) && ! empty($this->appId);

        // 8x8 JaaS strictly requires string "true" / "false" for moderator and feature claims
        $moderatorValue = $isJaaS ? ($isModerator ? 'true' : 'false') : $isModerator;
        $featureTrue = $isJaaS ? 'true' : true;
        $featureFalse = $isJaaS ? 'false' : false;

        $payload = [
            'aud' => 'jitsi',
            'iss' => 'chat',
            'sub' => $this->appId ?: $this->domain,
            'room' => '*',
            'nbf' => $now - 60,
            'iat' => $now - 60,
            'exp' => $exp,
            'context' => [
                'user' => [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => null,
                    'moderator' => $moderatorValue,
                ],
                'features' => [
                    'livestreaming' => $isModerator ? $featureTrue : $featureFalse,
                    'recording' => $isModerator ? $featureTrue : $featureFalse,
                    'transcription' => $isModerator ? $featureTrue : $featureFalse,
                    'outbound-call' => $featureFalse,
                ],
            ],
        ];

        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));
        $dataToSign = "{$encodedHeader}.{$encodedPayload}";

        if ($this->privateKey) {
            $key = openssl_pkey_get_private($this->privateKey);
            if (! $key && file_exists($this->privateKey)) {
                $key = openssl_pkey_get_private(file_get_contents($this->privateKey));
            }

            if ($key) {
                $signature = '';
                if (openssl_sign($dataToSign, $signature, $key, OPENSSL_ALGO_SHA256)) {
                    return "{$dataToSign}.".$this->base64UrlEncode($signature);
                }
            }
        } elseif ($this->secret) {
            $signature = hash_hmac('sha256', $dataToSign, $this->secret, true);

            return "{$dataToSign}.".$this->base64UrlEncode($signature);
        }

        return null;
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
