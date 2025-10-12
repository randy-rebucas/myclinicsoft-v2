<?php

use App\Models\Queue;
use App\Models\Encounter;
use App\Models\Prescription;
use App\Models\Medication;
use App\Services\PrescriptionPdfGenerator;
use function Livewire\Volt\{state, mount};

state([
    'queue' => null,
    'patient' => null,
    'encounter' => null,
    'prescriptions' => null,
    'newPrescription' => [
        'medication_name' => '',
        'dosage' => '',
        'frequency' => '',
        'quantity' => 1,
        'refills' => 0,
        'instructions' => '',
        'start_date' => '',
        'end_date' => '',
    ],
    'showAddForm' => false,
    'commonMedications' => [],
]);

mount(function (Queue $queue) {
    $this->queue = $queue;
    $this->patient = $queue->patient;
    
    // Get today's encounter
    $this->encounter = $this->patient->encounters()
        ->whereDate('encounter_date', now()->toDateString())
        ->where('doctor_id', auth()->user()->doctor->id)
        ->first();
    
    // Load existing prescriptions for this encounter
    if ($this->encounter) {
        $this->prescriptions = $this->encounter->prescriptions;
    } else {
        $this->prescriptions = collect();
    }
    
    // Load common medications (using a different approach since Medication model doesn't have name field)
    // For now, we'll use an empty collection - you may want to create a separate medications catalog table
    $this->commonMedications = collect();
    
    // Set default dates
    $this->newPrescription['start_date'] = now()->format('Y-m-d');
    $this->newPrescription['end_date'] = now()->addDays(30)->format('Y-m-d');
});

$addPrescription = function () {
    $this->validate([
        'newPrescription.medication_name' => 'required|string|max:255',
        'newPrescription.dosage' => 'required|string|max:100',
        'newPrescription.frequency' => 'required|string|max:100',
        'newPrescription.quantity' => 'required|integer|min:1',
        'newPrescription.instructions' => 'nullable|string',
        'newPrescription.start_date' => 'required|date',
        'newPrescription.end_date' => 'required|date|after:newPrescription.start_date',
    ]);
    
    if (!$this->encounter) {
        session()->flash('error', 'No encounter found for this patient.');
        return;
    }
    
    $prescription = Prescription::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => auth()->user()->doctor->id,
        'encounter_id' => $this->encounter->id,
        'medication_name' => $this->newPrescription['medication_name'],
        'dosage' => $this->newPrescription['dosage'],
        'frequency' => $this->newPrescription['frequency'],
        'quantity' => $this->newPrescription['quantity'],
        'refills' => $this->newPrescription['refills'],
        'instructions' => $this->newPrescription['instructions'],
        'start_date' => $this->newPrescription['start_date'],
        'end_date' => $this->newPrescription['end_date'],
        'status' => 'active',
    ]);
    
    if (!$this->prescriptions) {
        $this->prescriptions = collect();
    }
    $this->prescriptions->push($prescription);
    
    // Reset form
    $this->newPrescription = [
        'medication_name' => '',
        'dosage' => '',
        'frequency' => '',
        'quantity' => 1,
        'refills' => 0,
        'instructions' => '',
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
    ];
    
    $this->showAddForm = false;
    session()->flash('success', 'Prescription added successfully.');
};

$removePrescription = function ($prescriptionId) {
    $prescription = Prescription::find($prescriptionId);
    if ($prescription) {
        $prescription->delete();
        if ($this->prescriptions) {
            $this->prescriptions = $this->prescriptions->filter(fn($p) => $p->id !== $prescriptionId);
        }
        session()->flash('success', 'Prescription removed successfully.');
    }
};

$generatePdf = function () {
    if (!$this->prescriptions || $this->prescriptions->isEmpty()) {
        session()->flash('error', 'No prescriptions to generate PDF for.');
        return;
    }
    
    // For now, generate PDF for the first prescription
    // In a real implementation, you might want to generate a combined PDF
    $prescription = $this->prescriptions->first();
    $pdfGenerator = new PrescriptionPdfGenerator($prescription);
    
    $filename = $pdfGenerator->generateAndSave();
    
    session()->flash('success', 'Prescription PDF generated successfully.');
    
    // In a real implementation, you would redirect to download the PDF
    // return response()->download(storage_path('app/public/prescriptions/' . $filename));
};

