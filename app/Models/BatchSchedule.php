<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room_number',
        'notes',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'batch_id');
    }
}
