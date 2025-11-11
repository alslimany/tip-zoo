<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/dashboard');
    }
    
    public function test_admin_dashboard_is_accessible(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Total Animals');
    }
}
