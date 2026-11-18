<?php

namespace App\Models;

use App\Notifications\PasswordResetNotification;
use App\Notifications\RegistrationVerificationNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) ($this->is_active && ($this->isAdmin() || $this->isInstructor()));
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? asset($this->avatar_url) : null;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'phone_verified_at',
        'avatar_url',
        'bio',
        'province',
        'district',
        'city',
        'address_line_1',
        'address_line_2',
        'postal_code',
        'locale',
        'timezone',
        'is_active',
        'is_approved',
        'approved_at',
        'approved_by',
        'email_otp',
        'email_otp_expires_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_otp',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'email_otp_expires_at' => 'datetime',
            'approved_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
            'is_approved' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Roles assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    /**
     * Courses created by the user.
     */
    public function createdCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by_user_id');
    }

    /**
     * Courses where the user is an assigned instructor.
     */
    public function taughtCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_instructors')
            ->withPivot(['is_primary', 'can_manage_content', 'can_grade'])
            ->withTimestamps();
    }

    /**
     * Only published courses taught by the user.
     */
    public function publishedTaughtCourses(): BelongsToMany
    {
        return $this->taughtCourses()->where('status', Course::STATUS_PUBLISHED);
    }

    /**
     * All student enrollments for this user.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Active enrollments for this user.
     */
    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', Enrollment::STATUS_ACTIVE);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Courses the user is enrolled in.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['id', 'course_delivery_option_id', 'course_batch_id', 'enrollment_code', 'delivery_mode', 'status', 'price_paid', 'is_free', 'enrolled_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Check if user is actively or pending enrolled in a course.
     */
    public function isEnrolledIn(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_PENDING_PAYMENT])
            ->exists();
    }

    /**
     * Get user's enrollment for a specific course.
     */
    public function getEnrollmentFor(Course $course): ?Enrollment
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->latest()
            ->first();
    }

    /**
     * Check if user has a specific role or any of the given roles.
     *
     * @param  string|array<int, string>  $role
     */
    public function hasRole(string|array $role): bool
    {
        $roles = is_array($role) ? $role : [$role];

        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    /**
     * Check if user has a specific permission via their roles.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string|Role $role): void
    {
        $roleModel = is_string($role)
            ? Role::firstOrCreate(['name' => $role], [
                'display_name' => ucfirst($role),
                'description' => ucfirst($role).' role',
            ])
            : $role;

        $this->roles()->syncWithoutDetaching([$roleModel->id]);
        $this->load('roles');
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string|Role $role): void
    {
        $roleId = is_string($role)
            ? Role::where('name', $role)->value('id')
            : $role->id;

        if ($roleId) {
            $this->roles()->detach($roleId);
            $this->load('roles');
        }
    }

    /**
     * Helper check for Administrator role.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Helper check for Instructor role.
     */
    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }

    /**
     * Helper check for Student role.
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Formats phone number for display (e.g., +94 77 123 4567).
     */
    public function formattedPhone(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        $cleaned = preg_replace('/[^\d+]/', '', $this->phone);

        if (preg_match('/^\+94(\d{2})(\d{3})(\d{4})$/', $cleaned, $matches)) {
            return "+94 {$matches[1]} {$matches[2]} {$matches[3]}";
        }

        return $this->phone;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function instructorApplications(): HasMany
    {
        return $this->hasMany(InstructorApplication::class);
    }

    public function latestInstructorApplication(): HasOne
    {
        return $this->hasOne(InstructorApplication::class)->latestOfMany();
    }

    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(CourseAnnouncement::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Administrator who approved this user.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Determine if the user's account has been approved by an administrator.
     */
    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    /**
     * Approve the user's account and activate access.
     */
    public function approve(?User $approver = null): void
    {
        $this->forceFill([
            'is_approved' => true,
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => $approver?->id ?? auth()->id(),
        ])->save();
    }

    /**
     * Generate a new 6-digit numeric OTP valid for 15 minutes.
     */
    public function generateEmailOtp(): string
    {
        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'email_otp' => $code,
            'email_otp_expires_at' => now()->addMinutes(15),
        ])->save();

        return $code;
    }

    /**
     * Verify an entered 6-digit OTP code.
     */
    public function verifyEmailOtp(string $code): bool
    {
        if (empty($this->email_otp) || empty($this->email_otp_expires_at)) {
            return false;
        }

        if (now()->isAfter($this->email_otp_expires_at)) {
            return false;
        }

        if (trim($code) !== trim($this->email_otp)) {
            return false;
        }

        $this->forceFill([
            'email_verified_at' => now(),
            'email_otp' => null,
            'email_otp_expires_at' => null,
        ])->save();

        return true;
    }

    /**
     * Send queued custom branded email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new RegistrationVerificationNotification);
    }

    /**
     * Send queued custom branded password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordResetNotification($token));
    }
}
