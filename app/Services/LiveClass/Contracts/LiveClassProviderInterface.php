<?php

namespace App\Services\LiveClass\Contracts;

use App\Models\LiveClass;
use App\Models\User;

interface LiveClassProviderInterface
{
    /**
     * Get provider name identifier (e.g. 'jitsi').
     */
    public function getName(): string;

    /**
     * Determine whether the provider has required credentials configured.
     */
    public function isConfigured(): bool;

    /**
     * Return explanation and setup requirements when unconfigured.
     */
    public function getUnconfiguredReason(): ?string;

    /**
     * Generate unique room identifier and initial meeting details.
     */
    public function createMeetingRoom(LiveClass $liveClass): array;

    /**
     * Build sanitized client configuration for the embedded meeting interface.
     * Never exposes private keys, server secrets, or administrative credentials to students.
     *
     * @return array{
     *     provider: string,
     *     domain: string,
     *     room_name: string,
     *     jwt: ?string,
     *     user_name: string,
     *     user_email: string,
     *     is_moderator: bool,
     *     toolbar_buttons: array<string>,
     *     settings: array<string, mixed>
     * }
     */
    public function getClientMeetingConfig(LiveClass $liveClass, User $user, bool $isModerator): array;
}
