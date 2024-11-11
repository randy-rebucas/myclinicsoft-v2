<?php
use App\Models\Patient;
use function Livewire\Volt\{layout, state, mount, title, computed};

state(['patientId'])->url();

dd($this->patientId);
$patient = computed(function() {
    return Patient::findOrFail($this->patientId);
});
state([
    'activeTab' => 'overview',
    'loading' => false
]);

title(function() use ($patient) {
    
    return 'Patient Details - ';
});

$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

$createEncounter = function() {
    $this->redirect("/patients/{$this->patientId}/encounters/create", navigate: true);
};

$editPatient = function() {
    $this->redirect("/patients/{$this->patientId}/edit", navigate: true);
};

$goback = function () {
    $this->redirect('/patients', navigate: true);
};

layout('layouts.app');
?>

<div class="min-h-screen bg-gray-50">
    <!-- Header Component -->
    <x-patient.header 
        :patient="$patient->value()" 
        :on-back="$goback"
        :on-edit="$editPatient"
        :on-new-encounter="$createEncounter"
    />

    <!-- Navigation Tabs -->
    <x-patient.nav-tabs 
        :active-tab="$activeTab"
        :on-tab-change="$setActiveTab"
    />

    <!-- Main Content -->
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            @switch($activeTab)
                @case('overview')
                    <livewire:patient.tabs.overview :patient="$patient->value()" />
                    @break
                @case('encounters')
                    <livewire:patient.tabs.encounters :patient="$patient->value()" />
                    @break
                @case('records')
                    <livewire:patient.tabs.records :patient="$patient->value()" />
                    @break
                @case('prescriptions')
                    <livewire:patient.tabs.prescriptions :patient="$patient->value()" />
                    @break
            @endswitch
        </div>
    </div>
</div>
