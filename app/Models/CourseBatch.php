<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_delivery_option_id',
        'location_id',
        'batch_code',
        'batch_name',
        'max_seats',
        'enrolled_seats',
        'registration_start_date',
        'registration_end_date',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'max_seats' => 'integer',
            'enrolled_seats' => 'integer',
            'registration_start_date' => 'date',
            'registration_end_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function deliveryOption(): BelongsTo
    {
        return $this->belongsTo(CourseDeliveryOption::class, 'course_delivery_option_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(PhysicalLocation::class, 'location_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BatchSchedule::class, 'batch_id');
    }

    public function availableSeats(): int
    {
        return max(0, $this->max_seats - $this->enrolled_seats);
    }

    public function isFull(): bool
    {
        return $this->enrolled_seats >= $this->max_seats;
    }
}
