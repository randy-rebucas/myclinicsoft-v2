<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleForm extends Form
{
    public ?string $name = '';
    public array $permissions = [];
    public array $assigned_permissions = [];

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'assigned_permissions' => ['array'],
        ];
    }

    public function store(?Role $role)
    {
        $this->validate();

        $role = $role ?? new Role();
        $role->name = $this->name;
        $role->save();

        $role->syncPermissions($this->assigned_permissions);

        // Log role creation/update activity through the current user
        $user = Auth::user();
        if ($role->wasRecentlyCreated) {
            $user->recordActivity('created', 'Role was created: ' . $role->name);
        } else {
            $user->recordActivity('updated', 'Role was updated: ' . $role->name);
        }

        $this->reset();
        // if ($role) {
        //     $this->update($role);
        // } else {
        //     $this->create();
        // }

        // $this->reset('name');
    }

    public function setRole(Role $role)
    {
        $this->name = $role->name;
        $this->assigned_permissions = $role->permissions->pluck('name')->toArray();
    }

    public function update(?Role $role)
    {
        $role->update([
            'name' => $this->name
        ]);

        $role->syncPermissions($this->assigned_permissions);
    }

    public function create()
    {
        Role::create([
            'name' => $this->name,
        ])->givePermissionTo($this->assigned_permissions);
    }
}