$selectMedication = function ($medicationId) {
    // Since we're using an empty collection for now, this function won't do anything
    // In a real implementation, you would have a medications catalog table
    // $medication = $this->commonMedications->find($medicationId);
    // if ($medication) {
    //     $this->newPrescription['medication_name'] = $medication->name;
    //     $this->newPrescription['dosage'] = $medication->default_dosage ?? '';
    //     $this->newPrescription['frequency'] = $medication->default_frequency ?? '';
    // }
};

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Prescription Generator</h2>
                <p class="text-gray-600">Create and manage prescriptions for {{ $patient->full_name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="$set('showAddForm', true)" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Add Prescription
                </button>
                @if($prescriptions && $prescriptions->count() > 0)
                    <button wire:click="generatePdf" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Generate PDF
                    </button>
                @endif
                <button wire:click="$dispatch('start-followup')" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    Next: Follow-up
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

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    @svg('heroicon-o-exclamation-triangle', 'w-5 h-5 text-red-400')
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Prescription Form -->
    @if($showAddForm)
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Add New Prescription</h3>
                <button wire:click="$set('showAddForm', false)" 
                        class="text-gray-400 hover:text-gray-600">
                    @svg('heroicon-o-x-mark', 'w-6 h-6')
                </button>
            </div>
            
            <form wire:submit.prevent="addPrescription" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medication Name</label>
                            <input type="text" wire:model="newPrescription.medication_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter medication name">
                            @error('newPrescription.medication_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dosage</label>
                            <input type="text" wire:model="newPrescription.dosage" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 500mg, 10ml">
                            @error('newPrescription.dosage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                            <select wire:model="newPrescription.frequency" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select frequency</option>
                                <option value="Once daily">Once daily</option>
                                <option value="Twice daily">Twice daily</option>
                                <option value="Three times daily">Three times daily</option>
                                <option value="Four times daily">Four times daily</option>
                                <option value="Every 6 hours">Every 6 hours</option>
                                <option value="Every 8 hours">Every 8 hours</option>
                                <option value="Every 12 hours">Every 12 hours</option>
                                <option value="As needed">As needed</option>
                            </select>
                            @error('newPrescription.frequency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input type="number" wire:model="newPrescription.quantity" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 30"
                                   min="1">
                            @error('newPrescription.quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Refills</label>
                            <input type="number" wire:model="newPrescription.refills" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   min="0" max="5">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" wire:model="newPrescription.start_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('newPrescription.start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" wire:model="newPrescription.end_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('newPrescription.end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                            <textarea wire:model="newPrescription.instructions" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      rows="3"
                                      placeholder="Enter special instructions for the patient..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showAddForm', false)" 
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Add Prescription
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Common Medications -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Common Medications</h3>
        @if($commonMedications->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($commonMedications as $medication)
                    <button wire:click="selectMedication({{ $medication->id }})" 
                            class="p-3 text-left border border-gray-200 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="font-medium text-gray-900">{{ $medication->name }}</div>
                        @if($medication->default_dosage)
                            <div class="text-sm text-gray-600">{{ $medication->default_dosage }}</div>
                        @endif
                    </button>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>No common medications available.</p>
                <p class="text-sm">Please enter medication details manually in the form above.</p>
            </div>
        @endif
    </div>

    <!-- Current Prescriptions -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Current Prescriptions</h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($prescriptions ?? [] as $prescription)
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $prescription->medication_name }}</h4>
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    {{ $prescription->status }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Dosage:</span>
                                    <span class="font-medium">{{ $prescription->dosage }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Frequency:</span>
                                    <span class="font-medium">{{ $prescription->frequency }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Quantity:</span>
                                    <span class="font-medium">{{ $prescription->quantity }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Refills:</span>
                                    <span class="font-medium">{{ $prescription->refills }}</span>
                                </div>
                            </div>
                            @if($prescription->instructions)
                                <div class="mt-2">
                                    <span class="text-gray-600">Instructions:</span>
                                    <span class="text-sm">{{ $prescription->instructions }}</span>
                                </div>
                            @endif
                            <div class="mt-2 text-xs text-gray-500">
                                Duration: {{ $prescription->start_date->format('M d, Y') }} - {{ $prescription->end_date->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="ml-4">
                            <button wire:click="removePrescription({{ $prescription->id }})" 
                                    class="text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500">
                                @svg('heroicon-o-trash', 'w-5 h-5')
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        @svg('heroicon-o-document-text', 'w-8 h-8 text-gray-400')
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No prescriptions yet</h3>
                    <p class="text-gray-500">Add prescriptions for this patient to get started.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
