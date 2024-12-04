<?php

use App\Livewire\Forms\SetupForm;
use function Livewire\Volt\{state, layout, form};

form(SetupForm::class);

layout('layouts.app');

$save = function () {
    $this->form->store();
};

?>

<section>
    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Subscribe to continue</h3>

                    <form wire:submit="save" class="space-y-6">
                        <!-- Plan Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select Plan</label>
                            <select wire:model="form.plan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Choose a plan</option>
                                <option value="monthly">Monthly ($10/month)</option>
                                <option value="yearly">Yearly ($100/year)</option>
                            </select>
                            @error('form.plan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Payment Information -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Card Number</label>
                                <input type="text" wire:model="form.card_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="4242 4242 4242 4242">
                                @error('form.card_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Expiration Date</label>
                                    <input type="text" wire:model="form.expiration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="MM/YY">
                                    @error('form.expiration') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CVC</label>
                                    <input type="text" wire:model="form.cvc" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="123">
                                    @error('form.cvc') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Subscribe Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
