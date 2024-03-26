<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Livewire\Forms\RoleForm;
use function Livewire\Volt\{state, layout, form, mount, computed};

state('role');

layout('layouts.app');

form(RoleForm::class);

mount(function () {
    $this->form->permissions = Permission::all()->pluck('name');
});

$roles = computed(function () {
    return Role::all();
});

$delete = function (Role $role) {
    $role->delete();

    $this->dispatch('refresh');
};

$edit = function ($id) {
    $this->role = Role::findOrFail($id);

    $this->form->setRole($this->role);

    $this->dispatch('open-modal', 'form-role');
};

$create = function () {
    $this->role = null;

    $this->dispatch('open-modal', 'form-role');
};

$save = function () {
    $this->form->store($this->role);

    $this->dispatch('close-modal', 'form-role');

    $this->dispatch('refresh');
};

?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="min-w-full">
                    <div class="space-y-6">
                        <div class="flex justify-between">
                            <x-text-input wire:model.live="search" class="py-2" type="search" :placeholder="__('Search Roles...')" />
                            <x-secondary-button wire:click="create">
                                {{ __('Create New') }}
                            </x-secondary-button>
                        </div>

                        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
                            <x-table for="med-representative">
                                <x-table.thead>
                                    <x-table.row class="dark:bg-gray-900 dark:text-gray-100">
                                        <x-table.thead-cell :title="__('Role Name')" class="text-left" />
                                        <x-table.thead-cell :title="__('Permissions')" class="text-center" />
                                        <x-table.thead-cell title="" class="text-right" />
                                    </x-table.row>
                                </x-table.thead>
                                <x-table.tbody class="dark:border-gray-500">
                                    @forelse ($this->roles as $role)
                                        <x-table.row class="bg-white dark:bg-gray-700 dark:text-white"
                                            wire:loading.class="opacity-50">
                                            <x-table.tbody-cell :item="$role->name" class="uppercase" />
                                            <x-table.tbody-cell :item="$role->permissions->count()" class="text-center" />
                                            <x-table.tbody-cell :item="$role->id" class="text-right" :action="true">
                                                <button type="button" class="btn btn-info m-1 font-medium underline"
                                                    wire:click="edit({{ $role->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="w-5 h-5">
                                                        <path
                                                            d="m2.695 14.762-1.262 3.155a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.886L17.5 5.501a2.121 2.121 0 0 0-3-3L3.58 13.419a4 4 0 0 0-.885 1.343Z" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-info m-1 text-red-600 font-medium underline"
                                                    wire:click="delete({{ $role }})"
                                                    wire:confirm="Are you sure you want to delete this patient?">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="w-5 h-5">
                                                        <path fill-rule="evenodd"
                                                            d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </x-table.tbody-cell>
                                        </x-table.row>
                                    @empty
                                        <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                                            <x-table.tbody-cell colspan="6" :item="__('No role found!!')" />
                                        </x-table.row>
                                    @endforelse
                                </x-table.tbody>
                            </x-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="form-role" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Role Form') }}
            </h2>

            <div class="w-full">
                <x-input-label for="name" :value="__('Role Name')" />
                <x-text-input wire:model="form.name" id="name" class="block mt-1 w-full" type="text"
                    name="name" autofocus />
                <x-input-error :messages="$errors->get('form.name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <ul>
                    @foreach ($this->form->permissions as $permission)
                        <li>
                            <label class="checkbox-wrap">
                                <input type="checkbox" wire:model="form.assigned_permissions"
                                    value="{{ $permission }}">
                                {{ $permission }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</section>
