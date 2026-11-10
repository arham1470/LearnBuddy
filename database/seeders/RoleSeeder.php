<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Core System Roles
        $admin = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Administrator',
            'description' => 'Full administrative access and system management across LearnBuddy Sri Lanka.',
        ]);

        $instructor = Role::firstOrCreate(['name' => 'instructor'], [
            'display_name' => 'Instructor',
            'description' => 'Course creation, section/lesson management, live classes, assignments, and student grading.',
        ]);

        $student = Role::firstOrCreate(['name' => 'student'], [
            'display_name' => 'Student',
            'description' => 'Course discovery, enrollment, lesson learning, assignment submissions, and certificates.',
        ]);

        // 2. Define Granular Permissions
        $permissions = [
            ['name' => 'users.manage', 'group' => 'users', 'description' => 'Create, edit, activate, and deactivate users'],
            ['name' => 'courses.manage', 'group' => 'courses', 'description' => 'Create and modify course curricula and materials'],
            ['name' => 'courses.publish', 'group' => 'courses', 'description' => 'Approve and publish courses to the public catalogue'],
            ['name' => 'batches.manage', 'group' => 'batches', 'description' => 'Schedule physical and hybrid batches and venues'],
            ['name' => 'enrollments.manage', 'group' => 'enrollments', 'description' => 'Approve and manage student enrollments'],
            ['name' => 'finance.view', 'group' => 'finance', 'description' => 'View payment transactions and revenue reports'],
            ['name' => 'lessons.view', 'group' => 'learning', 'description' => 'Access authorized course lessons and videos'],
        ];

        foreach ($permissions as $item) {
            $permission = Permission::firstOrCreate(['name' => $item['name']], [
                'group' => $item['group'],
                'description' => $item['description'],
            ]);

            // Assign all permissions to Administrator
            $admin->permissions()->syncWithoutDetaching([$permission->id]);

            // Assign instructor permissions
            if (in_array($item['name'], ['courses.manage', 'batches.manage', 'lessons.view'])) {
                $instructor->permissions()->syncWithoutDetaching([$permission->id]);
            }

            // Assign student permissions
            if (in_array($item['name'], ['lessons.view'])) {
                $student->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }
    }
}
