<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed non-production demonstration accounts for manual testing.
     * Contains NO real personal data.
     */
    public function run(): void
    {
        // 1. Administrator Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@learnbuddy.lk'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('AdminPass123!'),
                'phone' => '+94771000001',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'province' => 'Western',
                'district' => 'Colombo',
                'city' => 'Colombo 03',
                'address_line_1' => 'No. 45 Galle Road',
                'postal_code' => '00300',
                'locale' => 'en',
                'timezone' => 'Asia/Colombo',
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // 2. Instructor Account
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@learnbuddy.lk'],
            [
                'name' => 'Dilshan Perera (Instructor)',
                'password' => Hash::make('InstructorPass123!'),
                'phone' => '+94771000002',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'province' => 'Central',
                'district' => 'Kandy',
                'city' => 'Peradeniya',
                'address_line_1' => 'No. 12 Kandy Road',
                'postal_code' => '20400',
                'locale' => 'en',
                'timezone' => 'Asia/Colombo',
                'is_active' => true,
            ]
        );
        $instructor->assignRole('instructor');

        // 3. Student Account
        $student = User::firstOrCreate(
            ['email' => 'student@learnbuddy.lk'],
            [
                'name' => 'Chamari Silva (Student)',
                'password' => Hash::make('StudentPass123!'),
                'phone' => '+94771000003',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'province' => 'Southern',
                'district' => 'Galle',
                'city' => 'Galle Fort',
                'address_line_1' => 'No. 88 Lighthouse Street',
                'postal_code' => '80000',
                'locale' => 'en',
                'timezone' => 'Asia/Colombo',
                'is_active' => true,
            ]
        );
        $student->assignRole('student');

        // 4. Inactive Student Account (for testing account deactivation rules)
        $inactive = User::firstOrCreate(
            ['email' => 'inactive@learnbuddy.lk'],
            [
                'name' => 'Suspended Account Test',
                'password' => Hash::make('StudentPass123!'),
                'phone' => '+94771000004',
                'phone_verified_at' => null,
                'email_verified_at' => now(),
                'province' => 'Northern',
                'district' => 'Jaffna',
                'city' => 'Jaffna Town',
                'postal_code' => '40000',
                'locale' => 'en',
                'timezone' => 'Asia/Colombo',
                'is_active' => false,
            ]
        );
        $inactive->assignRole('student');
    }
}
