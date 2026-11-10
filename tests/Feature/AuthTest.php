<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Create your LearnBuddy account');
    }

    public function test_new_users_can_register_with_valid_sri_lankan_phone(): void
    {
        Event::fake([Registered::class]);

        $response = $this->post('/register', [
            'name' => 'Sahan Jayawardena',
            'email' => 'sahan@example.com',
            'phone' => '0771234567',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('users', [
            'email' => 'sahan@example.com',
            'phone' => '+94771234567', // Normalized to E.164
            'locale' => 'en',
            'timezone' => 'Asia/Colombo',
        ]);

        Event::assertDispatched(Registered::class);
    }

    public function test_registration_normalizes_various_sri_lankan_phone_formats(): void
    {
        $testCases = [
            '071 234 5678' => '+94712345678',
            '+94 77 987 6543' => '+94779876543',
            '011 234 5678' => '+94112345678',
        ];

        foreach ($testCases as $input => $expected) {
            $email = 'user_'.uniqid().'@example.com';

            $this->post('/register', [
                'name' => 'Test User',
                'email' => $email,
                'phone' => $input,
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
                'terms' => '1',
            ]);

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'phone' => $expected,
            ]);

            auth()->logout();
        }
    }

    public function test_registration_rejects_invalid_sri_lankan_phone_number(): void
    {
        $response = $this->post('/register', [
            'name' => 'Invalid Phone User',
            'email' => 'invalid_phone@example.com',
            'phone' => '12345', // Too short, invalid Sri Lankan format
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertGuest();
    }

    public function test_registration_requires_terms_acceptance(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Terms User',
            'email' => 'noterms@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertSessionHasErrors(['terms']);
        $this->assertGuest();
    }

    public function test_registration_assigns_student_role_by_default(): void
    {
        $this->post('/register', [
            'name' => 'Default Role User',
            'email' => 'defaultrole@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
        ]);

        $user = User::where('email', 'defaultrole@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isStudent());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isInstructor());
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'valid@learnbuddy.lk',
            'password' => 'ValidPass123!',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'valid@learnbuddy.lk',
            'password' => 'ValidPass123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'valid@learnbuddy.lk',
            'password' => 'ValidPass123!',
        ]);

        $response = $this->post('/login', [
            'email' => 'valid@learnbuddy.lk',
            'password' => 'WrongPassword!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'inactive@learnbuddy.lk',
            'password' => 'ValidPass123!',
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@learnbuddy.lk',
            'password' => 'ValidPass123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_attempts_are_throttled_after_consecutive_failures(): void
    {
        User::factory()->create([
            'email' => 'throttle@learnbuddy.lk',
            'password' => 'ValidPass123!',
        ]);

        // 5 consecutive failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'throttle@learnbuddy.lk',
                'password' => 'WrongPassword',
            ]);
        }

        // 6th attempt must trigger rate limiting
        $response = $this->post('/login', [
            'email' => 'throttle@learnbuddy.lk',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Too many login attempts', $errorMessage);
    }

    public function test_users_can_logout_and_session_is_invalidated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Forgot password?');
    }

    public function test_password_reset_link_can_be_requested_without_user_enumeration(): void
    {
        // Existing user
        $user = User::factory()->create(['email' => 'existing@learnbuddy.lk']);
        $response1 = $this->post('/forgot-password', ['email' => 'existing@learnbuddy.lk']);
        $response1->assertSessionHas('status', 'If an account with that email exists, we have sent a password reset link.');

        // Non-existing user returns the exact same message
        $response2 = $this->post('/forgot-password', ['email' => 'nonexistent@learnbuddy.lk']);
        $response2->assertSessionHas('status', 'If an account with that email exists, we have sent a password reset link.');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'resetme@learnbuddy.lk']);
        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'resetme@learnbuddy.lk',
            'password' => 'BrandNewPass123!',
            'password_confirmation' => 'BrandNewPass123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(auth()->attempt([
            'email' => 'resetme@learnbuddy.lk',
            'password' => 'BrandNewPass123!',
        ]));
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('Verify your email');
    }

    public function test_user_cannot_view_another_users_profile_via_idor(): void
    {
        $userA = User::factory()->student()->create();
        $userB = User::factory()->student()->create();

        // User A attempts to view User B's profile
        $response = $this->actingAs($userA)->get(route('profile.show', $userB));

        // Must be blocked with 403 Forbidden by UserPolicy
        $response->assertStatus(403);
    }

    public function test_user_can_view_own_profile(): void
    {
        $user = User::factory()->student()->create();

        $response = $this->actingAs($user)->get(route('profile.show', $user));

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('Account Overview');
    }

    public function test_administrator_can_view_any_users_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();

        $response = $this->actingAs($admin)->get(route('profile.show', $student));

        $response->assertStatus(200);
        $response->assertSee($student->name);
    }

    public function test_role_middleware_blocks_unauthorized_users_with_403(): void
    {
        // Define temporary test route protected by 'role:admin'
        Route::get('/test-admin-gate', function () {
            return response()->json(['secret' => 'admin_area']);
        })->middleware(['auth', 'role:admin']);

        $student = User::factory()->student()->create();
        $admin = User::factory()->admin()->create();

        // Student accessing admin route -> 403 Forbidden
        $studentResponse = $this->actingAs($student)->get('/test-admin-gate');
        $studentResponse->assertStatus(403);

        // Admin accessing admin route -> 200 OK
        $adminResponse = $this->actingAs($admin)->get('/test-admin-gate');
        $adminResponse->assertStatus(200);
    }
}
