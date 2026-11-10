<?php

namespace Database\Seeders;

use App\Models\PhysicalLocation;
use Illuminate\Database\Seeder;

class PhysicalLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Colombo City Campus (Lab 01)',
                'code' => 'COL-LAB-1',
                'province' => 'Western',
                'district' => 'Colombo',
                'city' => 'Colombo 03',
                'address_line_1' => 'No. 120 Galle Road, Kollupitiya',
                'postal_code' => '00300',
                'contact_phone' => '+94 11 234 5678',
                'max_room_capacity' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Kandy Highlands Tech Hub',
                'code' => 'KDY-HUB-1',
                'province' => 'Central',
                'district' => 'Kandy',
                'city' => 'Kandy',
                'address_line_1' => 'No. 45 Peradeniya Road',
                'postal_code' => '20000',
                'contact_phone' => '+94 81 222 3344',
                'max_room_capacity' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Galle Fort Digital Center',
                'code' => 'GLE-CTR-1',
                'province' => 'Southern',
                'district' => 'Galle',
                'city' => 'Galle',
                'address_line_1' => 'No. 88 Lighthouse Street, Galle Fort',
                'postal_code' => '80000',
                'contact_phone' => '+94 91 333 4455',
                'max_room_capacity' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($locations as $loc) {
            PhysicalLocation::firstOrCreate(['code' => $loc['code']], $loc);
        }
    }
}
