<?php

namespace Tests\Feature;

use Tests\TestCase;

class DatabaseHealthCheckTest extends TestCase
{
    public function test_database_health_endpoint_returns_json_status(): void
    {
        $response = $this->get('/up/database');

        $this->assertContains($response->status(), [200, 503]);
        $response->assertHeader('Content-Type', 'application/json');

        $data = $response->json();
        $this->assertArrayHasKey('status', $data);

        // Verify credentials/secrets are NEVER exposed in response payload
        $content = $response->getContent();
        $this->assertStringNotContainsString('mysql-3e258d69', $content);
        $this->assertStringNotContainsString('avnadmin', $content);
        $this->assertStringNotContainsString('password', strtolower($content));
    }

    public function test_artisan_db_health_check_command_does_not_leak_secrets(): void
    {
        $this->artisan('db:health-check')
            ->doesntExpectOutputToContain('avnadmin')
            ->doesntExpectOutputToContain('mysql-3e258d69');
    }
}
