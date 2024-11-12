<div class="space-y-6">
    <!-- Now Serving -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-6">Now Serving</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($departments as $department)
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $department->name }}</h3>
                    @php
                        $currentQueue = $queues->where('department_id', $department->id)
                            ->where('status', 'in_progress')
                            ->first();
                    @endphp
                    <div class="text-3xl font-bold text-center text-indigo-600 dark:text-indigo-400">
                        {{ $currentQueue ? $currentQueue->queue_number : '-' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Next in Line -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Next in Line</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($departments as $department)
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $department->name }}</h3>
                    <div class="space-y-1">
                        @foreach($queues->where('department_id', $department->id)->where('status', 'waiting')->take(3) as $queue)
                            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-900 rounded px-3 py-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $queue->queue_number }}</span>
                                @if($queue->priority !== 'normal')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $queue->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-red-500 text-white' }}">
                                        {{ ucfirst($queue->priority) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
