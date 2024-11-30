<?php

use App\Models\Department;
use App\Models\Queue;
use App\Models\Ads;
use function Livewire\Volt\{state, layout, form, mount, computed, with};

state([
    'departments' => [],
    'filter' => 'all',
    'department_id' => 1,
    'ads' => [],
]);

layout('layouts.que');

mount(function () {
    $this->departments = Department::all();
    $this->ads = Ads::active()->latest()->take(2)->get();
});

$queues = computed(function () {
    return Queue::with(['patient', 'department'])
        ->when($this->filter !== 'all', fn($query) => $query->where('status', $this->filter))
        ->when($this->department_id, fn($query) => $query->where('department_id', $this->department_id))
        ->orderBy('priority', 'desc')
        ->orderBy('created_at', 'asc')
        ->paginate(10);
});

?>

<div class=" mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <!-- Main Content Column (spans 3 columns) -->
        <div class="lg:col-span-2">
            <!-- Department Tabs -->
            {{-- <div class="mb-8 hidden md:block">
                <div class="sm:hidden">
                    <select wire:model.live="department_id"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        @foreach ($this->departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hidden sm:block">
                    <nav class="flex space-x-4" aria-label="Departments">
                        @foreach ($this->departments as $department)
                            <button wire:click="$set('department_id', {{ $department->id }})"
                                class="px-4 py-2 rounded-md {{ $department->id === $this->department_id
                                    ? 'bg-indigo-600 text-white'
                                    : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                {{ $department->name }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div> --}}

            <!-- Now Serving Section -->
            <div class="mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-6">
                            Now Serving
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-8">
                            @foreach ($this->departments as $department)
                                @php
                                    $currentQueue = $this->queues
                                        ->where('department_id', $department->id)
                                        ->where('status', 'in_progress')
                                        ->first();
                                @endphp
                                <div class="relative group">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg opacity-75 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div
                                        class="relative bg-white dark:bg-gray-900 rounded-lg p-6 border-2 border-indigo-500">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                            {{ $department->name }}</h3>
                                        <div
                                            class="text-5xl font-bold text-center text-indigo-600 dark:text-indigo-400">
                                            {{ $currentQueue ? $currentQueue->queue_number : '-' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Waiting List Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Next in Line</h2>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        @foreach ($this->departments as $department)
                            <div class="space-y-3">
                                <h3
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    {{ $department->name }}
                                </h3>
                                <div class="space-y-2">
                                    @foreach ($this->queues->where('department_id', $department->id)->where('status', 'waiting')->take(3) as $queue)
                                        <div
                                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-900 rounded-lg p-3 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors">
                                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $queue->queue_number }}
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
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Ads Column -->
        <div class="lg:col-span-3">
            <div class="sticky top-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Advertisement</h2>
                        <div x-data="{
                            currentSlide: 0,
                            totalSlides: {{ count($this->ads) }},
                            autoplayInterval: null,

                            startAutoplay() {
                                if (this.totalSlides <= 1) return;
                                this.autoplayInterval = setInterval(() => this.next(), 10000);
                            },

                            stopAutoplay() {
                                if (this.autoplayInterval) clearInterval(this.autoplayInterval);
                            },

                            next() {
                                this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                            },

                            prev() {
                                this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                            },

                            goto(index) {
                                this.currentSlide = index;
                            }
                        }"
                        x-init="startAutoplay()"
                        @mouseenter="stopAutoplay()"
                        @mouseleave="startAutoplay()"
                        class="relative">
                            <!-- Carousel Container -->
                            <div class="overflow-hidden rounded-xl">
                                <div class="flex transition-transform duration-500 ease-in-out"
                                     :style="`transform: translateX(-${currentSlide * 100}%)`">
                                    @forelse ($this->ads as $ad)
                                        <div class="w-full flex-shrink-0 relative">
                                            @if ($ad->image_url)
                                                <img src="{{ Storage::url($ad->image_url) }}"
                                                     alt="{{ $ad->title }}"
                                                     class="w-full h-[400px] object-cover">
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                                <h3 class="text-2xl font-bold text-white mb-3">{{ $ad->title }}</h3>
                                                <p class="text-gray-200 text-lg line-clamp-2">{{ $ad->description }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="w-full">
                                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 h-[400px] flex items-center justify-center">
                                                <span class="text-gray-500 dark:text-gray-400">No advertisements available</span>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Navigation Arrows -->
                            @if (count($this->ads) > 1)
                                <button @click="prev()"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button @click="next()"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <!-- Navigation Dots -->
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-3">
                                    @foreach ($this->ads as $index => $ad)
                                        <button @click="goto({{ $index }})"
                                                class="w-3 h-3 rounded-full transition-all duration-200 transform"
                                                :class="currentSlide === {{ $index }}
                                                    ? 'bg-white scale-110'
                                                    : 'bg-white/50 hover:bg-white/75'">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
