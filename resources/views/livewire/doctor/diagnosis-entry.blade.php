<?php

use App\Models\Queue;
use App\Models\Encounter;
use App\Models\MedicalCondition;
use function Livewire\Volt\{state, mount};

state([
    'queue' => null,
    'patient' => null,
    'encounter' => null,
    'diagnosis' => '',
    'treatmentPlan' => '',
    'notes' => '',
    'medicalConditions' => [],
    'selectedConditions' => [],
]);

mount(function (Queue $queue) {
    $this->queue = $queue;
    $this->patient = $queue->patient;
    
    // Get today's encounter
    $this->encounter = $this->patient->encounters()
        ->whereDate('encounter_date', now()->toDateString())
        ->where('doctor_id', auth()->user()->doctor->id)
        ->first();
    
    if ($this->encounter) {
        $this->diagnosis = $this->encounter->diagnosis ?? '';
        $this->treatmentPlan = $this->encounter->treatment_plan ?? '';
        $this->notes = $this->encounter->notes ?? '';
    }
    
    // Load medical conditions for reference
    $this->medicalConditions = MedicalCondition::orderBy('condition_name')->get();
});

$saveDiagnosis = function () {
    if (!$this->encounter) {
        return;
    }
    
    $this->encounter->update([
        'diagnosis' => $this->diagnosis,
        'treatment_plan' => $this->treatmentPlan,
        'notes' => $this->notes,
    ]);
    
    session()->flash('success', 'Diagnosis and treatment plan saved successfully.');
};

$addCondition = function ($conditionId) {
    $condition = MedicalCondition::find($conditionId);
        if ($condition && !in_array($conditionId, $this->selectedConditions)) {
            $this->selectedConditions[] = $conditionId;
            if ($this->diagnosis) {
                $this->diagnosis .= ', ' . $condition->condition_name;
            } else {
                $this->diagnosis = $condition->condition_name;
            }
        }
};

$removeCondition = function ($conditionId) {
    $condition = MedicalCondition::find($conditionId);
    if ($condition) {
        $this->selectedConditions = array_filter($this->selectedConditions, fn($id) => $id !== $conditionId);
        $this->diagnosis = str_replace($condition->condition_name, '', $this->diagnosis);
        $this->diagnosis = preg_replace('/,\s*,/', ',', $this->diagnosis);
        $this->diagnosis = trim($this->diagnosis, ', ');
    }
};

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Diagnosis Entry</h2>
                <p class="text-gray-600">Record diagnosis details and medical notes for {{ $patient->full_name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="saveDiagnosis" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Save Diagnosis
                </button>
                <button wire:click="$dispatch('start-prescription')" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Next: Prescription
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    @svg('heroicon-o-check-circle', 'w-5 h-5 text-green-400')
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Diagnosis Forms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Primary Diagnosis -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Primary Diagnosis</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                        <textarea wire:model.live="diagnosis" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  rows="4"
                                  placeholder="Enter primary diagnosis..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                        <textarea wire:model.live="treatmentPlan" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  rows="6"
                                  placeholder="Enter treatment plan and recommendations..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea wire:model.live="notes" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  rows="4"
                                  placeholder="Add any additional clinical notes..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Medical Conditions Reference -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Common Medical Conditions</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($medicalConditions as $condition)
                        <button wire:click="addCondition({{ $condition->id }})" 
                                @class([
                                    'px-3 py-2 text-sm rounded-md border transition-colors',
                                    'bg-blue-100 border-blue-300 text-blue-800' => in_array($condition->id, $selectedConditions),
                                    'bg-gray-50 border-gray-300 text-gray-700 hover:bg-gray-100' => !in_array($condition->id, $selectedConditions)
                                ])>
                            {{ $condition->condition_name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column - Patient Info & Quick Actions -->
        <div class="space-y-6">
            <!-- Patient Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Summary</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">{{ $patient->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Age</p>
                        <p class="font-medium">{{ $patient->date_of_birth ? $patient->date_of_birth->age . ' years' : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Gender</p>
                        <p class="font-medium">{{ $patient->gender ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-medium">{{ $patient->phone }}</p>
                    </div>
                </div>
            </div>

            <!-- Selected Conditions -->
            @if(count($selectedConditions) > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Selected Conditions</h3>
                    <div class="space-y-2">
                        @foreach($selectedConditions as $conditionId)
                            @php $condition = $medicalConditions->find($conditionId); @endphp
                            @if($condition)
                                <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                                    <span class="text-sm font-medium text-blue-800">{{ $condition->condition_name }}</span>
                                    <button wire:click="removeCondition({{ $condition->id }})" 
                                            class="text-blue-600 hover:text-blue-800">
                                        @svg('heroicon-o-x-mark', 'w-4 h-4')
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button wire:click="saveDiagnosis" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Save Diagnosis
                    </button>
                    <button wire:click="$dispatch('start-prescription')" 
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Generate Prescription
                    </button>
                    <button wire:click="$dispatch('start-followup')" 
                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Schedule Follow-up
                    </button>
                    <button wire:click="$dispatch('back-to-queue')" 
                            class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Back to Queue
                    </button>
                </div>
            </div>

            <!-- Diagnosis Guidelines -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Diagnosis Guidelines</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• Be specific and accurate in diagnosis</li>
                    <li>• Include relevant symptoms and findings</li>
                    <li>• Consider differential diagnoses</li>
                    <li>• Document severity and stage if applicable</li>
                    <li>• Include any relevant ICD-10 codes</li>
                </ul>
            </div>
        </div>
    </div>
</div>
