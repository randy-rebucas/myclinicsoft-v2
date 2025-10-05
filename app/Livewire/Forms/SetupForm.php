<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Livewire\Attributes\Validate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Form;

class SetupForm extends Form
{
    #[Validate]
    public string $email = '';

    public string $password = '';

    public string $name = '';

    public string $password_confirmation = '';

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'. User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function store()
    {
        $validated = $this->validate();

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        // Check if this is the first user (superadmin setup)
        $userCount = User::count();
        if ($userCount === 1) {
            // Set up roles and permissions for the first user
            $this->setupInitialRolesAndPermissions();
            
            // Set up default clinic settings
            $this->setupDefaultClinicSettings();
            
            // Assign admin role to the first user
            $user->assignRole('admin');
        }

        // Log user registration activity
        $user->recordActivity('created', 'User account was created');

        Auth::login($user);

        // Log successful login activity
        $user->recordActivity('login', 'User logged in');

        return redirect()->to('/');
    }

    /**
     * Set up initial roles and permissions for the system
     */
    private function setupInitialRolesAndPermissions(): void
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
            'view medical records',
            
            // Appointment management
            'manage appointments',
            
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
            'manage system',
            'access nova admin',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createRolesAndAssignPermissions();
    }

    /**
     * Create roles and assign appropriate permissions
     */
    private function createRolesAndAssignPermissions(): void
    {
        // Patient role - minimal access
        $patientRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'patient']);
        $patientRole->syncPermissions([
            'view dashboard',
            'view patient records',
        ]);

        // Doctor role - comprehensive medical access
        $doctorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->syncPermissions([
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
            'view activities',
        ]);

        // Admin role - full access
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(\Spatie\Permission\Models\Permission::all());
    }

    /**
     * Set up default clinic settings
     */
    private function setupDefaultClinicSettings(): void
    {
        $defaultSettings = [
            'clinic_name' => 'My Clinic',
            'clinic_address' => '123 Main Street',
            'clinic_city' => 'Your City',
            'clinic_state' => 'Your State',
            'clinic_zip' => '12345',
            'clinic_country' => 'Your Country',
            'clinic_phone' => '(555) 123-4567',
            'clinic_emergency_phone' => '(555) 999-8888',
            'clinic_email' => 'info@myclinic.com',
            'clinic_website' => 'https://myclinic.com',
            'clinic_hours_weekdays' => 'Monday - Friday: 8:00 AM - 6:00 PM',
            'clinic_hours_saturday' => 'Saturday: 9:00 AM - 1:00 PM',
            'clinic_hours_sunday' => 'Sunday: Closed',
            'clinic_description' => 'Providing comprehensive healthcare services in a warm, professional environment.',
            'clinic_tagline' => 'Your Health, Our Priority',
        ];

        foreach ($defaultSettings as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

}
