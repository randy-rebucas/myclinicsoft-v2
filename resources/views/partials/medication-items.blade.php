<div class="medication-list my-5">
    @foreach ($items as $item)
        <div class="medication-item flex justify-between items-center p-4 mb-2 bg-gray-100 rounded-lg shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-transform">
            <span class="medication-name font-semibold text-gray-800 flex-2">{{ $item['fields']['medication_name'] }}</span>
            <span class="medication-dosage text-gray-600 flex-1 text-center">{{ $item['fields']['dosage'] }}</span>
            <span class="medication-frequency text-gray-600 flex-1 text-right">{{ $item['fields']['frequency'] }}</span>
        </div>
    @endforeach
</div>

<style>
@media (max-width: 600px) {
    .medication-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 2;
    }

    .medication-dosage,
    .medication-frequency {
        text-align: left;
    }
}
</style>
