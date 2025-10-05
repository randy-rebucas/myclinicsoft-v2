<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckInitialUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirects_to_setup_when_no_users_exist(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('setup'));
    }

    public function test_allows_access_when_users_exist(): void
    {
        // Create a user
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertOk();
    }

    public function test_setup_page_accessible_when_no_users_exist(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $response = $this->get('/setup');

        $response->assertOk();
    }

    public function test_setup_page_accessible_when_users_exist(): void
    {
        // Create a user
        User::factory()->create();

        $response = $this->get('/setup');

        $response->assertOk();
    }

    public function test_public_routes_accessible_when_no_users_exist(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_auth_routes_redirect_when_no_users_exist(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        // Create a user for authentication
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertRedirect(route('setup'));
    }

    public function test_login_route_redirects_to_setup_when_no_users_exist(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $response = $this->get('/login');

        $response->assertRedirect(route('setup'));
    }

    public function test_register_route_redirects_to_setup_when_no_users_exist(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $response = $this->get('/register');

        $response->assertRedirect(route('setup'));
    }

    public function test_api_auth_routes_return_json_when_no_users_exist(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(503)
                ->assertJson([
                    'message' => 'System setup required. Please create an admin user first.',
                    'setup_required' => true
                ])
                ->assertJsonStructure(['setup_url']);
    }
}
