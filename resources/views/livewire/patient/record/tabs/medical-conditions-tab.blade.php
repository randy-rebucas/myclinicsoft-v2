<div class="bg-white shadow-lg rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Medical Conditions</h3>
        <div class="flex space-x-2">
            <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medical Condition'"
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Add New
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($medicalConditions as $condition)
            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="text-base font-semibold text-gray-900">{{ $condition->name }}</h4>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="font-medium text-gray-500 w-20">Diagnosed:</span>
                                <span class="text-gray-900">{{ $condition->diagnosis_date }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="font-medium text-gray-500 w-20">Status:</span>
                                <span class="text-gray-900">{{ $condition->status }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <button @click="showModal = true; modalType = 'edit'; modalTitle = 'Edit Medical Condition'"
                            class="text-gray-400 hover:text-gray-500">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-8">
                <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Medical Conditions</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new medical condition record.</p>
                <div class="mt-6">
                    <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medical Condition'"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        Add Medical Condition
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>
