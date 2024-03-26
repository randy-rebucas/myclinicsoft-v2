<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Spatie\Permission\Models\Role;

class RoleForm extends Form
{
    #[Validate('required|string|max:255')]
    public $name;

    public $permissions = [];

    public $assigned_permissions = [];

    public function store(?Role $role)
    {
        $this->validate();

        if ($role) {
            $this->update($role);
        } else {
            $this->create();
        }

        $this->reset('name');
    }

    public function setRole(?Role $role = null)
    {
        $this->name = $role->name;
        $this->assigned_permissions = $role->permissions->pluck('name');
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
