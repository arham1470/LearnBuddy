<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\CourseSection;
use App\Models\InstructorApplication;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Models\BatchSchedule;
use App\Models\BatchSession;
use App\Models\PhysicalLocation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleInstructorCoursesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Categories Exist
        $cloudCategory = Category::firstOrCreate(['slug' => 'cloud-devops'], [
            'name' => 'Cloud & DevOps',
            'icon' => 'fas fa-cloud',
            'description' => 'Cloud infrastructure, containerization, and automated deployments.',
            'sort_order' => 1,
        ]);

        $seCategory = Category::firstOrCreate(['slug' => 'software-engineering'], [
            'name' => 'Software Engineering',
            'icon' => 'fas fa-laptop-code',
            'description' => 'Full-stack software architecture, Laravel, backend systems, and enterprise design patterns.',
            'sort_order' => 2,
        ]);

        $designCategory = Category::firstOrCreate(['slug' => 'ui-ux-design'], [
            'name' => 'UI/UX Design',
            'icon' => 'fas fa-palette',
            'description' => 'User research, wireframing, high-fidelity prototypes, and mobile-first interface design.',
            'sort_order' => 3,
        ]);

        $webCategory = Category::firstOrCreate(['slug' => 'web-development'], [
            'name' => 'Web Development',
            'icon' => 'fas fa-code',
            'description' => 'Modern frontend and responsive web development using Tailwind CSS and modern frameworks.',
            'sort_order' => 4,
        ]);

        // 2. Ensure Physical Locations Exist
        $colomboLoc = PhysicalLocation::firstOrCreate(['code' => 'COL-LAB-1'], [
            'name' => 'Colombo City Campus (Lab 01)',
            'province' => 'Western',
            'district' => 'Colombo',
            'city' => 'Colombo 03',
            'address_line_1' => 'No. 120 Galle Road, Kollupitiya',
            'postal_code' => '00300',
            'contact_phone' => '+94 11 234 5678',
            'max_room_capacity' => 30,
            'is_active' => true,
        ]);

        $kandyLoc = PhysicalLocation::firstOrCreate(['code' => 'KDY-HUB-1'], [
            'name' => 'Kandy Highlands Tech Hub',
            'province' => 'Central',
            'district' => 'Kandy',
            'city' => 'Kandy',
            'address_line_1' => 'No. 45 Peradeniya Road',
            'postal_code' => '20000',
            'contact_phone' => '+94 81 222 3344',
            'max_room_capacity' => 25,
            'is_active' => true,
        ]);

        $galleLoc = PhysicalLocation::firstOrCreate(['code' => 'GLE-CTR-1'], [
            'name' => 'Galle Fort Digital Center',
            'province' => 'Southern',
            'district' => 'Galle',
            'city' => 'Galle',
            'address_line_1' => 'No. 88 Lighthouse Street, Galle Fort',
            'postal_code' => '80000',
            'contact_phone' => '+94 91 333 4455',
            'max_room_capacity' => 20,
            'is_active' => true,
        ]);

        // 3. Create Dedicated Instructor Account
        $instructor = User::updateOrCreate(
            ['email' => 'kusal.instructor@learnbuddy.lk'],
            [
                'name' => 'Dr. Kusal Mendis',
                'password' => Hash::make('InstructorPass123!'),
                'phone' => '+94772000005',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'bio' => 'Senior Cloud Architect and Principal Software Engineer with 12+ years of industry experience across Sri Lanka and Southeast Asia. Specializes in scalable microservices, containerization, Laravel ecosystems, and high-performance database architectures.',
                'province' => 'Western',
                'district' => 'Colombo',
                'city' => 'Colombo 03',
                'address_line_1' => 'No. 58 Duplication Road',
                'postal_code' => '00300',
                'locale' => 'en',
                'timezone' => 'Asia/Colombo',
                'is_active' => true,
                'is_approved' => true,
                'approved_at' => now(),
            ]
        );
        $instructor->assignRole('instructor');

        // Create or update approved instructor application
        InstructorApplication::updateOrCreate(
            ['user_id' => $instructor->id],
            [
                'name' => $instructor->name,
                'email' => $instructor->email,
                'phone' => $instructor->phone,
                'headline' => 'Principal Cloud Architect & Senior University Lecturer',
                'bio' => $instructor->bio,
                'subject_areas' => ['Cloud & DevOps', 'Software Engineering', 'UI/UX Design Systems', 'Database Optimization'],
                'languages' => ['English', 'Sinhala'],
                'linkedin_url' => 'https://linkedin.com/in/kusal-mendis-tech',
                'portfolio_url' => 'https://kusalmendis.dev',
                'sample_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status' => 'approved',
                'admin_notes' => 'Verified verified credentials, doctoral qualifications in software architecture, and 10+ years teaching experience.',
                'reviewed_at' => now(),
            ]
        );

        $now = Carbon::now('Asia/Colombo');

        // -------------------------------------------------------------------------
        // Course 1: (Physical + Online) Full-Stack Cloud & DevOps Mastery with AWS & Docker
        // -------------------------------------------------------------------------
        $course1 = Course::updateOrCreate(
            ['slug' => 'cloud-devops-aws-docker-mastery'],
            [
                'category_id' => $cloudCategory->id,
                'title' => 'Full-Stack Cloud & DevOps Mastery with AWS & Docker',
                'subtitle' => 'Master production containerization, CI/CD pipelines, Kubernetes, and enterprise cloud infrastructure.',
                'description' => 'A comprehensive, industry-grade masterclass covering Docker containers, multi-stage builds, automated GitHub Actions CI/CD, AWS ECS/EKS deployments, and Terraform infrastructure-as-code. Designed for developers and DevOps engineers transitioning to high-scale production systems.',
                'difficulty_level' => 'intermediate',
                'language' => 'English',
                'duration_hours' => 48.0,
                'learning_outcomes' => [
                    'Build production-optimized Docker images with multi-stage builds',
                    'Architect automated CI/CD deployment pipelines on AWS',
                    'Provision scalable cloud infrastructure using Terraform',
                    'Implement cluster monitoring, centralized logging, and disaster recovery',
                ],
                'prerequisites' => [
                    'Basic familiarity with Linux command-line terminal commands',
                    'Foundational understanding of web application backends',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays(10),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $this->attachInstructor($course1, $instructor);

        // Course 1: Delivery Options
        $c1Online = CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course1->id, 'delivery_mode' => 'online', 'title' => 'Online Self-Paced & Live Lab Access'],
            [
                'description' => 'Full on-demand streaming of all recorded lectures, project repositories, digital badge, and weekly online live mentor Q&A sessions.',
                'price' => 14500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'access_period_days' => 365,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => false,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ]
        );

        $c1Physical = CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course1->id, 'delivery_mode' => 'physical', 'title' => 'In-Person Weekend Bootcamp (Colombo Campus)'],
            [
                'description' => 'Hands-on weekend in-person classroom training in Colombo with dedicated workstation labs, instructor guidance, and networking with industry peers.',
                'price' => 38500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'max_capacity' => 24,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => true,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ]
        );

        // Course 1: Physical Batch & Week Schedule
        $batch1 = CourseBatch::updateOrCreate(
            ['batch_code' => 'BAT-DEV-2026-COL1'],
            [
                'course_delivery_option_id' => $c1Physical->id,
                'location_id' => $colomboLoc->id,
                'batch_name' => 'Colombo Weekend Cloud Cohort 01',
                'max_seats' => 24,
                'enrolled_seats' => 6,
                'registration_start_date' => $now->copy()->subDays(15)->toDateString(),
                'registration_end_date' => $now->copy()->addDays(14)->toDateString(),
                'start_date' => $now->copy()->addDays(20)->toDateString(),
                'end_date' => $now->copy()->addDays(110)->toDateString(),
                'status' => 'open_for_enrollment',
            ]
        );

        $this->seedBatchSchedules($batch1, [
            ['day_of_week' => 'Saturday', 'start_time' => '09:00:00', 'end_time' => '13:00:00', 'room_number' => 'Lab A-101', 'notes' => 'Hands-on Docker containerization practicals & cluster networking'],
            ['day_of_week' => 'Sunday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'room_number' => 'Lab A-101', 'notes' => 'AWS Cloud Infrastructure, Terraform scripting & deployment'],
        ]);

        $this->seedBatchSessions($batch1, $instructor, [
            ['offset_days' => 20, 'day' => 'Saturday', 'start_time' => '09:00:00', 'end_time' => '13:00:00', 'topic' => 'Orientation & Linux Container Runtime Internals', 'room' => 'Lab A-101'],
            ['offset_days' => 21, 'day' => 'Sunday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'topic' => 'Multi-Stage Dockerfiles & Optimization Lab', 'room' => 'Lab A-101'],
            ['offset_days' => 27, 'day' => 'Saturday', 'start_time' => '09:00:00', 'end_time' => '13:00:00', 'topic' => 'Docker Compose & Multi-Container Microservices', 'room' => 'Lab A-101'],
            ['offset_days' => 28, 'day' => 'Sunday', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'topic' => 'AWS IAM, VPC Architecture & Security Groups', 'room' => 'Lab A-101'],
        ]);

        $this->seedLiveClasses($course1, $instructor, $batch1, [
            ['title' => 'Live Cloud Architecture & Microservices Q&A', 'start_offset_days' => 25, 'duration_hours' => 2],
        ]);

        $this->seedCurriculum($course1, [
            [
                'title' => 'Module 1: Modern Cloud Infrastructure & Linux Foundations',
                'description' => 'Operating system primitives, process isolation, namespaces, and cloud storage paradigms.',
                'lessons' => [
                    ['title' => '1.1 Introduction to Cloud-Native Paradigms & Architecture', 'duration' => 25, 'preview' => true],
                    ['title' => '1.2 Linux Kernel Namespaces, CGroups & Isolation', 'duration' => 38, 'preview' => false],
                    ['title' => '1.3 SSH Tunneling, Firewall Rules & TLS Security', 'duration' => 32, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 2: Containerization with Docker & Multi-Container Setups',
                'description' => 'From basic container runtimes to hardened multi-stage production images and health checks.',
                'lessons' => [
                    ['title' => '2.1 Docker Engine Architecture and Layer Caching', 'duration' => 30, 'preview' => true],
                    ['title' => '2.2 Building Minimalist Alpine-Based Production Images', 'duration' => 42, 'preview' => false],
                    ['title' => '2.3 Multi-Service Orchestration with Docker Compose', 'duration' => 45, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 3: AWS Deployment, CI/CD & Automated Orchestration',
                'description' => 'Automated deployment pipelines using GitHub Actions to Amazon ECS and RDS clusters.',
                'lessons' => [
                    ['title' => '3.1 Provisioning Cloud Networks: VPC, Subnets & Gateways', 'duration' => 50, 'preview' => false],
                    ['title' => '3.2 GitHub Actions Workflows for Continuous Deployment', 'duration' => 40, 'preview' => false],
                    ['title' => '3.3 Observability: Centralized Logging & Alerting with CloudWatch', 'duration' => 35, 'preview' => false],
                ],
            ],
        ]);

        // -------------------------------------------------------------------------
        // Course 2: (Physical + Online) Advanced Laravel 11 & Enterprise API Architecture
        // -------------------------------------------------------------------------
        $course2 = Course::updateOrCreate(
            ['slug' => 'advanced-laravel-11-enterprise-architecture'],
            [
                'category_id' => $seCategory->id,
                'title' => 'Advanced Laravel 11 & Enterprise API Architecture',
                'subtitle' => 'Domain-driven design, asynchronous queues, microservices, and high-concurrency caching.',
                'description' => 'Deep dive into building fault-tolerant enterprise web applications. Covers custom service providers, pipeline design patterns, Redis distributed queues, CQRS principles, and bulletproof security against IDOR and race conditions.',
                'difficulty_level' => 'advanced',
                'language' => 'English',
                'duration_hours' => 40.0,
                'learning_outcomes' => [
                    'Structure complex enterprise domain logic in modular Laravel architecture',
                    'Scale asynchronous queues and Redis job workers efficiently',
                    'Prevent advanced vulnerabilities like IDOR, CSRF, and race conditions',
                    'Implement high-throughput REST & GraphQL APIs with automated contract testing',
                ],
                'prerequisites' => [
                    'Intermediate knowledge of PHP and basic Laravel MVC concepts',
                    'Familiarity with SQL database schemas and Eloquent ORM',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays(8),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $this->attachInstructor($course2, $instructor);

        // Course 2: Delivery Options
        $c2Online = CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course2->id, 'delivery_mode' => 'online', 'title' => 'Online Self-Paced Developer Pass'],
            [
                'description' => 'Complete digital access to high-definition video walkthroughs, repository codebases, and digital certificate.',
                'price' => 11500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'access_period_days' => 365,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => false,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ]
        );

        $c2Physical = CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course2->id, 'delivery_mode' => 'physical', 'title' => 'In-Person Intensive Weekend Batch (Kandy Hub)'],
            [
                'description' => 'Interactive in-person code labs at our Kandy Tech Hub with live pair programming, architectural design reviews, and certificate.',
                'price' => 29500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'max_capacity' => 20,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => true,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ]
        );

        // Course 2: Physical Batch & Week Schedule
        $batch2 = CourseBatch::updateOrCreate(
            ['batch_code' => 'BAT-LAR-2026-KDY1'],
            [
                'course_delivery_option_id' => $c2Physical->id,
                'location_id' => $kandyLoc->id,
                'batch_name' => 'Kandy Intensive Weekend Intake 01',
                'max_seats' => 20,
                'enrolled_seats' => 8,
                'registration_start_date' => $now->copy()->subDays(12)->toDateString(),
                'registration_end_date' => $now->copy()->addDays(18)->toDateString(),
                'start_date' => $now->copy()->addDays(24)->toDateString(),
                'end_date' => $now->copy()->addDays(90)->toDateString(),
                'status' => 'open_for_enrollment',
            ]
        );

        $this->seedBatchSchedules($batch2, [
            ['day_of_week' => 'Sunday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'room_number' => 'Hall 201', 'notes' => 'Intensive architecture lecture, live refactoring, and code review'],
        ]);

        $this->seedBatchSessions($batch2, $instructor, [
            ['offset_days' => 24, 'day' => 'Sunday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'topic' => 'Enterprise Architecture & Domain Service Container', 'room' => 'Hall 201'],
            ['offset_days' => 31, 'day' => 'Sunday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'topic' => 'Pipeline Pattern, Middleware & Complex Filtering', 'room' => 'Hall 201'],
            ['offset_days' => 38, 'day' => 'Sunday', 'start_time' => '10:00:00', 'end_time' => '15:00:00', 'topic' => 'Redis Queues, Horizons & Resilient Job Workers', 'room' => 'Hall 201'],
        ]);

        $this->seedLiveClasses($course2, $instructor, $batch2, [
            ['title' => 'Live Enterprise Code Review & Refactoring Session', 'start_offset_days' => 30, 'duration_hours' => 2],
        ]);

        $this->seedCurriculum($course2, [
            [
                'title' => 'Module 1: Enterprise Laravel Design Patterns & Architecture',
                'description' => 'Deconstructing monolithic controllers into modular actions, data transfer objects, and domain services.',
                'lessons' => [
                    ['title' => '1.1 Domain-Driven Design in Modern Laravel Applications', 'duration' => 28, 'preview' => true],
                    ['title' => '1.2 Advanced Service Container Binding & Contextual Dependency Injection', 'duration' => 35, 'preview' => false],
                    ['title' => '1.3 Implementing Pipeline Design Patterns for Complex Workflows', 'duration' => 40, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 2: High-Concurrency Caching & Distributed Job Queues',
                'description' => 'Managing async tasks, idempotency keys, rate limits, and Redis clustering.',
                'lessons' => [
                    ['title' => '2.1 Redis Queue Architecture & Job Failure Recovery', 'duration' => 32, 'preview' => true],
                    ['title' => '2.2 Real-Time Event Broadcasting with WebSockets', 'duration' => 45, 'preview' => false],
                    ['title' => '2.3 Database Locking: Optimistic vs Pessimistic Locks', 'duration' => 36, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 3: Enterprise Security & API Hardening',
                'description' => 'Sanctum tokens, scoped abilities, rate limiting, and zero-trust API validation.',
                'lessons' => [
                    ['title' => '3.1 Bulletproof Authorization Policies & IDOR Elimination', 'duration' => 30, 'preview' => false],
                    ['title' => '3.2 High-Throughput RESTful API Pagination & Fractal Transformers', 'duration' => 34, 'preview' => false],
                    ['title' => '3.3 Automated Contract & Integration Testing with PHPUnit', 'duration' => 42, 'preview' => false],
                ],
            ],
        ]);

        // -------------------------------------------------------------------------
        // Course 3: (Physical + Online) Modern UI/UX Design Systems & Mobile-First Prototyping
        // -------------------------------------------------------------------------
        $course3 = Course::updateOrCreate(
            ['slug' => 'modern-ui-ux-design-systems-prototyping'],
            [
                'category_id' => $designCategory->id,
                'title' => 'Modern UI/UX Design Systems & Mobile-First Prototyping',
                'subtitle' => 'Figma component architectures, accessible design tokens, micro-interactions, and usability testing.',
                'description' => 'A hands-on professional course for digital product designers and frontend engineers. Master atomic design systems, WCAG AAA color accessibility, responsive auto-layout, interactive state machines, and design handoff.',
                'difficulty_level' => 'all_levels',
                'language' => 'English',
                'duration_hours' => 32.0,
                'learning_outcomes' => [
                    'Establish scalable design token systems in Figma and Tailwind CSS',
                    'Design accessible interfaces compliant with WCAG 2.2 accessibility standards',
                    'Create complex, interactive micro-prototypes with component variants and variables',
                    'Conduct usability testing sessions and translate insights into polished UI',
                ],
                'prerequisites' => [
                    'Curiosity about product design and digital interfaces (no prior coding required)',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays(6),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $this->attachInstructor($course3, $instructor);

        // Course 3: Delivery Options
        $c3Online = CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course3->id, 'delivery_mode' => 'online', 'title' => 'Online Designer Pass'],
            [
                'description' => 'Immediate on-demand access to all Figma UI kits, video lessons, component libraries, and certificate.',
                'price' => 8500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'access_period_days' => 365,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => false,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ]
        );

        $c3Physical = CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course3->id, 'delivery_mode' => 'physical', 'title' => 'In-Person Studio Lab (Galle Fort Campus)'],
            [
                'description' => 'In-person weekend studio workshops inside historic Galle Fort with real design critique sessions and portfolio reviews.',
                'price' => 24000.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'max_capacity' => 18,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => true,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ]
        );

        // Course 3: Physical Batch & Week Schedule
        $batch3 = CourseBatch::updateOrCreate(
            ['batch_code' => 'BAT-DES-2026-GLE1'],
            [
                'course_delivery_option_id' => $c3Physical->id,
                'location_id' => $galleLoc->id,
                'batch_name' => 'Southern Creative Studio Cohort 01',
                'max_seats' => 18,
                'enrolled_seats' => 4,
                'registration_start_date' => $now->copy()->subDays(10)->toDateString(),
                'registration_end_date' => $now->copy()->addDays(20)->toDateString(),
                'start_date' => $now->copy()->addDays(28)->toDateString(),
                'end_date' => $now->copy()->addDays(80)->toDateString(),
                'status' => 'open_for_enrollment',
            ]
        );

        $this->seedBatchSchedules($batch3, [
            ['day_of_week' => 'Sunday', 'start_time' => '09:30:00', 'end_time' => '13:30:00', 'room_number' => 'Studio 1', 'notes' => 'Design critique, Figma Auto-Layout lab, and interactive prototyping'],
        ]);

        $this->seedBatchSessions($batch3, $instructor, [
            ['offset_days' => 28, 'day' => 'Sunday', 'start_time' => '09:30:00', 'end_time' => '13:30:00', 'topic' => 'Design Tokens, Typographic Scales & Spatial Grid', 'room' => 'Studio 1'],
            ['offset_days' => 35, 'day' => 'Sunday', 'start_time' => '09:30:00', 'end_time' => '13:30:00', 'topic' => 'Auto-Layout Components & Responsive Frame Modeling', 'room' => 'Studio 1'],
            ['offset_days' => 42, 'day' => 'Sunday', 'start_time' => '09:30:00', 'end_time' => '13:30:00', 'topic' => 'Interactive Micro-Animations & User Usability Labs', 'room' => 'Studio 1'],
        ]);

        $this->seedLiveClasses($course3, $instructor, $batch3, [
            ['title' => 'Live Design Critique & Portfolio Evaluation', 'start_offset_days' => 32, 'duration_hours' => 2],
        ]);

        $this->seedCurriculum($course3, [
            [
                'title' => 'Module 1: Design Foundations, Spatial Systems & Accessibility',
                'description' => 'Typography scales, WCAG color harmony, 8pt spatial grid, and visual hierarchy.',
                'lessons' => [
                    ['title' => '1.1 The 8-Point Grid System & Visual Hierarchy Principles', 'duration' => 22, 'preview' => true],
                    ['title' => '1.2 Accessible Contrast & Color Theory for Modern Displays', 'duration' => 28, 'preview' => false],
                    ['title' => '1.3 Information Architecture & User Journey Wireframing', 'duration' => 35, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 2: Scalable Figma Variables, Auto-Layout & Component Sets',
                'description' => 'Building enterprise design libraries with tokens, modes, component variants, and nested slots.',
                'lessons' => [
                    ['title' => '2.1 Mastering Advanced Figma Auto-Layout & Constraints', 'duration' => 36, 'preview' => true],
                    ['title' => '2.2 Color and Number Variables for Dark/Light Mode Theming', 'duration' => 40, 'preview' => false],
                    ['title' => '2.3 Building Resilient Component Sets with Interactive Variants', 'duration' => 44, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 3: Interactive Micro-Animations & Usability Benchmarking',
                'description' => 'Smart animate transitions, mobile gestures, and developer handoff documentation.',
                'lessons' => [
                    ['title' => '3.1 Smart Animate & State Transitions in Figma', 'duration' => 30, 'preview' => false],
                    ['title' => '3.2 Usability Testing Protocols & Unmoderated Test Scripts', 'duration' => 38, 'preview' => false],
                    ['title' => '3.3 Engineering Handoff: Inspect Panel, Tokens & CSS Export', 'duration' => 26, 'preview' => false],
                ],
            ],
        ]);

        // -------------------------------------------------------------------------
        // Course 4: (Online Self-Paced) Next.js 15 & React Server Components Deep Dive
        // -------------------------------------------------------------------------
        $course4 = Course::updateOrCreate(
            ['slug' => 'nextjs-15-react-server-components-mastery'],
            [
                'category_id' => $webCategory->id,
                'title' => 'Next.js 15 & React Server Components Deep Dive',
                'subtitle' => 'Master App Router, Server Actions, streaming SSR, and edge deployment.',
                'description' => 'Explore the modern frontier of web engineering with Next.js 15. Learn how React Server Components eliminate bundle size bloat, execute secure Server Actions, stream partial pages via Suspense, and deploy to worldwide edge networks.',
                'difficulty_level' => 'intermediate',
                'language' => 'English',
                'duration_hours' => 28.0,
                'learning_outcomes' => [
                    'Architect production applications with Next.js App Router',
                    'Harness React Server Components (RSC) and streaming SSR',
                    'Write secure Server Actions with Zod validation',
                    'Deploy globally distributed edge middleware with zero cold starts',
                ],
                'prerequisites' => [
                    'Proficiency in JavaScript (ES6+) and modern React fundamentals',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays(4),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $this->attachInstructor($course4, $instructor);

        CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course4->id, 'delivery_mode' => 'online', 'title' => 'Online Self-Paced Pro Pass'],
            [
                'description' => 'Complete digital access to all lessons, interactive coding sandboxes, full GitHub repository, and digital certificate.',
                'price' => 9500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'access_period_days' => 365,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => false,
                'includes_live_classes' => false,
                'includes_certificate' => true,
            ]
        );

        $this->seedCurriculum($course4, [
            [
                'title' => 'Module 1: React Server Components & Streaming Architecture',
                'description' => 'Understanding the mental model shift from client components to server-first rendering.',
                'lessons' => [
                    ['title' => '1.1 The React Server Components Revolution Explained', 'duration' => 24, 'preview' => true],
                    ['title' => '1.2 Layouts, Template Files & Parallel Route Slots', 'duration' => 32, 'preview' => false],
                    ['title' => '1.3 Instant Loading States & Streaming with React Suspense', 'duration' => 36, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 2: Server Actions, Form Validations & Mutations',
                'description' => 'Handling user input without API boilerplate using direct server actions and optimistic updates.',
                'lessons' => [
                    ['title' => '2.1 Writing Type-Safe Server Actions with Zod', 'duration' => 30, 'preview' => true],
                    ['title' => '2.2 Optimistic UI Updates with useOptimistic Hook', 'duration' => 28, 'preview' => false],
                    ['title' => '2.3 Revalidating Cached Tags & Path Invalidation Strategies', 'duration' => 34, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 3: Authentication, Middleware & Production Edge Deployment',
                'description' => 'Edge middleware, session cookies, route protection, and edge cloud CDN deployment.',
                'lessons' => [
                    ['title' => '3.1 JWT & Session Cookie Protection in Edge Middleware', 'duration' => 35, 'preview' => false],
                    ['title' => '3.2 Static Page Generation vs Dynamic Rendering at Runtime', 'duration' => 40, 'preview' => false],
                    ['title' => '3.3 Deploying to Vercel and AWS Amplify with Edge CDN', 'duration' => 25, 'preview' => false],
                ],
            ],
        ]);

        // -------------------------------------------------------------------------
        // Course 5: (Online + Live Classes) Database Optimization & Distributed MySQL with Aiven
        // -------------------------------------------------------------------------
        $course5 = Course::updateOrCreate(
            ['slug' => 'database-optimization-distributed-mysql-aiven'],
            [
                'category_id' => $cloudCategory->id,
                'title' => 'Database Optimization & Distributed MySQL with Aiven',
                'subtitle' => 'Query profiling, composite indexing, connection pooling, and SSL replication across clusters.',
                'description' => 'Learn how to take MySQL from slow queries and deadlock timeouts to sub-millisecond query responses. Covers EXPLAIN query plans, B-Tree vs Hash index structures, connection pooling over SSL/TLS, and read-replica distribution on Aiven Cloud.',
                'difficulty_level' => 'advanced',
                'language' => 'English',
                'duration_hours' => 36.0,
                'learning_outcomes' => [
                    'Analyze slow query logs and optimize complex queries using EXPLAIN ANALYZE',
                    'Design multi-column composite indexes that eliminate filesort operations',
                    'Configure TLS 1.3 encrypted cloud MySQL instances with high-availability replication',
                    'Diagnose and eliminate database deadlocks in high-concurrency environments',
                ],
                'prerequisites' => [
                    'Comfortable writing standard SQL queries and understanding foreign keys',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays(3),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $this->attachInstructor($course5, $instructor);

        CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course5->id, 'delivery_mode' => 'online', 'title' => 'Online Masterclass with Live Database Labs'],
            [
                'description' => 'Full access to cloud database sandboxes, video recordings, live query tuning workshops, and certificate.',
                'price' => 12000.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'access_period_days' => 365,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => false,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ]
        );

        $this->seedLiveClasses($course5, $instructor, null, [
            ['title' => 'Live Database Tuning & EXPLAIN Workshop', 'start_offset_days' => 12, 'duration_hours' => 2],
            ['title' => 'Live Cloud MySQL Replication & Failover Simulation', 'start_offset_days' => 26, 'duration_hours' => 2],
        ]);

        $this->seedCurriculum($course5, [
            [
                'title' => 'Module 1: Query Execution Plans, EXPLAIN & Indexing Secrets',
                'description' => 'Understanding how MySQL Optimizer chooses index scans and how to design covering indexes.',
                'lessons' => [
                    ['title' => '1.1 Deep Dive into EXPLAIN and EXPLAIN ANALYZE Output', 'duration' => 32, 'preview' => true],
                    ['title' => '1.2 B-Tree Index Anatomy & Leftmost Prefix Rule', 'duration' => 38, 'preview' => false],
                    ['title' => '1.3 Eliminating Using filesort and Using temporary', 'duration' => 40, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 2: Transaction Isolation & High-Concurrency Locking',
                'description' => 'ACID guarantees, row-level locks, gap locks, and deadlock troubleshooting.',
                'lessons' => [
                    ['title' => '2.1 MySQL InnoDB Storage Engine Architecture & Buffer Pools', 'duration' => 30, 'preview' => true],
                    ['title' => '2.2 Transaction Isolation Levels and Read Consistency', 'duration' => 35, 'preview' => false],
                    ['title' => '2.3 Detecting and Resolving InnoDB Deadlocks in Production', 'duration' => 45, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 3: Cloud Replication & High-Availability Clustering with Aiven',
                'description' => 'Setting up primary-replica clusters, zero-downtime failovers, and SSL certificate verification.',
                'lessons' => [
                    ['title' => '3.1 Provisioning Managed Cloud MySQL on Aiven over TLS 1.3', 'duration' => 28, 'preview' => false],
                    ['title' => '3.2 Read-Replica Load Distribution with Laravel Database Sharding', 'duration' => 36, 'preview' => false],
                    ['title' => '3.3 Disaster Recovery: Point-in-Time Backups & Restorations', 'duration' => 30, 'preview' => false],
                ],
            ],
        ]);

        // -------------------------------------------------------------------------
        // Course 6: (Online Hands-on) Cybersecurity & Secure Web Application Defenses
        // -------------------------------------------------------------------------
        $course6 = Course::updateOrCreate(
            ['slug' => 'cybersecurity-secure-web-application-defenses'],
            [
                'category_id' => $seCategory->id,
                'title' => 'Cybersecurity & Secure Web Application Defenses',
                'subtitle' => 'OWASP Top 10 mitigation, CSRF, XSS, SQLi defenses, and penetration testing fundamentals.',
                'description' => 'Learn how black-hat attackers exploit modern web applications and how software architects build impenetrable systems. Covers hands-on penetration testing, ethical hacking fundamentals, cryptographically secure tokens, and CI/CD vulnerability scanning.',
                'difficulty_level' => 'intermediate',
                'language' => 'English',
                'duration_hours' => 35.0,
                'learning_outcomes' => [
                    'Identify and neutralize the OWASP Top 10 web vulnerabilities',
                    'Implement strict Content Security Policy (CSP) and CORS headers',
                    'Conduct vulnerability assessments with Burp Suite and OWASP ZAP',
                    'Protect user credentials with modern Argon2id and PBKDF2 hashing standards',
                ],
                'prerequisites' => [
                    'Understanding of HTTP request/response lifecycles and cookies',
                ],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays(2),
                'created_by_user_id' => $instructor->id,
            ]
        );

        $this->attachInstructor($course6, $instructor);

        CourseDeliveryOption::updateOrCreate(
            ['course_id' => $course6->id, 'delivery_mode' => 'online', 'title' => 'Online Cyber Defense Lab Access'],
            [
                'description' => 'Full access to simulated capture-the-flag (CTF) lab environments, video lectures, defense checklists, and digital credential.',
                'price' => 13500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
                'access_period_days' => 365,
                'includes_recorded_lessons' => true,
                'includes_physical_sessions' => false,
                'includes_live_classes' => true,
                'includes_certificate' => true,
            ]
        );

        $this->seedLiveClasses($course6, $instructor, null, [
            ['title' => 'Live Penetration Testing & Ethical Hacking Demonstration', 'start_offset_days' => 18, 'duration_hours' => 2],
        ]);

        $this->seedCurriculum($course6, [
            [
                'title' => 'Module 1: Web Threat Modeling & The OWASP Top 10 In-Depth',
                'description' => 'Hands-on exploration of common web attack vectors and defensive countermeasures.',
                'lessons' => [
                    ['title' => '1.1 Foundations of Secure Web Architecture & Threat Modeling', 'duration' => 26, 'preview' => true],
                    ['title' => '1.2 Preventing SQL Injection & Blind SQLi Attacks', 'duration' => 36, 'preview' => false],
                    ['title' => '1.3 Neutralizing Cross-Site Scripting (XSS) & HTML Sanitization', 'duration' => 34, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 2: Cryptographic Storage & Session Token Hardening',
                'description' => 'Modern password hashing algorithms, JWT security pitfalls, and CSRF token defense.',
                'lessons' => [
                    ['title' => '2.1 Argon2id vs Bcrypt: Modern Cryptographic Hashing Standards', 'duration' => 28, 'preview' => true],
                    ['title' => '2.2 CSRF Protection, SameSite Cookies & Origin Verification', 'duration' => 30, 'preview' => false],
                    ['title' => '2.3 Insecure Direct Object References (IDOR) & Scoped Authorizations', 'duration' => 38, 'preview' => false],
                ],
            ],
            [
                'title' => 'Module 3: Defensive Headers & Automated Security Pipelines',
                'description' => 'Strict transport security, Content Security Policies, and automated vulnerability scanning.',
                'lessons' => [
                    ['title' => '3.1 Crafting Robust Content Security Policy (CSP) Headers', 'duration' => 32, 'preview' => false],
                    ['title' => '3.2 Automated Dependency Vulnerability Scanning in GitHub Actions', 'duration' => 28, 'preview' => false],
                    ['title' => '3.3 Penetration Testing Workflows with Burp Suite Community', 'duration' => 45, 'preview' => false],
                ],
            ],
        ]);
    }

    private function attachInstructor(Course $course, User $instructor): void
    {
        $course->instructors()->syncWithoutDetaching([
            $instructor->id => [
                'is_primary' => true,
                'can_manage_content' => true,
                'can_grade' => true,
            ],
        ]);
    }

    private function seedBatchSchedules(CourseBatch $batch, array $schedules): void
    {
        foreach ($schedules as $sched) {
            BatchSchedule::updateOrCreate(
                [
                    'batch_id' => $batch->id,
                    'day_of_week' => $sched['day_of_week'],
                ],
                [
                    'start_time' => $sched['start_time'],
                    'end_time' => $sched['end_time'],
                    'room_number' => $sched['room_number'],
                    'notes' => $sched['notes'],
                ]
            );
        }
    }

    private function seedBatchSessions(CourseBatch $batch, User $instructor, array $sessions): void
    {
        $now = Carbon::now('Asia/Colombo');
        foreach ($sessions as $sess) {
            $date = $now->copy()->addDays($sess['offset_days'])->toDateString();
            BatchSession::updateOrCreate(
                [
                    'batch_id' => $batch->id,
                    'session_date' => $date,
                ],
                [
                    'start_time' => $sess['start_time'],
                    'end_time' => $sess['end_time'],
                    'topic' => $sess['topic'],
                    'room_number' => $sess['room'],
                    'status' => 'scheduled',
                    'conducted_by_user_id' => $instructor->id,
                ]
            );
        }
    }

    private function seedLiveClasses(Course $course, User $instructor, ?CourseBatch $batch, array $classes): void
    {
        $now = Carbon::now('Asia/Colombo');
        foreach ($classes as $idx => $cls) {
            $startTime = $now->copy()->addDays($cls['start_offset_days'])->setTime(19, 0, 0);
            $endTime = $startTime->copy()->addHours($cls['duration_hours'] ?? 2);

            LiveClass::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => $cls['title'],
                ],
                [
                    'instructor_id' => $instructor->id,
                    'batch_id' => $batch?->id,
                    'description' => 'Interactive live online masterclass hosted by '.$instructor->name.'. Covers live hands-on demonstrations and direct student Q&A.',
                    'scheduled_start_time' => $startTime,
                    'scheduled_end_time' => $endTime,
                    'provider' => 'jitsi',
                    'meeting_identifier' => 'learnbuddy-live-'.Str::slug($cls['title']),
                    'meeting_password' => 'lbLive2026!',
                    'status' => LiveClass::STATUS_SCHEDULED,
                    'access_permission' => LiveClass::ACCESS_ENROLLED_STUDENTS,
                ]
            );
        }
    }

    private function seedCurriculum(Course $course, array $sections): void
    {
        foreach ($sections as $secIndex => $secData) {
            $section = CourseSection::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => $secData['title'],
                ],
                [
                    'description' => $secData['description'] ?? null,
                    'sort_order' => $secIndex + 1,
                    'is_published' => true,
                ]
            );

            foreach ($secData['lessons'] as $lesIndex => $lesData) {
                $slug = Str::slug($lesData['title']);
                Lesson::updateOrCreate(
                    [
                        'section_id' => $section->id,
                        'slug' => $slug,
                    ],
                    [
                        'title' => $lesData['title'],
                        'type' => 'video',
                        'duration_minutes' => $lesData['duration'],
                        'sort_order' => $lesIndex + 1,
                        'is_preview' => $lesData['preview'],
                        'is_published' => true,
                        'content' => 'Welcome to '.$lesData['title'].'. In this session, our instructor breaks down core architecture, demonstrates practical code implementation, and walks through production scenarios with real-world troubleshooting.',
                    ]
                );
            }
        }
    }
}
