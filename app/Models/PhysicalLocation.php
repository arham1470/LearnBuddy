<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhysicalLocation extends Model
{
    use HasFactory;

    public const PROVINCES = [
        'Western',
        'Central',
        'Southern',
        'Northern',
        'Eastern',
        'North Western',
        'North Central',
        'Uva',
        'Sabaragamuwa',
    ];

    public const DISTRICTS = [
        'Western' => ['Colombo', 'Gampaha', 'Kalutara'],
        'Central' => ['Kandy', 'Matale', 'Nuwara Eliya'],
        'Southern' => ['Galle', 'Matara', 'Hambantota'],
        'Northern' => ['Jaffna', 'Kilinochchi', 'Mannar', 'Vavuniya', 'Mullaitivu'],
        'Eastern' => ['Batticaloa', 'Ampara', 'Trincomalee'],
        'North Western' => ['Kurunegala', 'Puttalam'],
        'North Central' => ['Anuradhapura', 'Polonnaruwa'],
        'Uva' => ['Badulla', 'Monaragala'],
        'Sabaragamuwa' => ['Ratnapura', 'Kegalle'],
    ];

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

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->district,
            $this->postal_code ?: null,
            $this->province.' Province',
            'Sri Lanka',
        ]);

        return implode(', ', $parts);
    }

    public function shortLocation(): string
    {
        return "{$this->city}, {$this->district} ({$this->province})";
    }
}
