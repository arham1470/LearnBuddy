<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseDeliveryOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'delivery_mode',
        'title',
        'description',
        'price',
        'currency',
        'is_free',
        'is_active',
        'max_capacity',
        'access_period_days',
        'includes_recorded_lessons',
        'includes_physical_sessions',
        'includes_live_classes',
        'includes_certificate',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'max_capacity' => 'integer',
            'access_period_days' => 'integer',
            'includes_recorded_lessons' => 'boolean',
            'includes_physical_sessions' => 'boolean',
            'includes_live_classes' => 'boolean',
            'includes_certificate' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(CourseBatch::class, 'course_delivery_option_id');
    }

    public function formattedPrice(): string
    {
        if ($this->is_free || (float) $this->price === 0.0) {
            return 'Free';
        }

        return $this->currency.' '.number_format((float) $this->price, 2);
    }
}
