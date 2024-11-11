<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Kidzklinika') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans">
    <div class="bg-blue-50 text-black/50">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-blue-500 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <h1 class="text-3xl font-bold text-blue-600">Kidzklinika</h1>
                    </div>
                    @if (Route::has('login'))
                        <livewire:navigation.guest />
                    @endif
                </header>

                <main class="mt-6">
                    <!-- Hero Section -->
                    <div class="mb-12 text-center">
                        <h2 class="text-4xl font-bold text-blue-800 mb-4">Caring for Your Children Like Our Own</h2>
                        <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                            Providing comprehensive pediatric care from newborns to adolescents in a warm, 
                            family-friendly environment.
                        </p>
                    </div>

                    <!-- Existing Services and Appointments Grid -->
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <a href="#services" class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-lg hover:shadow-xl transition">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100">
                                <svg class="size-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </div>
                            <div class="pt-3">
                                <h2 class="text-xl font-semibold text-black">Our Services</h2>
                                <p class="mt-4 text-sm/relaxed">
                                    Comprehensive pediatric care including well-child visits, vaccinations, 
                                    developmental screenings, and treatment for common childhood illnesses.
                                </p>
                            </div>
                        </a>

                        <a href="#appointments" class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-lg hover:shadow-xl transition">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100">
                                <svg class="size-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="pt-3">
                                <h2 class="text-xl font-semibold text-black">Book Appointment</h2>
                                <p class="mt-4 text-sm/relaxed">
                                    Schedule your child's next visit online. We offer flexible appointment times 
                                    including early morning and weekend slots.
                                </p>
                            </div>
                        </a>

                        <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-lg lg:col-span-2">
                            <div class="pt-3 w-full">
                                <h2 class="text-xl font-semibold text-black text-center">Contact Us</h2>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                                    <div>
                                        <h3 class="font-semibold">Address</h3>
                                        <p class="text-sm">123 Medical Center Drive<br>City, State 12345</p>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold">Phone</h3>
                                        <p class="text-sm">(555) 123-4567<br>Emergency: (555) 999-8888</p>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold">Hours</h3>
                                        <p class="text-sm">Mon-Fri: 8am - 6pm<br>Sat: 9am - 1pm</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Patient Portal -->
                    <section id="portal" class="mt-16">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-xl p-8 text-white">
                            <div class="md:flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold mb-2">Patient Portal</h2>
                                    <p class="text-blue-100 mb-4 md:mb-0">
                                        Access your child's medical records, schedule appointments, and message our team 24/7.
                                    </p>
                                </div>
                                <div class="space-x-4">
                                    <a href="/login" class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">
                                        Login
                                    </a>
                                    <a href="/register" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-400 transition">
                                        Register
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- FAQ Section -->
                    <section id="faq" class="mt-16">
                        <h2 class="text-2xl font-bold text-blue-800 text-center mb-8">Frequently Asked Questions</h2>
                        <div class="space-y-4">
                            <!-- FAQ Item 1 -->
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <button class="w-full text-left px-8 py-6 focus:outline-none" data-faq-button>
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-xl">When should I schedule my child's first visit?</h3>
                                        <svg class="w-6 h-6 text-blue-600 transition-transform duration-200" 
                                             data-faq-icon fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    <div class="mt-4 text-gray-600 text-base hidden" data-faq-answer>
                                        We recommend scheduling your child's first visit within the first week after birth. This initial visit is crucial for checking your newborn's health and establishing a relationship with your pediatrician.
                                    </div>
                                </button>
                            </div>

                            <!-- FAQ Item 2 -->
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <button class="w-full text-left px-8 py-6 focus:outline-none" data-faq-button>
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-xl">What should I bring to my child's appointment?</h3>
                                        <svg class="w-6 h-6 text-blue-600 transition-transform duration-200" 
                                             data-faq-icon fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    <div class="mt-4 text-gray-600 text-base hidden" data-faq-answer>
                                        Please bring:
                                        <ul class="list-disc ml-6 mt-3 space-y-2">
                                            <li>Insurance card</li>
                                            <li>Immunization records</li>
                                            <li>List of current medications</li>
                                            <li>Any relevant medical records</li>
                                            <li>List of questions or concerns</li>
                                        </ul>
                                    </div>
                                </button>
                            </div>

                            <!-- FAQ Item 3 -->
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <button class="w-full text-left px-8 py-6 focus:outline-none" data-faq-button>
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-xl">How do I handle after-hours emergencies?</h3>
                                        <svg class="w-6 h-6 text-blue-600 transition-transform duration-200" 
                                             data-faq-icon fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    <div class="mt-4 text-gray-600 text-base hidden" data-faq-answer>
                                        For emergencies after regular office hours:
                                        <ul class="list-disc ml-6 mt-3 space-y-2">
                                            <li>Call our 24/7 emergency line: (555) 999-8888</li>
                                            <li>Use our patient portal to send non-urgent messages</li>
                                            <li>Visit the nearest emergency room for severe cases</li>
                                            <li>On-call physicians are available for urgent consultations</li>
                                        </ul>
                                    </div>
                                </button>
                            </div>

                            <!-- FAQ Item 4 -->
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <button class="w-full text-left px-8 py-6 focus:outline-none" data-faq-button>
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-xl">What are your office hours and appointment policies?</h3>
                                        <svg class="w-6 h-6 text-blue-600 transition-transform duration-200" 
                                             data-faq-icon fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    <div class="mt-4 text-gray-600 text-base hidden" data-faq-answer>
                                        Our office hours and policies:
                                        <ul class="list-disc ml-6 mt-3 space-y-2">
                                            <li>Monday - Friday: 8:00 AM - 6:00 PM</li>
                                            <li>Saturday: 9:00 AM - 1:00 PM</li>
                                            <li>Same-day appointments available for urgent cases</li>
                                            <li>Please arrive 15 minutes early for your first visit</li>
                                            <li>24-hour notice required for appointment cancellations</li>
                                            <li>Telehealth consultations available for eligible visits</li>
                                        </ul>
                                    </div>
                                </button>
                            </div>

                            <!-- FAQ Item 5 -->
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <button class="w-full text-left px-8 py-6 focus:outline-none" data-faq-button>
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-xl">How can I access my child's medical records?</h3>
                                        <svg class="w-6 h-6 text-blue-600 transition-transform duration-200" 
                                             data-faq-icon fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    <div class="mt-4 text-gray-600 text-base hidden" data-faq-answer>
                                        You can access your child's medical records in several ways:
                                        <ul class="list-disc ml-6 mt-3 space-y-2">
                                            <li>Log in to our secure Patient Portal for 24/7 access to:
                                                <ul class="list-circle ml-6 mt-2 space-y-1">
                                                    <li>Visit summaries</li>
                                                    <li>Immunization records</li>
                                                    <li>Lab results</li>
                                                    <li>Growth charts</li>
                                                </ul>
                                            </li>
                                            <li>Request printed copies from our office (may take 3-5 business days)</li>
                                            <li>Records can be transferred to other providers upon written request</li>
                                            <li>Parents/legal guardians must provide ID for records access</li>
                                        </ul>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Map Section -->
                    <section id="location" class="mt-16">
                        <h2 class="text-2xl font-bold text-blue-800 text-center mb-8">Find Us</h2>
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <div id="map-container" class="relative">
                                <div class="aspect-w-16 aspect-h-9">
                                    <iframe 
                                        id="map-iframe"
                                        class="w-full h-[400px]"
                                        frameborder="0" 
                                        style="border:0" 
                                        loading="lazy"
                                        allowfullscreen
                                        referrerpolicy="no-referrer-when-downgrade"
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3935.726161055584!2d124.79931121478386!3d10.675897992389598!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a8f5c392c7f8ff%3A0x46a4f39f6d1d3b4b!2sKidzKlinika!5e0!3m2!1sen!2sph!4v1647827147693!5m2!1sen!2sph">
                                    </iframe>
                                </div>
                                <button 
                                    id="get-location-btn"
                                    class="absolute top-4 right-4 bg-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 hover:bg-gray-50 transition-colors"
                                    onclick="getLocation()"
                                >
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <span class="text-gray-700">Get Directions</span>
                                </button>
                            </div>
                            <!-- Location Details -->
                            <div class="p-6 bg-gray-50 border-t">
                                <div class="flex items-start gap-2 text-gray-600">
                                    <svg class="w-5 h-5 text-blue-600 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-blue-800">KidzKlinika</h3>
                                        <p class="mt-1">Zone 11, Baybay City, Leyte</p>
                                        <p id="distance-info" class="mt-2 text-sm text-blue-600 hidden">
                                            Loading distance...
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Emergency Banner -->
                    <div class="mt-16 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">24/7 Emergency Care</h3>
                                <p class="text-sm text-red-700 mt-1">
                                    For emergencies, call (555) 999-8888 or visit the nearest emergency room.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </main>

                <footer class="py-16 text-center text-sm text-black">
                    <p>© {{ date('Y') }} Kidzklinika. All rights reserved.</p>
                </footer>
            </div>
        </div>
    </div>
</body>

</html>
