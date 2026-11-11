<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchScheduleChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'changed_by_user_id',
        'change_type',
        'previous_details',
        'new_details',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'previous_details' => 'array',
            'new_details' => 'array',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'batch_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
