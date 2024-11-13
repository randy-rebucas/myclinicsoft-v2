<form wire:submit="save" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Blood Pressure -->
        <div>
            <x-input-label for="systolic" :value="__('Blood Pressure (mmHg)')" />
            <div class="flex gap-2">
                <div>
                    <x-text-input wire:model="form.systolic" type="number" placeholder="Systolic" />
                    @error('form.systolic') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-text-input wire:model="form.diastolic" type="number" placeholder="Diastolic" />
                    @error('form.diastolic') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Pulse Rate -->
        <div>
            <x-input-label for="pulse_rate" :value="__('Pulse Rate (bpm)')" />
            <x-text-input wire:model="form.pulse_rate" type="number" class="w-full" />
            @error('form.pulse_rate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Temperature -->
        <div>
            <x-input-label for="temperature" :value="__('Temperature (°C)')" />
            <x-text-input wire:model="form.temperature" type="number" step="0.1" class="w-full" />
            @error('form.temperature') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Respiratory Rate -->
        <div>
            <x-input-label for="respiratory_rate" :value="__('Respiratory Rate (bpm)')" />
            <x-text-input wire:model="form.respiratory_rate" type="number" class="w-full" />
            @error('form.respiratory_rate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Oxygen Saturation -->
        <div>
            <x-input-label for="oxygen_saturation" :value="__('Oxygen Saturation (%)')" />
            <x-text-input wire:model="form.oxygen_saturation" type="number" class="w-full" />
            @error('form.oxygen_saturation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Blood Sugar -->
        <div>
            <x-input-label for="blood_sugar" :value="__('Blood Sugar (mg/dL)')" />
            <x-text-input wire:model="form.blood_sugar" type="number" class="w-full" />
            @error('form.blood_sugar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="flex justify-end gap-x-2">
        <x-secondary-button type="button" wire:click="$dispatch('close-modal')">
            {{ __('Cancel') }}
        </x-secondary-button>
        <x-primary-button type="submit">
            {{ __('Save') }}
        </x-primary-button>
    </div>
</form>
