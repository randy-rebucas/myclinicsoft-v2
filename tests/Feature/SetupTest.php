<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_setup_screen_can_be_rendered(): void
    {
        $response = $this->get('/setup');

        $response
            ->assertOk()
            ->assertSeeVolt('setup.index');
    }

    public function test_first_user_gets_admin_role(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $component = Volt::test('setup.index')
            ->set('form.name', 'Admin User')
            ->set('form.email', 'admin@example.com')
            ->set('form.password', 'password')
            ->set('form.password_confirmation', 'password');

        $component->call('save');

        $component->assertRedirect('/');

        // Check that user was created
        $this->assertDatabaseCount('users', 1);

        $user = User::first();
        $this->assertEquals('Admin User', $user->name);
        $this->assertEquals('admin@example.com', $user->email);

        // Check that user has admin role
        $this->assertTrue($user->hasRole('admin'));

        // Check that roles and permissions were created
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('roles', ['name' => 'doctor']);
        $this->assertDatabaseHas('roles', ['name' => 'patient']);

        // Check that permissions were created
        $this->assertDatabaseHas('permissions', ['name' => 'view dashboard']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage users']);
    }

    public function test_setup_creates_all_necessary_permissions(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $component = Volt::test('setup.index')
            ->set('form.name', 'Admin User')
            ->set('form.email', 'admin@example.com')
            ->set('form.password', 'password')
            ->set('form.password_confirmation', 'password');

        $component->call('save');

        // Check that all expected permissions exist
        $expectedPermissions = [
            'view dashboard',
            'view users',
            'create users',
            'update users',
            'delete users',
            'manage users',
            'view roles',
            'create roles',
            'update roles',
            'delete roles',
            'manage roles',
            'view permissions',
            'assign permissions',
            'view patients',
            'create patients',
            'update patients',
            'delete patients',
            'view patient records',
            'create patient records',
            'update patient records',
            'view doctors',
            'create doctors',
            'update doctors',
            'delete doctors',
            'view queue',
            'manage queue',
            'update queue status',
            'view encounters',
            'create encounters',
            'update encounters',
            'delete encounters',
            'view medical conditions',
            'create medical conditions',
            'update medical conditions',
            'delete medical conditions',
            'view medications',
            'create medications',
            'update medications',
            'delete medications',
            'view prescriptions',
            'create prescriptions',
            'update prescriptions',
            'print prescriptions',
            'view medical records',
            'manage appointments',
            'view clinics',
            'create clinics',
            'update clinics',
            'delete clinics',
            'manage clinic settings',
            'view settings',
            'update settings',
            'manage system settings',
            'view activities',
            'view audit logs',
            'dump database',
            'manage system',
            'access nova admin',
        ];

        foreach ($expectedPermissions as $permission) {
            $this->assertDatabaseHas('permissions', ['name' => $permission]);
        }
    }

    public function test_admin_role_has_all_permissions(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $component = Volt::test('setup.index')
            ->set('form.name', 'Admin User')
            ->set('form.email', 'admin@example.com')
            ->set('form.password', 'password')
            ->set('form.password_confirmation', 'password');

        $component->call('save');

        $adminRole = Role::where('name', 'admin')->first();
        $allPermissions = Permission::all();

        $this->assertTrue($adminRole->permissions->count() === $allPermissions->count());
    }

    public function test_user_is_authenticated_after_setup(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $component = Volt::test('setup.index')
            ->set('form.name', 'Admin User')
            ->set('form.email', 'admin@example.com')
            ->set('form.password', 'password')
            ->set('form.password_confirmation', 'password');

        $component->call('save');

        $this->assertAuthenticated();
    }

    public function test_setup_creates_default_clinic_settings(): void
    {
        // Ensure no users exist
        $this->assertDatabaseCount('users', 0);

        $component = Volt::test('setup.index')
            ->set('form.name', 'Admin User')
            ->set('form.email', 'admin@example.com')
            ->set('form.password', 'password')
            ->set('form.password_confirmation', 'password');

        $component->call('save');

        // Check that default clinic settings were created
        $expectedSettings = [
            'clinic_name',
            'clinic_address',
            'clinic_city',
            'clinic_state',
            'clinic_zip',
            'clinic_country',
            'clinic_phone',
            'clinic_emergency_phone',
            'clinic_email',
            'clinic_website',
            'clinic_hours_weekdays',
            'clinic_hours_saturday',
            'clinic_hours_sunday',
            'clinic_description',
            'clinic_tagline',
        ];

        foreach ($expectedSettings as $setting) {
            $this->assertDatabaseHas('settings', ['key' => $setting]);
        }

        // Check specific default values
        $this->assertDatabaseHas('settings', [
            'key' => 'clinic_name',
            'value' => 'My Clinic'
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'clinic_tagline',
            'value' => 'Your Health, Our Priority'
        ]);
    }
}
