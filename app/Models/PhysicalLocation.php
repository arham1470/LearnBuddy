<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhysicalLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'province',
        'district',
        'city',
        'address_line_1',
        'address_line_2',
        'postal_code',
        'google_map_embed_url',
        'contact_phone',
        'max_room_capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'max_room_capacity' => 'integer',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(CourseBatch::class, 'location_id');
    }
}
