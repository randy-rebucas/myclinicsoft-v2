<div x-data="{
    notifications: [],
    init() {
        // Function to initialize Echo listener
        const initEcho = () => {
            if (typeof window.Echo !== 'undefined') {
                window.Echo.channel('queues')
                    .listen('QueueUpdated', (e) => {
                        const id = Date.now();
                        this.notifications.push({
                            id: id,
                            message: e.message,
                            status: e.status
                        });
                        setTimeout(() => {
                            this.notifications = this.notifications.filter(n => n.id !== id);
                        }, 5000);
                    });
            } else {
                // Retry after a short delay if Echo is not available yet
                setTimeout(initEcho, 100);
            }
        };

        // Start initialization
        initEcho();
    }
}" class="fixed top-4 right-4 z-50 space-y-2 w-72">
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="true" x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <!-- New Queue Icon -->
                    <template x-if="notification.status === 'new'">
                        <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <!-- In Progress Icon -->
                    <template x-if="notification.status === 'in_progress'">
                        <svg class="h-6 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <!-- Completed Icon -->
                    <template x-if="notification.status === 'completed'">
                        <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <!-- Cancelled Icon -->
                    <template x-if="notification.status === 'cancelled'">
                        <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="notification.message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="notifications = notifications.filter(n => n.id !== notification.id)"
                        class="rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
