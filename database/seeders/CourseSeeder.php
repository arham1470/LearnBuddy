<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\PhysicalLocation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@learnbuddy.lk')->first();
        $instructor = User::where('email', 'instructor@learnbuddy.lk')->first();
        $softwareCategory = Category::where('slug', 'software-engineering')->first();
        $colomboLocation = PhysicalLocation::where('code', 'COL-LAB-1')->first();

        if (! $admin || ! $instructor || ! $softwareCategory) {
            return;
        }

        // 1. Published Course with Both Online and Physical delivery options
        $course1 = Course::firstOrCreate(
            ['slug' => 'fullstack-laravel-sri-lanka'],
            [
                'category_id' => $softwareCategory->id,
                'title' => 'Full-Stack Web Development with Laravel & Tailwind',
                'subtitle' => 'Comprehensive masterclass designed for developers and IT students across Sri Lanka.',
                'description' => 'Master industry-standard software architecture using Laravel, Tailwind CSS, Alpine.js, and Aiven Cloud MySQL with mandatory SSL encryption. Covers authentication, role-based authorization, REST APIs, and production deployment.',
                'difficulty_level' => 'intermediate',
                'language' => 'English',
                'duration_hours' => 36.0,
                'learning_outcomes' => [
                    'Architect enterprise-grade web applications in Laravel',
                    'Implement secure role-based authorization and IDOR protections',
                    'Configure cloud database clusters over TLS 1.3 encryption',
                    'Build responsive, mobile-first Blade and Tailwind layouts',
                ],
                'prerequisites' => [
                    'Basic familiarity with PHP syntax and object-oriented programming',
                    'Understanding of HTML and CSS fundamentals',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => now()->subDays(5),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $course1->instructors()->syncWithoutDetaching([
            $instructor->id => [
                'is_primary' => true,
                'can_manage_content' => true,
                'can_grade' => true,
            ],
            $admin->id => [
                'is_primary' => false,
                'can_manage_content' => true,
                'can_grade' => true,
            ],
        ]);

        // Online delivery option
        $onlineOpt = CourseDeliveryOption::firstOrCreate(
            ['course_id' => $course1->id, 'delivery_mode' => 'online', 'title' => 'Online Self-Paced Learning'],
            [
                'description' => 'Immediate on-demand streaming of all lesson videos, downloadable starter kits, and digital certificate.',
                'price' => 7500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'access_period_days' => null, // Lifetime
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => false,
                'includes_live_classes' => false,
                'includes_certificate' => true,
            ]
        );

        // Physical delivery option
        $physicalOpt = CourseDeliveryOption::firstOrCreate(
            ['course_id' => $course1->id, 'delivery_mode' => 'physical', 'title' => 'In-Person Physical Classroom (Colombo)'],
            [
                'description' => 'Weekend in-person lectures and guided lab practicals at our Colombo City Campus.',
                'price' => 22500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'max_capacity' => 25,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => true,
                'includes_live_classes' => false,
                'includes_certificate' => true,
            ]
        );

        if ($colomboLocation && $physicalOpt->batches()->doesntExist()) {
            CourseBatch::create([
                'course_delivery_option_id' => $physicalOpt->id,
                'location_id' => $colomboLocation->id,
                'batch_code' => 'BAT-COL-2026-B1',
                'batch_name' => 'Colombo Weekend Intake 01',
                'max_seats' => 25,
                'enrolled_seats' => 0,
                'registration_start_date' => now()->subDays(2),
                'registration_end_date' => now()->addDays(20),
                'start_date' => now()->addDays(25),
                'end_date' => now()->addDays(85),
                'status' => 'open_for_enrollment',
            ]);
        }

        // Add curriculum sections & lessons
        if ($course1->sections()->doesntExist()) {
            $section1 = $course1->sections()->create([
                'title' => 'Architecture & Laravel Core Foundations',
                'description' => 'Understand the MVC lifecycle, service containers, and migration patterns.',
                'sort_order' => 1,
                'is_published' => true,
            ]);

            $section1->lessons()->create([
                'title' => '1.1 System Architecture & High-Level Domain Modeling',
                'slug' => 'system-architecture-high-level-domain-modeling',
                'type' => 'video',
                'duration_minutes' => 24,
                'sort_order' => 1,
                'is_preview' => true,
                'is_published' => true,
            ]);

            $section1->lessons()->create([
                'title' => '1.2 Secure Cloud Database Connections & SSL Verification',
                'slug' => 'secure-cloud-database-connections-ssl-verification',
                'type' => 'video',
                'duration_minutes' => 32,
                'sort_order' => 2,
                'is_preview' => false,
                'is_published' => true,
            ]);

            $section2 = $course1->sections()->create([
                'title' => 'Authentication, Authorization & IDOR Protection',
                'description' => 'Deep dive into server-side Gates, Policies, and login throttling.',
                'sort_order' => 2,
                'is_published' => true,
            ]);

            $section2->lessons()->create([
                'title' => '2.1 Role-Based Access Control and Principle of Least Privilege',
                'slug' => 'role-based-access-control-principle-least-privilege',
                'type' => 'video',
                'duration_minutes' => 28,
                'sort_order' => 1,
                'is_preview' => false,
                'is_published' => true,
            ]);
        }

        // 2. Draft Course (Authored by instructor)
        $course2 = Course::firstOrCreate(
            ['slug' => 'ui-ux-design-systems-mastery'],
            [
                'category_id' => Category::where('slug', 'ui-ux-design')->first()->id ?? $softwareCategory->id,
                'title' => 'Design Systems & Responsive Web Interfaces',
                'subtitle' => 'Building scalable design tokens, accessible components, and Figma foundations.',
                'description' => 'Learn how to build design systems from atomic tokens to complete production web applications. Covers responsive viewport adaptations from 360px mobile up to 4K ultra-wide monitors.',
                'difficulty_level' => 'all_levels',
                'language' => 'English',
                'duration_hours' => 18.0,
                'learning_outcomes' => [
                    'Establish consistent typography, spacing, and brand color tokens',
                    'Design accessible components with WCAG AAA contrast',
                ],
                'status' => Course::STATUS_DRAFT,
                'published_at' => null,
                'created_by_user_id' => $instructor->id,
            ]
        );

        $course2->instructors()->syncWithoutDetaching([
            $instructor->id => [
                'is_primary' => true,
                'can_manage_content' => true,
                'can_grade' => true,
            ],
        ]);

        CourseDeliveryOption::firstOrCreate(
            ['course_id' => $course2->id, 'delivery_mode' => 'online', 'title' => 'Online Self-Paced'],
            [
                'description' => 'Full access to design system workshops.',
                'price' => 6000.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'includes_recorded_lessons' => true,
                'includes_certificate' => true,
            ]
        );

        // 3. Published Free Online Course (Web Development)
        $webCategory = Category::where('slug', 'web-development')->first() ?? $softwareCategory;
        $course3 = Course::firstOrCreate(
            ['slug' => 'modern-frontend-tailwind-alpine'],
            [
                'category_id' => $webCategory->id,
                'title' => 'Modern Frontend Engineering with Tailwind CSS & Alpine.js',
                'subtitle' => 'Build reactive, lightweight user interfaces without heavy JavaScript bundle overhead.',
                'description' => 'A hands-on, zero-fluff introduction to modern frontend development. Learn how to combine utility-first CSS with declarative Alpine.js components for accessible modals, accordions, and dropdowns.',
                'difficulty_level' => 'beginner',
                'language' => 'English',
                'duration_hours' => 12.0,
                'learning_outcomes' => [
                    'Master Tailwind CSS 4 utility fundamentals and dark mode',
                    'Construct declarative interactive widgets with Alpine.js',
                    'Prevent horizontal overflow and implement WCAG contrast standards',
                ],
                'prerequisites' => [
                    'Basic understanding of HTML elements and CSS selectors',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => now()->subDays(10),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $course3->instructors()->syncWithoutDetaching([
            $instructor->id => [
                'is_primary' => true,
                'can_manage_content' => true,
                'can_grade' => true,
            ],
        ]);

        CourseDeliveryOption::firstOrCreate(
            ['course_id' => $course3->id, 'delivery_mode' => 'online', 'title' => 'Free Self-Paced Online Stream'],
            [
                'description' => 'Full immediate streaming access to all modules, code repositories, and quizzes.',
                'price' => 0.00,
                'currency' => 'LKR',
                'is_free' => true,
                'is_active' => true,
                'includes_recorded_lessons' => true,
                'includes_certificate' => true,
            ]
        );

        if ($course3->sections()->doesntExist()) {
            $sec = $course3->sections()->create([
                'title' => 'Introduction to Utility-First Design',
                'description' => 'Break down monolithic stylesheets into composable design tokens.',
                'sort_order' => 1,
                'is_published' => true,
            ]);

            $sec->lessons()->create([
                'title' => 'Setting up Vite and Tailwind in a Modern App',
                'slug' => 'setting-up-vite-and-tailwind-in-a-modern-app',
                'type' => 'video',
                'duration_minutes' => 18,
                'sort_order' => 1,
                'is_preview' => true,
                'is_published' => true,
            ]);
        }

        // Add valid demonstration reviews for Course 3
        $student = User::where('email', 'student@learnbuddy.lk')->first();
        if ($student) {
            Review::firstOrCreate(
                ['course_id' => $course3->id, 'user_id' => $student->id],
                [
                    'rating' => 5,
                    'title' => 'Exceptionally practical and clean explanation!',
                    'comment' => 'The lessons on Alpine.js modal dispatches and Tailwind responsive utilities cleared up so much confusion. Highly recommended for any web developer in Sri Lanka.',
                    'is_published' => true,
                ]
            );
        }

        // 4. Published Physical Classroom Course in Kandy (Cloud & DevOps)
        $cloudCategory = Category::where('slug', 'cloud-devops')->first() ?? $softwareCategory;
        $kandyLocation = PhysicalLocation::where('code', 'KAN-LAB-1')->first();

        $course4 = Course::firstOrCreate(
            ['slug' => 'cloud-devops-mysql-aiven'],
            [
                'category_id' => $cloudCategory->id,
                'title' => 'Cloud Infrastructure & Managed MySQL Clusters on Aiven',
                'subtitle' => 'Hands-on enterprise database administration, automated backups, and SSL/TLS configuration.',
                'description' => 'An in-depth infrastructure course focused on production-grade cloud databases. Learn to provision Aiven MySQL clusters, enforce mandatory TLS 1.3 certificates, monitor connection pools, and design zero-downtime migration workflows.',
                'difficulty_level' => 'advanced',
                'language' => 'English',
                'duration_hours' => 20.0,
                'learning_outcomes' => [
                    'Provision and configure managed cloud database clusters',
                    'Implement mandatory CA certificate verification and SSL encryption',
                    'Design rollback-safe relational database schema migrations',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => now()->subDays(3),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $course4->instructors()->syncWithoutDetaching([
            $instructor->id => [
                'is_primary' => true,
                'can_manage_content' => true,
                'can_grade' => true,
            ],
        ]);

        $kandyDeliveryOpt = CourseDeliveryOption::firstOrCreate(
            ['course_id' => $course4->id, 'delivery_mode' => 'physical', 'title' => 'In-Person Weekend Lab (Kandy)'],
            [
                'description' => 'Hands-on weekend computer lab sessions held at our Kandy Campus with direct instructor supervision.',
                'price' => 18500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'max_capacity' => 20,
                'includes_physical_sessions' => true,
                'includes_certificate' => true,
            ]
        );

        if ($kandyLocation && $kandyDeliveryOpt->batches()->doesntExist()) {
            CourseBatch::create([
                'course_delivery_option_id' => $kandyDeliveryOpt->id,
                'location_id' => $kandyLocation->id,
                'batch_code' => 'BAT-KAN-2026-B1',
                'batch_name' => 'Kandy Saturday Morning Lab Intake',
                'max_seats' => 20,
                'enrolled_seats' => 0,
                'registration_start_date' => now()->subDays(5),
                'registration_end_date' => now()->addDays(15),
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(70),
                'status' => 'open_for_enrollment',
            ]);
        }

        // 5. Published Sinhala Medium Course (Web Development)
        $course5 = Course::firstOrCreate(
            ['slug' => 'sinhala-web-development-foundations'],
            [
                'category_id' => $webCategory->id,
                'title' => 'Web Development Foundations (සිංහල මාධ්‍යයෙන්)',
                'subtitle' => 'HTML, CSS සහ Modern JavaScript මූලධර්ම මුලසිට සරලව ඉගෙන ගන්න.',
                'description' => 'ශ්‍රී ලාංකික සිසුන් සහ ආරම්භකයින් සඳහා විශේෂයෙන් සකස් කරන ලද වෙබ් නිර්මාණකරණ පාඨමාලාව. HTML5, CSS3 සහ JavaScript මුලසිට සරල සිංහලෙන් ඉගෙන ගන්න.',
                'difficulty_level' => 'beginner',
                'language' => 'Sinhala',
                'duration_hours' => 16.0,
                'learning_outcomes' => [
                    'වෙබ් අඩවි නිර්මාණය සඳහා මූලික තාක්ෂණයන් නිවැරදිව භාවිතය',
                    'Mobile-friendly සහ Responsive Layouts නිර්මාණය',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => now()->subDays(7),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $course5->instructors()->syncWithoutDetaching([
            $instructor->id => [
                'is_primary' => true,
                'can_manage_content' => true,
                'can_grade' => true,
            ],
        ]);

        CourseDeliveryOption::firstOrCreate(
            ['course_id' => $course5->id, 'delivery_mode' => 'online', 'title' => 'Online Self-Paced (Sinhala)'],
            [
                'description' => 'Online video recordings and exercises in Sinhala.',
                'price' => 4500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'includes_recorded_lessons' => true,
                'includes_certificate' => true,
            ]
        );
    }
}
