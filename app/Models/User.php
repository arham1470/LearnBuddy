<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
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
}
