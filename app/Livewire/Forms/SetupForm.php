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

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'menu.staff']);
        
        $role = Role::create(['name' => 'super-admin'])->givePermissionTo(Permission::all());

        $user->assignRole($role);

        Auth::login($user);

        return redirect()->to('/');
    }

}
