<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseDeliveryOption;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\Video\MuxVideoProvider;
use App\Services\Video\VideoService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VideoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $instructor;

    protected User $otherInstructor;

    protected User $student;

    protected User $unauthorizedStudent;

    protected Course $course;

    protected CourseSection $section;

    protected Lesson $lesson;

    protected Lesson $unpublishedLesson;

    protected Course $otherCourse;

    protected Lesson $otherCourseLesson;

    protected string $testSigningKeyId = 'test-signing-key-id';

    protected string $testWebhookSecret = 'test_webhook_secret_12345';

    protected string $testPrivateKey = <<<'EOD'
-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDADcWkTASArh8Y
x7s393jin/dFcnBIptPbJcijE2QRPb8uirfhiWd0caEMIotLMhU2AGdTgz8d+66G
1VkGslFPEktFAOl+Pok1B742skEPx6ScLGFbWhv41fQd6R8TGV3S4c8XokHQ1eSa
bKLCrIwWZE3h5y6bTGcix78dpiY5tg9cNMK2yxuGiqZvkvBj2fUypKjIC4c3D2GI
8Njrfusf7Ad727CVDb77LYhHuhCTCVj5AX3sWtzCERqKikxdf5f3+hSxvMh5orJw
C5kDlJvhEx2lWZ215wSCDI57H9mBoSHmgV8SZqD4/9uNucdNPgGrN+Bx4jcDBod7
9t0fz+NXAgMBAAECggEAQsQm6xSwVgnybpt1FyJV6LdC7+Y2adNxgFS02RiTJ7lH
HweDoIkzqZQdMEpMEZKyNAlU4oAUr7hA9sfxwLW6NZp/B1inU8eqzIBKHNrAapt1
uwNkvyzm342F5eYW8v1VFEyDQcybQ94wMI1G0hXJR7mMJ9ZGah/YFQvLAaQy0snA
AIzEglVCX6PTwaG3b0Cbc7mZW4GqSA3z4anM0k9SJbS6KE+W7sTGKDSf9xfdljJ/
2I9vU8Z5T/y1zi8UVsWXdE4quHovfrw96jF0RsFR/OCgjtR0vUXo87KSpK/9Owql
9+XSKaLpaT4lFbhAdbPz9gjGUooh8kCigt3d0hzJgQKBgQD/LIOSeoBmOyRU3jsc
dNFUoj/rjTSnzhrpyynK4PZULr4NpD1S0PgoulqiQeJ8S6hVUy2uNaznQXHk7Jz3
NcGArCx8Xa7Y9WQGY+FuZHltUJ/CMbGNm/XqYa4qst1qQB1X+G/AgQEO5ehnHqHL
k+fR61LSWdu7sFi+yEJXRfrDgQKBgQDArPHV0oFuiSJ+EbYcLs9WTk01q2RiQMnc
XDYqCO+4uTPGfg1VDzrOxBOLSb0fHkU1tFGlmRMJ8+ezvpStwo5C9g172TuJvW3F
xJYBm2WraFBIvnYbIIkotycTY9K/Z0URdYd7GfMRS3OnyPCwM0otxaxRyGaF+HZk
ZXl4CUay1wKBgQCyHq19KxvDwNbckv/qHjUdAeDimP+/fuRUfGeg4aWxH6u0tAfG
bm47Z9HqX1kscFeBr/08FOWry4goR25ONezr18N50J9DfQ+kUkZDG8mTH+tU0AFj
mxFu2V6NGavrBfQ1uwqu8p39e0jd6okAJS913fFDOkxi2HcqPXnpl8SygQKBgAu6
+8Y+2K54Hud6AJJarzjb74ty8duhgiiuXIXWoXGnpOVSy1EAal4oReBXDvKUDzaj
0cy5MxWKCfNBiR/DFpIxFMUThrJuG/7MZy2V3my/HxQutFYWdwNJk1F05GHO0b+s
XiE6hTfqVhJDA5RWqw96V3FrOAK0C9E09F5AglNlAoGACGlqERXW+/GOJPDfnhwx
FsB53s5eLVnxKDF4U89esZC/npUKM5Bf+h7rric9rYvUvyDe67YBZz+ymkL4L2LS
fa1x1+C0rHn/nb87zKE1bafD5feuQl4TlwJ8MlykIYvLwcVdTi6hu7EqXkWPLzEI
gOtcf2FeDHWM7yJXZbf7BE8=
-----END PRIVATE KEY-----
EOD;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $category = Category::firstOrCreate(
            ['slug' => 'cloud-native'],
            ['name' => 'Cloud Native Engineering', 'sort_order' => 1]
        );

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');

        $this->otherInstructor = User::factory()->create();
        $this->otherInstructor->assignRole('instructor');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $this->unauthorizedStudent = User::factory()->create();
        $this->unauthorizedStudent->assignRole('student');

        $this->course = Course::create([
            'category_id' => $category->id,
            'title' => 'Kubernetes in Production',
            'slug' => 'kubernetes-in-production',
            'description' => 'Comprehensive Kubernetes production guide for cloud architects.',
            'status' => Course::STATUS_PUBLISHED,
            'published_at' => now(),
            'created_by_user_id' => $this->instructor->id,
        ]);

        $this->course->instructors()->attach($this->instructor->id, [
            'is_primary' => true,
            'can_manage_content' => true,
            'can_grade' => true,
        ]);

        $deliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->course->id,
            'delivery_mode' => 'online',
            'title' => 'Online Streaming Access',
            'is_free' => true,
            'price' => 0.00,
            'currency' => 'LKR',
            'is_active' => true,
        ]);

        $this->section = CourseSection::create([
            'course_id' => $this->course->id,
            'title' => 'Module 1: Pods & Services',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->lesson = Lesson::create([
            'section_id' => $this->section->id,
            'title' => '1.1 Deep Dive into Cgroups & Namespaces',
            'slug' => 'deep-dive-cgroups-namespaces',
            'type' => 'video',
            'video_provider' => 'mux',
            'video_status' => 'ready',
            'video_playback_id' => 'mock-signed-playback-id-123',
            'duration_minutes' => 18,
            'sort_order' => 1,
            'is_preview' => false,
            'is_published' => true,
        ]);

        $this->unpublishedLesson = Lesson::create([
            'section_id' => $this->section->id,
            'title' => '1.2 Advanced Service Meshes',
            'slug' => 'advanced-service-meshes',
            'type' => 'video',
            'video_provider' => 'mux',
            'video_status' => 'ready',
            'video_playback_id' => 'mock-signed-playback-id-unpub',
            'duration_minutes' => 25,
            'sort_order' => 2,
            'is_preview' => false,
            'is_published' => false,
        ]);

        // Unrelated course
        $this->otherCourse = Course::create([
            'category_id' => $category->id,
            'title' => 'Golang Microservices',
            'slug' => 'golang-microservices',
            'description' => 'Golang microservices distributed architecture roadmap.',
            'status' => Course::STATUS_PUBLISHED,
            'created_by_user_id' => $this->otherInstructor->id,
        ]);

        $otherSection = CourseSection::create([
            'course_id' => $this->otherCourse->id,
            'title' => 'Module 1: Go Routines',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->otherCourseLesson = Lesson::create([
            'section_id' => $otherSection->id,
            'title' => 'Channels and Concurrency',
            'type' => 'video',
            'is_published' => true,
        ]);

        // Active enrollment for $this->student
        Enrollment::create([
            'enrollment_code' => 'ENR-K8S-001',
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $deliveryOption->id,
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'amount_paid' => 0.00,
            'currency' => 'LKR',
            'enrolled_at' => now(),
        ]);
    }

    protected function configureMockMuxProvider(): void
    {
        config([
            'video.default' => 'mux',
            'video.token_ttl' => 600,
            'video.mux.token_id' => 'mock-token-id',
            'video.mux.token_secret' => 'mock-token-secret',
            'video.mux.signing_key_id' => $this->testSigningKeyId,
            'video.mux.signing_key_private' => $this->testPrivateKey,
            'video.mux.webhook_secret' => $this->testWebhookSecret,
        ]);

        $provider = new MuxVideoProvider(config('video.mux'));
        app(VideoService::class)->setProvider($provider);
    }

    protected function unconfigureVideoProvider(): void
    {
        config([
            'video.mux.token_id' => null,
            'video.mux.token_secret' => null,
            'video.mux.signing_key_id' => null,
            'video.mux.signing_key_private' => null,
            'video.mux.webhook_secret' => null,
        ]);

        $provider = new MuxVideoProvider(config('video.mux'));
        app(VideoService::class)->setProvider($provider);
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Upload Permission Tests
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_initiate_video_upload(): void
    {
        $response = $this->postJson(route('management.lessons.video-upload', [$this->course, $this->lesson]));
        $response->assertUnauthorized();
    }

    public function test_student_cannot_initiate_video_upload(): void
    {
        $response = $this->actingAs($this->student)
            ->postJson(route('management.lessons.video-upload', [$this->course, $this->lesson]));

        $response->assertForbidden();
    }

    public function test_unauthorized_instructor_cannot_initiate_video_upload(): void
    {
        $response = $this->actingAs($this->otherInstructor)
            ->postJson(route('management.lessons.video-upload', [$this->course, $this->lesson]));

        $response->assertForbidden();
    }

    public function test_authorized_instructor_receives_unconfigured_error_when_provider_not_configured(): void
    {
        $this->unconfigureVideoProvider();

        $response = $this->actingAs($this->instructor)
            ->postJson(route('management.lessons.video-upload', [$this->course, $this->lesson]));

        $response->assertStatus(422);
        $response->assertJson([
            'error' => 'provider_unconfigured',
        ]);
    }

    public function test_authorized_instructor_can_initiate_upload_when_provider_configured(): void
    {
        $this->configureMockMuxProvider();

        Http::fake([
            'https://api.mux.com/video/v1/uploads' => Http::response([
                'data' => [
                    'id' => 'mock-upload-session-999',
                    'url' => 'https://mock-upload.mux.com/direct-file',
                    'status' => 'waiting',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->instructor)
            ->postJson(route('management.lessons.video-upload', [$this->course, $this->lesson]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'upload_url' => 'https://mock-upload.mux.com/direct-file',
            'upload_id' => 'mock-upload-session-999',
            'video_provider' => 'mux',
        ]);

        $this->assertDatabaseHas('lessons', [
            'id' => $this->lesson->id,
            'video_upload_id' => 'mock-upload-session-999',
            'video_status' => 'waiting_upload',
            'video_playback_policy' => 'signed',
        ]);
    }

    public function test_upload_session_rejects_cross_course_idor_with_404(): void
    {
        $this->configureMockMuxProvider();

        // Pass lesson belonging to other course under this course URL
        $response = $this->actingAs($this->admin)
            ->postJson(route('management.lessons.video-upload', [$this->course, $this->otherCourseLesson]));

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Playback Authorisation & IDOR Protection Tests
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_playback_data(): void
    {
        $response = $this->getJson(route('learning.playback', [$this->course, $this->lesson]));
        $response->assertUnauthorized();
    }

    public function test_non_enrolled_student_cannot_access_playback_data(): void
    {
        $response = $this->actingAs($this->unauthorizedStudent)
            ->getJson(route('learning.playback', [$this->course, $this->lesson]));

        $response->assertForbidden();
    }

    public function test_student_with_pending_payment_cannot_access_playback_data(): void
    {
        Enrollment::where('user_id', $this->student->id)
            ->where('course_id', $this->course->id)
            ->update(['status' => Enrollment::STATUS_PENDING_PAYMENT]);

        $response = $this->actingAs($this->student)
            ->getJson(route('learning.playback', [$this->course, $this->lesson]));

        $response->assertForbidden();
    }

    public function test_student_cannot_access_playback_of_unpublished_lesson(): void
    {
        $response = $this->actingAs($this->student)
            ->getJson(route('learning.playback', [$this->course, $this->unpublishedLesson]));

        $response->assertForbidden();

        // Course instructor CAN access unpublished lesson playback
        $this->configureMockMuxProvider();
        $instResponse = $this->actingAs($this->instructor)
            ->getJson(route('learning.playback', [$this->course, $this->unpublishedLesson]));

        $instResponse->assertOk();
    }

    public function test_playback_rejects_foreign_lesson_id_with_404(): void
    {
        $response = $this->actingAs($this->student)
            ->getJson(route('learning.playback', [$this->course, $this->otherCourseLesson]));

        $response->assertNotFound();
    }

    public function test_playback_returns_unconfigured_status_when_provider_credentials_missing(): void
    {
        $this->unconfigureVideoProvider();

        $response = $this->actingAs($this->student)
            ->getJson(route('learning.playback', [$this->course, $this->lesson]));

        $response->assertOk();
        $response->assertJson([
            'status' => 'unconfigured',
        ]);
    }

    public function test_playback_returns_processing_status_when_asset_is_encoding(): void
    {
        $this->lesson->update(['video_status' => 'processing']);

        $response = $this->actingAs($this->student)
            ->getJson(route('learning.playback', [$this->course, $this->lesson]));

        $response->assertOk();
        $response->assertJson([
            'status' => 'processing',
        ]);
    }

    public function test_playback_returns_errored_status_when_encoding_failed(): void
    {
        $this->lesson->update([
            'video_status' => 'errored',
            'video_error_message' => 'Video file codec is unsupported.',
        ]);

        $response = $this->actingAs($this->student)
            ->getJson(route('learning.playback', [$this->course, $this->lesson]));

        $response->assertOk();
        $response->assertJson([
            'status' => 'errored',
        ]);
    }

    public function test_enrolled_student_receives_signed_playback_url_and_watermark_when_configured(): void
    {
        $this->configureMockMuxProvider();

        $response = $this->actingAs($this->student)
            ->getJson(route('learning.playback', [$this->course, $this->lesson]));

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'playback_id',
            'playback_url',
            'playback_token',
            'expires_at',
            'watermark' => [
                'student_id',
                'student_name',
                'student_email',
                'client_ip',
                'timestamp',
                'disclaimer',
            ],
        ]);

        $data = $response->json();
        $this->assertEquals('ready', $data['status']);
        $this->assertEquals('mock-signed-playback-id-123', $data['playback_id']);
        $this->assertStringContainsString('https://stream.mux.com/mock-signed-playback-id-123.m3u8?token=', $data['playback_url']);
        $this->assertEquals($this->student->email, $data['watermark']['student_email']);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Webhook Signature & Processing Tests
    |--------------------------------------------------------------------------
    */

    public function test_webhook_without_signature_is_rejected_with_401(): void
    {
        $this->configureMockMuxProvider();

        $response = $this->postJson(route('webhooks.video.mux'), [
            'type' => 'video.asset.ready',
        ]);

        $response->assertStatus(401);
    }

    public function test_webhook_with_invalid_signature_is_rejected_with_401(): void
    {
        $this->configureMockMuxProvider();

        $response = $this->withHeaders([
            'mux-signature' => 't='.time().',v1=invalid_hmac_hash_string',
        ])->postJson(route('webhooks.video.mux'), [
            'type' => 'video.asset.ready',
        ]);

        $response->assertStatus(401);
    }

    public function test_webhook_with_expired_timestamp_is_rejected(): void
    {
        $this->configureMockMuxProvider();

        $payload = json_encode(['type' => 'video.asset.ready']);
        $expiredTime = time() - 600; // 10 minutes ago
        $signature = hash_hmac('sha256', "{$expiredTime}.{$payload}", $this->testWebhookSecret);

        $response = $this->call(
            'POST',
            route('webhooks.video.mux'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_MUX_SIGNATURE' => "t={$expiredTime},v1={$signature}",
            ],
            $payload
        );

        $response->assertStatus(401);
    }

    public function test_valid_webhook_updates_lesson_when_asset_created(): void
    {
        $this->configureMockMuxProvider();

        $this->lesson->update([
            'video_upload_id' => 'mux-up-test-111',
            'video_status' => 'waiting_upload',
        ]);

        $payload = json_encode([
            'type' => 'video.upload.asset_created',
            'data' => [
                'id' => 'mux-up-test-111',
                'upload_id' => 'mux-up-test-111',
                'asset_id' => 'mux-asset-created-777',
            ],
        ]);

        $time = time();
        $signature = hash_hmac('sha256', "{$time}.{$payload}", $this->testWebhookSecret);

        $response = $this->call(
            'POST',
            route('webhooks.video.mux'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_MUX_SIGNATURE' => "t={$time},v1={$signature}",
            ],
            $payload
        );

        $response->assertOk();
        $this->assertDatabaseHas('lessons', [
            'id' => $this->lesson->id,
            'video_asset_id' => 'mux-asset-created-777',
            'video_status' => 'processing',
        ]);
    }

    public function test_valid_webhook_updates_lesson_when_asset_ready(): void
    {
        $this->configureMockMuxProvider();

        $this->lesson->update([
            'video_asset_id' => 'mux-asset-ready-888',
            'video_status' => 'processing',
        ]);

        $payload = json_encode([
            'type' => 'video.asset.ready',
            'data' => [
                'id' => 'mux-asset-ready-888',
                'status' => 'ready',
                'duration' => 1250.5, // ~21 minutes
                'aspect_ratio' => '16:9',
                'playback_ids' => [
                    [
                        'id' => 'signed-playback-888-xyz',
                        'policy' => 'signed',
                    ],
                ],
            ],
        ]);

        $time = time();
        $signature = hash_hmac('sha256', "{$time}.{$payload}", $this->testWebhookSecret);

        $response = $this->call(
            'POST',
            route('webhooks.video.mux'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_MUX_SIGNATURE' => "t={$time},v1={$signature}",
            ],
            $payload
        );

        $response->assertOk();
        $this->assertDatabaseHas('lessons', [
            'id' => $this->lesson->id,
            'video_playback_id' => 'signed-playback-888-xyz',
            'video_status' => 'ready',
            'video_aspect_ratio' => '16:9',
            'duration_minutes' => 21,
            'video_error_message' => null,
        ]);
    }

    public function test_valid_webhook_updates_lesson_when_asset_errored(): void
    {
        $this->configureMockMuxProvider();

        $this->lesson->update([
            'video_asset_id' => 'mux-asset-failed-999',
            'video_status' => 'processing',
        ]);

        $payload = json_encode([
            'type' => 'video.asset.errored',
            'data' => [
                'id' => 'mux-asset-failed-999',
                'status' => 'errored',
                'errors' => [
                    'messages' => ['Corrupted MP4 container header.'],
                ],
            ],
        ]);

        $time = time();
        $signature = hash_hmac('sha256', "{$time}.{$payload}", $this->testWebhookSecret);

        $response = $this->call(
            'POST',
            route('webhooks.video.mux'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_MUX_SIGNATURE' => "t={$time},v1={$signature}",
            ],
            $payload
        );

        $response->assertOk();
        $this->assertDatabaseHas('lessons', [
            'id' => $this->lesson->id,
            'video_status' => 'errored',
            'video_error_message' => 'Corrupted MP4 container header.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Token Expiration and Structure Tests
    |--------------------------------------------------------------------------
    */

    public function test_token_is_valid_rs256_jwt_with_correct_claims_and_future_expiry(): void
    {
        $this->configureMockMuxProvider();
        $provider = app(VideoService::class)->getProvider();

        $playbackId = 'playback-token-test-123';
        $ttl = 300; // 5 minutes
        $token = $provider->generateSignedPlaybackToken($playbackId, ['custom' => 'val'], $ttl);

        $parts = explode('.', $token);
        $this->assertCount(3, $parts, 'JWT must contain header, payload, and signature separated by dots');

        $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

        $this->assertEquals('RS256', $header['alg']);
        $this->assertEquals($this->testSigningKeyId, $header['kid']);

        $this->assertEquals($playbackId, $payload['sub']);
        $this->assertEquals('v', $payload['aud']);
        $this->assertEquals('val', $payload['custom']);
        $this->assertGreaterThan(time(), $payload['exp']);
        $this->assertLessThanOrEqual(time() + $ttl + 2, $payload['exp']);
    }
}
