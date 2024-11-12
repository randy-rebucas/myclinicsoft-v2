<div>
    <x-modal wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Add to Queue
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                    <select wire:model="department_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                    <select wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button wire:click="$set('showModal', false)">
                    Cancel
                </x-secondary-button>
                <x-primary-button wire:click="addToQueue">
                    Add to Queue
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</div>
