<?php

namespace App\Services\Video;

use App\Models\Lesson;
use App\Models\User;
use App\Services\Video\Exceptions\VideoProviderException;
use Illuminate\Support\Facades\Log;

class VideoService
{
    protected VideoProviderInterface $provider;

    public function __construct(?VideoProviderInterface $provider = null)
    {
        $this->provider = $provider ?? $this->resolveProvider();
    }

    public function getProvider(): VideoProviderInterface
    {
        return $this->provider;
    }

    public function setProvider(VideoProviderInterface $provider): void
    {
        $this->provider = $provider;
    }

    protected function resolveProvider(): VideoProviderInterface
    {
        $driver = config('video.default', 'mux');

        return match ($driver) {
            'mux' => new MuxVideoProvider,
            default => new NullVideoProvider,
        };
    }

    /**
     * Request a direct upload session for a specific lesson and update its database state.
     *
     * @return array{upload_url: string, upload_id: string}
     */
    public function initiateDirectUpload(Lesson $lesson): array
    {
        if (! $this->provider->isConfigured()) {
            throw VideoProviderException::notConfigured($this->provider->getProviderName());
        }

        $result = $this->provider->createDirectUploadUrl([
            'passthrough' => "lesson:{$lesson->id}",
        ]);

        $lesson->update([
            'video_provider' => $this->provider->getProviderName(),
            'video_upload_id' => $result['upload_id'],
            'video_status' => 'waiting_upload',
            'video_error_message' => null,
            'video_playback_policy' => 'signed',
        ]);

        return $result;
    }

    /**
     * Generate secure playback access data for an authorized viewer.
     *
     * @return array<string, mixed>
     */
    public function getPlaybackDataForUser(Lesson $lesson, User $user, string $clientIp = ''): array
    {
        if (empty($lesson->video_playback_id)) {
            return [
                'status' => 'none',
                'message' => 'No video asset has been attached to this lesson.',
            ];
        }

        if (in_array($lesson->video_status, ['waiting_upload', 'processing'])) {
            return [
                'status' => 'processing',
                'message' => 'Video is currently being encoded and optimized by our streaming servers. Please refresh in a moment.',
            ];
        }

        if ($lesson->video_status === 'errored') {
            return [
                'status' => 'errored',
                'message' => 'Video encoding failed: '.($lesson->video_error_message ?? 'Unknown provider error.').' Please contact support or re-upload.',
            ];
        }

        if (! $this->provider->isConfigured()) {
            return [
                'status' => 'unconfigured',
                'message' => 'Video streaming provider credentials (Mux) are unconfigured in this environment. Playback cannot be signed.',
                'provider' => $this->provider->getProviderName(),
            ];
        }

        $ttl = (int) config('video.token_ttl', 600);
        $expiresAt = time() + $ttl;

        $token = $this->provider->generateSignedPlaybackToken($lesson->video_playback_id, [
            'usr' => $user->id,
            'eml' => $user->email,
        ], $ttl);

        $playbackUrl = $this->provider->buildPlaybackUrl($lesson->video_playback_id, $token);

        return [
            'status' => 'ready',
            'playback_id' => $lesson->video_playback_id,
            'playback_url' => $playbackUrl,
            'playback_token' => $token,
            'expires_at' => $expiresAt,
            'expires_in_seconds' => $ttl,
            'watermark' => [
                'student_id' => $user->id,
                'student_name' => $user->name,
                'student_email' => $user->email,
                'client_ip' => $clientIp,
                'timestamp' => now()->toISOString(),
                'disclaimer' => 'Dynamic client-side watermarking is active to deter unauthorized recording and leaking. Note that no web technology can completely prevent all external screen recording.',
            ],
        ];
    }

    /**
     * Process an incoming verified webhook event from the provider.
     *
     * @param  array<string, mixed>  $event
     */
    public function handleWebhookEvent(array $event): void
    {
        $type = $event['type'] ?? 'unknown';
        $uploadId = $event['upload_id'] ?? null;
        $assetId = $event['asset_id'] ?? null;

        Log::info("Processing video webhook event [{$type}]", [
            'upload_id' => $uploadId,
            'asset_id' => $assetId,
        ]);

        $lesson = null;
        if ($uploadId) {
            $lesson = Lesson::where('video_upload_id', $uploadId)->first();
        }
        if (! $lesson && $assetId) {
            $lesson = Lesson::where('video_asset_id', $assetId)->first();
        }

        if (! $lesson) {
            Log::warning("No lesson found matching webhook event [{$type}]", [
                'upload_id' => $uploadId,
                'asset_id' => $assetId,
            ]);

            return;
        }

        switch ($type) {
            case 'video.upload.asset_created':
                $lesson->update([
                    'video_asset_id' => $assetId ?? $lesson->video_asset_id,
                    'video_status' => 'processing',
                ]);
                break;

            case 'video.asset.ready':
                $durationSeconds = $event['duration_seconds'] ?? null;
                $durationMinutes = $durationSeconds ? (int) ceil($durationSeconds / 60) : $lesson->duration_minutes;

                $lesson->update([
                    'video_asset_id' => $assetId ?? $lesson->video_asset_id,
                    'video_playback_id' => $event['playback_id'] ?? $lesson->video_playback_id,
                    'video_status' => 'ready',
                    'video_aspect_ratio' => $event['aspect_ratio'] ?? $lesson->video_aspect_ratio,
                    'duration_minutes' => $durationMinutes ?: $lesson->duration_minutes,
                    'video_error_message' => null,
                ]);
                break;

            case 'video.asset.errored':
                $lesson->update([
                    'video_status' => 'errored',
                    'video_error_message' => $event['error_message'] ?? 'Asset encoding encountered a fatal error on the video provider.',
                ]);
                break;
        }
    }
}
