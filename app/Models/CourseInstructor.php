<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseInstructor extends Model
{
    use HasFactory;

    protected $table = 'course_instructors';

    protected $fillable = [
        'course_id',
        'user_id',
        'is_primary',
        'can_manage_content',
        'can_grade',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'can_manage_content' => 'boolean',
            'can_grade' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
