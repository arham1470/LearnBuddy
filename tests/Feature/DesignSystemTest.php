<?php

namespace Tests\Feature;

use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    public function test_homepage_shell_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('LearnBuddy');
        $response->assertSee('Skip to main content');
        $response->assertSee('Explore Our Courses');
        $response->assertSee('Top Categories');
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('Colombo Central Campus');
    }

    public function test_design_system_page_renders_successfully(): void
    {
        $response = $this->get('/design-system');

        $response->assertStatus(200);
        $response->assertSee('Design System');
        $response->assertSee('Component Library');
        $response->assertSee('#FF5F00');
        $response->assertSee('#FFF0E6');
        $response->assertSee('#14B8A6');
        $response->assertSee('Reusable Button Components');
        $response->assertSee('Alpine.js Interactive Modals');
    }

    public function test_mobile_navigation_markup_is_present(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Verify Alpine.js state attributes for mobile navigation
        $response->assertSee('mobileNavOpen', false);
        $response->assertSee('aria-label="Toggle mobile menu"', false);
    }
}
