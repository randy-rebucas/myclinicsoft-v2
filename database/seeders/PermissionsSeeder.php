<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create comprehensive permissions
        $permissions = [
            // Dashboard and general access
            'view dashboard',
            
            // User management
            'view users',
            'create users',
            'update users',
            'delete users',
            'manage users',
            
            // Role and permission management
            'view roles',
            'create roles',
            'update roles',
            'delete roles',
            'manage roles',
            'view permissions',
            'assign permissions',
            
            // Patient management
            'view patients',
            'create patients',
            'update patients',
            'delete patients',
            'view patient records',
            'create patient records',
            'update patient records',
            
            // Doctor management
            'view doctors',
            'create doctors',
            'update doctors',
            'delete doctors',
            
            
            
            // Queue management
            'view queue',
            'manage queue',
            'update queue status',
            
            // Medical records
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
            'view medical records', // Used in dashboard
            
            // Appointment management
            'manage appointments', // Used in dashboard
            
            // Clinic management
            'view clinics',
            'create clinics',
            'update clinics',
            'delete clinics',
            'manage clinic settings',
            
            // Settings management
            'view settings',
            'update settings',
            'manage system settings',
            
            // Activity and logging
            'view activities',
            'view audit logs',
            
            // System administration
            'dump database',
            'manage system', // Used in dashboard
            'access nova admin',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createRolesAndAssignPermissions();
        
        // Create demo admin user
        $this->createDemoAdmin();
    }

    /**
     * Create roles and assign appropriate permissions
     */
    private function createRolesAndAssignPermissions(): void
    {
        // Patient role - minimal access
        $patientRole = Role::create(['name' => 'patient']);
        $patientRole->givePermissionTo([
            'view dashboard',
            'view patient records', // Only their own records
        ]);

        // Medical Representative role - limited access
        $medrepRole = Role::create(['name' => 'medrep']);
        $medrepRole->givePermissionTo([
            'view dashboard',
            'view doctors',
        ]);


        // Doctor role - comprehensive medical access
        $doctorRole = Role::create(['name' => 'doctor']);
        $doctorRole->givePermissionTo([
            'view dashboard',
            'view patients',
            'create patients',
            'update patients',
            'view patient records',
            'create patient records',
            'update patient records',
            'view encounters',
            'create encounters',
            'update encounters',
            'view medical conditions',
            'create medical conditions',
            'update medical conditions',
            'view medications',
            'create medications',
            'update medications',
            'view prescriptions',
            'create prescriptions',
            'update prescriptions',
            'print prescriptions',
            'view medical records',
            'manage appointments',
            'view queue',
            'manage queue',
            'update queue status',
            'view doctors',
            'view receptionists',
            'view activities',
        ]);

        // Admin role - full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }

    /**
     * Create demo admin user
     */
    private function createDemoAdmin(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');
    }
}
