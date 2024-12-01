<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-4">
        <div class="space-y-4 p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <x-input-label for="update_password_current_password" :value="__('Current Password')"
                    class="text-sm font-medium text-gray-700 md:pt-2" />
                <div class="md:col-span-3">
                    <x-text-input wire:model.live="current_password" id="update_password_current_password"
                        name="current_password" type="password" class="block w-full" autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <x-input-label for="update_password_password" :value="__('New Password')"
                    class="text-sm font-medium text-gray-700 md:pt-2" />
                <div class="md:col-span-3">
                    <x-text-input wire:model.live="password" id="update_password_password" name="password" type="password"
                        class="block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')"
                    class="text-sm font-medium text-gray-700 md:pt-2" />
                <div class="md:col-span-3">
                    <x-text-input wire:model.live="password_confirmation" id="update_password_password_confirmation"
                        name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
        </div>
        <div class="px-4 py-3 bg-gray-50 text-right rounded-b-lg">
            <x-primary-button wire:loading.attr="disabled" wire:target="updatePassword">
                <span wire:loading.remove wire:target="updatePassword">{{ __('Save') }}</span>
                <span wire:loading wire:target="updatePassword" class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    {{ __('Saving...') }}
                </span>
            </x-primary-button>
        </div>
    </form>
</section>
