<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-3 space-y-6">
        <x-card>
            <x-card.header>Personal Information</x-card.header>
            <livewire:patient.profile :patient="$patient" />
        </x-card>
        
        <x-card>
            <x-card.header>Contact Details</x-card.header>
            <livewire:patient.address :patient="$patient" />
        </x-card>
    </div>

    <!-- Right Column -->
    <div class="lg:col-span-9 space-y-6">
        <x-card>
            <x-card.header>
                <div class="flex items-center justify-between">
                    <h3>Recent Activity</h3>
                    <x-badge color="green">Last Updated Today</x-badge>
                </div>
            </x-card.header>
            <livewire:patient.encounter.index :patient="$patient" limit="5" />
        </x-card>

        <x-card>
            <x-card.header>Medical History</x-card.header>
            <livewire:patient.record.index :patient="$patient" />
        </x-card>
    </div>
</div> 