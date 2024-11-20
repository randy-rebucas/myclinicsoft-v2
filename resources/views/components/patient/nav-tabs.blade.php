@props(['activeTab', 'onTabChange'])

<div class="border-t">
    <nav class="-mb-px flex space-x-8">
        @foreach(['overview' => 'Overview', 
                 'encounters' => 'Encounters', 
                 'records' => 'Medical Records', 
                 'prescriptions' => 'Prescriptions'] as $tab => $label)
            <button 
                wire:click="setActiveTab('{{ $tab }}')"
                class="{{ $activeTab === $tab 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
                {{ $label }}
            </button>
        @endforeach
    </nav>
</div> 