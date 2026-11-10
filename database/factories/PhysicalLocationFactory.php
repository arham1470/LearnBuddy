<?php

namespace Database\Factories;

use App\Models\PhysicalLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhysicalLocationFactory extends Factory
{
    protected $model = PhysicalLocation::class;

    public function definition(): array
    {
        return [
            'name' => 'Campus '.fake()->city(),
            'code' => 'LOC-'.fake()->unique()->numerify('####'),
            'province' => 'Western',
            'district' => 'Colombo',
            'city' => 'Colombo',
            'address_line_1' => fake()->streetAddress(),
            'max_room_capacity' => 30,
            'is_active' => true,
        ];
    }
}
