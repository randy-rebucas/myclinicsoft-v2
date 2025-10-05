<?php

use App\Models\ClinicDoctor;
use App\Models\Queue;
use App\Models\Clinic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\{state, layout, form, mount, computed, with};


state([
    'clinics' => [],
    'filter' => 'all',
    'clinic_id' => null,
    'ads' => [],
    'listeners' => ['echo:queues,QueueUpdated' => 'refreshQueues'],
]);

layout('layouts.queue');

mount(function () {
    $query = ClinicDoctor::with('clinic')
        ->where('is_primary', true);
    if (Auth::user()->hasRole('receptionist')) {
        $doctorId = Auth::user()?->receptionist?->doctor?->id;
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }
    } else {
        $doctorId = Auth::user()?->doctor?->id;
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }
    }
    $clinicDoctor = $query->first();
    $this->clinic_id = $clinicDoctor ? $clinicDoctor->clinic->id : null;

    // Ads functionality removed - table was dropped in cleanup migration
    $this->ads = [];
});

$queues = computed(function () {
    return Queue::with(['patient', 'clinic'])
        ->when($this->filter !== 'all', fn($query) => $query->where('status', $this->filter))
        ->when($this->clinic_id, fn($query) => $query->where('clinic_id', $this->clinic_id))
        ->whereDate('created_at', Carbon::today())
        ->orderBy('priority', 'desc')
        ->orderBy('created_at', 'asc')
        ->get();
});

$refreshQueues = function () {
    $this->dispatch('$refresh');
};

?>

<div class="h-screen flex">
    <!-- Main Content Column (Left) -->
    <div class="w-1/3 overflow-y-auto p-8">
        <!-- Now Serving Section -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-6">
                        Now Serving
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-8">

                        @php
                            $currentQueue = $this->queues->where('status', 'in_progress')->first();
                        @endphp
                        <div class="relative group">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg opacity-75 group-hover:opacity-100 transition-opacity">
                            </div>
                            <div class="relative bg-white dark:bg-gray-900 rounded-lg p-6 border-2 border-indigo-500">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ Clinic::where('id', $this->clinic_id)->first()?->name ?? 'Unknown Clinic' }}</h3>
                                <div class="text-5xl font-bold text-center text-indigo-600 dark:text-indigo-400">
                                    {{ $currentQueue ? $currentQueue->queue_number : '-' }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable Waiting List Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 sticky top-0 bg-white dark:bg-gray-800">
                    Next in Line</h2>
                <div class="space-y-6">

                    <div class="space-y-3">
                        <h3
                            class="text-sm font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider sticky top-14 bg-white dark:bg-gray-800">
                            {{ Clinic::where('id', $this->clinic_id)->first()?->name ?? 'Unknown Clinic' }}
                        </h3>
                        <div class="space-y-2">
                            @foreach ($this->queues->where('clinic_id', $this->clinic_id)->where('status', 'waiting') as $queue)
                                <div
                                    class="flex items-center justify-between bg-gray-50 dark:bg-gray-900 rounded-lg p-3 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $queue->queue_number }} - <span class="font-normal">{{ $queue->patient?->first_name ?? 'Unknown' }} {{ $queue->patient?->last_name ?? 'Patient' }}</span>
                                    </span>
                                    @if ($queue->priority !== 'normal')
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-full {{ $queue->priority === 'urgent'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                : 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' }}">
                                            {{ ucfirst($queue->priority) }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Ads Column (Right) -->
    <div class="w-2/3 h-screen">
        <!-- Youtube Container -->
        <div class="h-full" x-data="{
            videos: @json($this->ads),
            currentVideo: 0,
            player: null,

            initYouTube() {
                if (typeof YT === 'undefined') {
                    // Load YouTube IFrame API if not already loaded
                    const tag = document.createElement('script');
                    tag.src = 'https://www.youtube.com/iframe_api';
                    const firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                    window.onYouTubeIframeAPIReady = () => this.createPlayer();
                } else {
                    this.createPlayer();
                }
            },

            createPlayer() {
                this.player = new YT.Player('youtube-player', {
                    height: '100%',
                    width: '100%',
                    videoId: this.videos[this.currentVideo],
                    playerVars: {
                        autoplay: 1,
                        controls: 0,
                        rel: 0,
                        showinfo: 0,
                        mute: 1
                    },
                    events: {
                        onStateChange: (event) => {
                            // When video ends, play next video
                            if (event.data === YT.PlayerState.ENDED) {
                                this.currentVideo = (this.currentVideo + 1) % this.videos.length;
                                this.player.loadVideoById(this.videos[this.currentVideo]);
                            }
                        }
                    }
                });
            }
        }" x-init="initYouTube()">
            <div id="youtube-player" class="w-full h-full"></div>
        </div>
    </div>
</div>
