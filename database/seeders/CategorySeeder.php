<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Software Engineering',
                'slug' => 'software-engineering',
                'icon' => 'fas fa-laptop-code',
                'description' => 'Full-stack software architecture, Laravel, backend systems, and enterprise design patterns.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'icon' => 'fas fa-code',
                'description' => 'Modern frontend and responsive web development using Tailwind CSS, JavaScript, and Vue.',
                'sort_order' => 2,
            ],
            [
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
                'icon' => 'fas fa-palette',
                'description' => 'User research, wireframing, high-fidelity prototypes, and mobile-first interface design.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Cloud & DevOps',
                'slug' => 'cloud-devops',
                'icon' => 'fas fa-cloud',
                'description' => 'Database management, SSL encryption, cloud deployments, and CI/CD automation.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Business & Technology',
                'slug' => 'business-technology',
                'icon' => 'fas fa-chart-line',
                'description' => 'Digital commerce in Sri Lanka, project leadership, and payment integrations.',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
