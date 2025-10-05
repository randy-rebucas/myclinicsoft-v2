<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ \App\Helpers\ClinicSettings::name() }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); }
        }
        
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        .animate-slide-in-up { animation: slideInUp 0.6s ease-out forwards; }
        .animate-fade-in-scale { animation: fadeInScale 0.5s ease-out forwards; }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        .scroll-smooth { scroll-behavior: smooth; }
        
        .mobile-menu-hidden { transform: translateX(-100%); }
        .mobile-menu-visible { transform: translateX(0); }
        
        /* Typography Styles */
        * {
            font-family: 'Roboto', sans-serif;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .letter-spacing-wide {
            letter-spacing: 0.05em;
        }
        
        .line-height-relaxed {
            line-height: 1.7;
        }
    </style>
</head>

<body class="antialiased scroll-smooth">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold gradient-text">{{ \App\Helpers\ClinicSettings::name() }}</h1>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#home" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Home</a>
                        <a href="#services" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Services</a>
                        <a href="#appointments" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Appointments</a>
                        <a href="#portal" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Portal</a>
                        <a href="#faq" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">FAQ</a>
                        <a href="#contact" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Contact</a>
                    @if (Route::has('login'))
                        <livewire:navigation.guest />
                    @endif
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="md:hidden mobile-menu-hidden fixed inset-0 z-40 transition-transform duration-300 ease-in-out">
            <div class="fixed inset-0 bg-black bg-opacity-25" onclick="toggleMobileMenu()"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button onclick="toggleMobileMenu()" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <h1 class="text-xl font-bold gradient-text">{{ \App\Helpers\ClinicSettings::name() }}</h1>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <a href="#home" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md" onclick="toggleMobileMenu()">Home</a>
                        <a href="#services" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md" onclick="toggleMobileMenu()">Services</a>
                        <a href="#appointments" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md" onclick="toggleMobileMenu()">Appointments</a>
                        <a href="#portal" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md" onclick="toggleMobileMenu()">Portal</a>
                        <a href="#faq" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md" onclick="toggleMobileMenu()">FAQ</a>
                        <a href="#contact" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md" onclick="toggleMobileMenu()">Contact</a>
                    </nav>
                </div>
            </div>
        </div>
    </nav>

                    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background with gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23667eea" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-slide-in-up">
                <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 text-shadow">
                    {{ \App\Helpers\ClinicSettings::tagline() }}
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto line-height-relaxed letter-spacing-wide">
                    {{ \App\Helpers\ClinicSettings::description() }}
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <a href="#appointments" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Book Appointment
                    </a>
                    <a href="#services" class="border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-full text-lg font-semibold hover:bg-blue-600 hover:text-white transition-all duration-300">
                        <i class="fas fa-stethoscope mr-2"></i>
                        Our Services
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-center animate-fade-in-scale">
                        <div class="text-4xl font-bold text-blue-600 mb-2" data-counter="5000">0</div>
                        <div class="text-gray-600">Happy Families</div>
                    </div>
                    <div class="text-center animate-fade-in-scale" style="animation-delay: 0.2s">
                        <div class="text-4xl font-bold text-indigo-600 mb-2" data-counter="15">0</div>
                        <div class="text-gray-600">Years Experience</div>
                    </div>
                    <div class="text-center animate-fade-in-scale" style="animation-delay: 0.4s">
                        <div class="text-4xl font-bold text-purple-600 mb-2" data-counter="24">0</div>
                        <div class="text-gray-600">Hours Support</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 animate-float">
            <div class="w-20 h-20 bg-blue-200 rounded-full opacity-20"></div>
        </div>
        <div class="absolute bottom-20 right-10 animate-float" style="animation-delay: 2s">
            <div class="w-16 h-16 bg-indigo-200 rounded-full opacity-20"></div>
        </div>
        <div class="absolute top-1/2 left-5 animate-float" style="animation-delay: 4s">
            <div class="w-12 h-12 bg-purple-200 rounded-full opacity-20"></div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold  text-gray-900 mb-4 text-shadow">
                    Our <span class="text-gradient">Services</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto line-height-relaxed">
                    Comprehensive pediatric care designed to support your child's health and development at every stage.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service Card 1 -->
                <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-stethoscope text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold  text-gray-900 mb-4">Well-Child Visits</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Regular check-ups to monitor your child's growth, development, and overall health with personalized care plans.
                        </p>
                        <a href="#appointments" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                            Learn More <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service Card 2 -->
                <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-syringe text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold  text-gray-900 mb-4">Vaccinations</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Complete immunization schedules to protect your child from preventable diseases with the latest vaccines.
                        </p>
                        <a href="#appointments" class="inline-flex items-center text-green-600 font-semibold hover:text-green-700 transition-colors">
                            Learn More <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    </div>

                <!-- Service Card 3 -->
                <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-heartbeat text-white text-2xl"></i>
                            </div>
                        <h3 class="text-2xl font-bold  text-gray-900 mb-4">Emergency Care</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            24/7 emergency services with experienced pediatric specialists ready to handle urgent medical situations.
                        </p>
                        <a href="#contact" class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700 transition-colors">
                            Learn More <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service Card 4 -->
                <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-brain text-white text-2xl"></i>
                            </div>
                        <h3 class="text-2xl font-bold  text-gray-900 mb-4">Developmental Screening</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Early detection and intervention for developmental delays with comprehensive assessment tools.
                        </p>
                        <a href="#appointments" class="inline-flex items-center text-orange-600 font-semibold hover:text-orange-700 transition-colors">
                            Learn More <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service Card 5 -->
                <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="absolute inset-0 bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-laptop-medical text-white text-2xl"></i>
                            </div>
                        <h3 class="text-2xl font-bold  text-gray-900 mb-4">Telehealth</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Virtual consultations for non-emergency visits, providing convenient access to pediatric care from home.
                        </p>
                        <a href="#appointments" class="inline-flex items-center text-teal-600 font-semibold hover:text-teal-700 transition-colors">
                            Learn More <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service Card 6 -->
                <div class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                            </div>
                        <h3 class="text-2xl font-bold  text-gray-900 mb-4">Specialist Referrals</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Coordinated care with pediatric specialists for complex conditions requiring specialized treatment.
                        </p>
                        <a href="#contact" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                            Learn More <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Appointment Booking Section -->
    <section id="appointments" class="py-20 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold  text-gray-900 mb-4 text-shadow">
                    Book Your <span class="text-gradient">Appointment</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto line-height-relaxed">
                    Schedule your child's next visit with our easy-to-use online booking system.
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Easy Online Booking</h3>
                            <p class="text-gray-600">Schedule appointments 24/7 with our user-friendly online system.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Flexible Scheduling</h3>
                            <p class="text-gray-600">Choose from morning, afternoon, and weekend appointment slots.</p>
                                    </div>
                                </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-bell text-purple-600 text-xl"></i>
                            </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Reminder Notifications</h3>
                            <p class="text-gray-600">Get email and SMS reminders before your appointment.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-8 shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Quick Appointment</h3>
                    <div class="space-y-4">
                        <a href="/login" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 px-6 rounded-xl font-semibold text-center block hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login to Book
                        </a>
                        <a href="/register" class="w-full border-2 border-blue-600 text-blue-600 py-4 px-6 rounded-xl font-semibold text-center block hover:bg-blue-600 hover:text-white transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>
                            New Patient Registration
                        </a>
                    </div>
                    <div class="mt-6 text-center">
                        <p class="text-gray-600 mb-2">Or call us directly:</p>
                        <a href="tel:{{ \App\Helpers\ClinicSettings::phone() }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700">
                            <i class="fas fa-phone mr-2"></i>
                            {{ \App\Helpers\ClinicSettings::phone() }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Patient Portal Section -->
    <section id="portal" class="py-20 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold  text-white mb-4 text-shadow">
                    Patient <span class="text-yellow-300">Portal</span>
                </h2>
                <p class="text-xl text-blue-100 max-w-3xl mx-auto line-height-relaxed">
                                        Access your child's medical records, schedule appointments, and message our team 24/7.
                                    </p>
                                </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">Medical Records</h3>
                            <p class="text-blue-100">View test results, growth charts, and visit summaries instantly.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-comments text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">Secure Messaging</h3>
                            <p class="text-blue-100">Communicate directly with your child's healthcare team.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">Mobile Access</h3>
                            <p class="text-blue-100">Access your portal from any device, anywhere, anytime.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 border border-white border-opacity-20">
                    <h3 class="text-2xl font-bold text-white mb-6 text-center">Get Started</h3>
                    <div class="space-y-4">
                        <a href="/login" class="w-full bg-white text-blue-600 py-4 px-6 rounded-xl font-semibold text-center block hover:bg-gray-100 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login to Portal
                        </a>
                        <a href="/register" class="w-full border-2 border-white text-white py-4 px-6 rounded-xl font-semibold text-center block hover:bg-white hover:text-blue-600 transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create Account
                        </a>
                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold  text-gray-900 mb-4 text-shadow">
                    Frequently Asked <span class="text-gradient">Questions</span>
                </h2>
                <p class="text-xl text-gray-600 line-height-relaxed">
                    Find answers to common questions about our pediatric services.
                </p>
            </div>
            
                        <div class="space-y-4">
                            <!-- FAQ Item 1 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <button class="w-full text-left px-8 py-6 focus:outline-none group" data-faq-button>
                                    <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-xl text-gray-900 group-hover:text-blue-600 transition-colors">
                                When should I schedule my child's first visit?
                            </h3>
                            <div class="flex-shrink-0 ml-4">
                                <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300" data-faq-icon></i>
                            </div>
                                    </div>
                        <div class="mt-4 text-gray-600 text-base hidden overflow-hidden" data-faq-answer>
                            <div class="pb-4">
                                        We recommend scheduling your child's first visit within the first week after birth. This initial visit is crucial for checking your newborn's health and establishing a relationship with your pediatrician.
                            </div>
                                    </div>
                                </button>
                            </div>

                            <!-- FAQ Item 2 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <button class="w-full text-left px-8 py-6 focus:outline-none group" data-faq-button>
                                    <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-xl text-gray-900 group-hover:text-blue-600 transition-colors">
                                What should I bring to my child's appointment?
                            </h3>
                            <div class="flex-shrink-0 ml-4">
                                <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300" data-faq-icon></i>
                            </div>
                                    </div>
                        <div class="mt-4 text-gray-600 text-base hidden overflow-hidden" data-faq-answer>
                            <div class="pb-4">
                                        Please bring:
                                        <ul class="list-disc ml-6 mt-3 space-y-2">
                                            <li>Insurance card</li>
                                            <li>Immunization records</li>
                                            <li>List of current medications</li>
                                            <li>Any relevant medical records</li>
                                            <li>List of questions or concerns</li>
                                        </ul>
                            </div>
                                    </div>
                                </button>
                            </div>

                            <!-- FAQ Item 3 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <button class="w-full text-left px-8 py-6 focus:outline-none group" data-faq-button>
                                    <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-xl text-gray-900 group-hover:text-blue-600 transition-colors">
                                How do I handle after-hours emergencies?
                            </h3>
                            <div class="flex-shrink-0 ml-4">
                                <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300" data-faq-icon></i>
                            </div>
                                    </div>
                        <div class="mt-4 text-gray-600 text-base hidden overflow-hidden" data-faq-answer>
                            <div class="pb-4">
                                        For emergencies after regular office hours:
                                        <ul class="list-disc ml-6 mt-3 space-y-2">
                                            <li>Call our 24/7 emergency line: {{ \App\Helpers\ClinicSettings::emergencyPhone() }}</li>
                                            <li>Use our patient portal to send non-urgent messages</li>
                                            <li>Visit the nearest emergency room for severe cases</li>
                                            <li>On-call physicians are available for urgent consultations</li>
                                        </ul>
                            </div>
                                    </div>
                                </button>
                            </div>

                            <!-- FAQ Item 4 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <button class="w-full text-left px-8 py-6 focus:outline-none group" data-faq-button>
                                    <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-xl text-gray-900 group-hover:text-blue-600 transition-colors">
                                What are your office hours and appointment policies?
                            </h3>
                            <div class="flex-shrink-0 ml-4">
                                <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300" data-faq-icon></i>
                            </div>
                                    </div>
                        <div class="mt-4 text-gray-600 text-base hidden overflow-hidden" data-faq-answer>
                            <div class="pb-4">
                                        Our office hours and policies:
                                        <ul class="list-disc ml-6 mt-3 space-y-2">
                                            <li>{{ \App\Helpers\ClinicSettings::hours()['weekdays'] }}</li>
                                            <li>{{ \App\Helpers\ClinicSettings::hours()['saturday'] }}</li>
                                            <li>Same-day appointments available for urgent cases</li>
                                            <li>Please arrive 15 minutes early for your first visit</li>
                                            <li>24-hour notice required for appointment cancellations</li>
                                            <li>Telehealth consultations available for eligible visits</li>
                                        </ul>
                            </div>
                                    </div>
                                </button>
                            </div>

                            <!-- FAQ Item 5 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <button class="w-full text-left px-8 py-6 focus:outline-none group" data-faq-button>
                                    <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-xl text-gray-900 group-hover:text-blue-600 transition-colors">
                                How can I access my child's medical records?
                            </h3>
                            <div class="flex-shrink-0 ml-4">
                                <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300" data-faq-icon></i>
                            </div>
                                    </div>
                        <div class="mt-4 text-gray-600 text-base hidden overflow-hidden" data-faq-answer>
                            <div class="pb-4">
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
                                    </div>
                                </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact & Location Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold  text-gray-900 mb-4 text-shadow">
                    Find <span class="text-gradient">Us</span>
                </h2>
                <p class="text-xl text-gray-600 line-height-relaxed">
                    Visit our clinic or get in touch with us for any questions.
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Information -->
                <div class="space-y-8">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Address</h4>
                                    <p class="text-gray-600">{!! \App\Helpers\ClinicSettings::fullAddress() !!}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-phone text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Phone</h4>
                                    <p class="text-gray-600">
                                        <a href="tel:{{ \App\Helpers\ClinicSettings::phone() }}" class="hover:text-blue-600 transition-colors">{{ \App\Helpers\ClinicSettings::phone() }}</a><br>
                                        Emergency: <a href="tel:{{ \App\Helpers\ClinicSettings::emergencyPhone() }}" class="text-red-600 hover:text-red-700 transition-colors">{{ \App\Helpers\ClinicSettings::emergencyPhone() }}</a>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Office Hours</h4>
                                    <p class="text-gray-600">
                                        {!! \App\Helpers\ClinicSettings::hours()['formatted'] !!}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-envelope text-orange-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Email</h4>
                                    <p class="text-gray-600">
                                        <a href="mailto:{{ \App\Helpers\ClinicSettings::email() }}" class="hover:text-blue-600 transition-colors">{{ \App\Helpers\ClinicSettings::email() }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Map -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                            <div id="map-container" class="relative">
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
                                <button 
                                    id="get-location-btn"
                                    class="absolute top-4 right-4 bg-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 hover:bg-gray-50 transition-colors"
                                    onclick="getLocation()"
                                >
                            <i class="fas fa-directions text-blue-600"></i>
                                    <span class="text-gray-700">Get Directions</span>
                                </button>
                            </div>
                            <div class="p-6 bg-gray-50 border-t">
                        <div class="flex items-start gap-3 text-gray-600">
                            <i class="fas fa-map-marker-alt text-blue-600 mt-1"></i>
                                    <div>
                                <h3 class="font-semibold text-blue-800">{{ \App\Helpers\ClinicSettings::name() }}</h3>
                                <p class="mt-1">{{ \App\Helpers\ClinicSettings::address() }}</p>
                                        <p id="distance-info" class="mt-2 text-sm text-blue-600 hidden">
                                            Loading distance...
                                        </p>
                            </div>
                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Emergency Banner -->
    <section class="py-8 bg-gradient-to-r from-red-500 to-red-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-4 text-white">
                            <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                    <div>
                        <h3 class="text-lg font-semibold">24/7 Emergency Care Available</h3>
                        <p class="text-red-100">
                            For emergencies, call <a href="tel:{{ \App\Helpers\ClinicSettings::emergencyPhone() }}" class="font-bold hover:text-white transition-colors">{{ \App\Helpers\ClinicSettings::emergencyPhone() }}</a> or visit the nearest emergency room.
                                </p>
                            </div>
                        </div>
                    </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-2">
                    <h3 class="text-2xl font-bold  gradient-text mb-4">{{ \App\Helpers\ClinicSettings::name() }}</h3>
                    <p class="text-gray-300 mb-6 max-w-md">
                        {{ \App\Helpers\ClinicSettings::description() }}
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center hover:bg-blue-500 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center hover:bg-pink-700 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-800 rounded-full flex items-center justify-center hover:bg-blue-900 transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#services" class="text-gray-300 hover:text-white transition-colors">Services</a></li>
                        <li><a href="#appointments" class="text-gray-300 hover:text-white transition-colors">Appointments</a></li>
                        <li><a href="#portal" class="text-gray-300 hover:text-white transition-colors">Patient Portal</a></li>
                        <li><a href="#faq" class="text-gray-300 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#contact" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-400"></i>
                            {{ \App\Helpers\ClinicSettings::address() }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-blue-400"></i>
                            {{ \App\Helpers\ClinicSettings::phone() }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-blue-400"></i>
                            {{ \App\Helpers\ClinicSettings::email() }}
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Newsletter Signup -->
            <div class="mt-12 pt-8 border-t border-gray-800">
                <div class="max-w-md mx-auto text-center">
                    <h4 class="text-lg font-semibold mb-4">Stay Updated</h4>
                    <p class="text-gray-300 mb-4">Subscribe to our newsletter for health tips and updates.</p>
                    <div class="flex">
                        <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-2 rounded-l-lg bg-gray-800 border border-gray-700 text-white focus:outline-none focus:border-blue-500">
                        <button class="px-6 py-2 bg-blue-600 rounded-r-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Kidzklinika. All rights reserved. | Privacy Policy | Terms of Service</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Interactive Features -->
    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('mobile-menu-hidden');
            mobileMenu.classList.toggle('mobile-menu-visible');
        }

        // Add click event listener to mobile menu button
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', toggleMobileMenu);
            }
            
            // Active navigation highlighting
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            
            function highlightNavigation() {
                let current = '';
                const headerHeight = 100;
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - headerHeight;
                    const sectionHeight = section.offsetHeight;
                    if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                        current = section.getAttribute('id');
                    }
                });
                
                navLinks.forEach(link => {
                    link.classList.remove('text-blue-600', 'font-semibold');
                    link.classList.add('text-gray-700');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.remove('text-gray-700');
                        link.classList.add('text-blue-600', 'font-semibold');
                    }
                });
            }
            
            window.addEventListener('scroll', highlightNavigation);
            highlightNavigation(); // Call once on load
        });

        // FAQ Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const faqButtons = document.querySelectorAll('[data-faq-button]');
            
            faqButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const answer = this.querySelector('[data-faq-answer]');
                    const icon = this.querySelector('[data-faq-icon]');
                    
                    // Close other open FAQs
                    faqButtons.forEach(otherButton => {
                        if (otherButton !== this) {
                            const otherAnswer = otherButton.querySelector('[data-faq-answer]');
                            const otherIcon = otherButton.querySelector('[data-faq-icon]');
                            otherAnswer.classList.add('hidden');
                            otherIcon.style.transform = 'rotate(0deg)';
                        }
                    });
                    
                    // Toggle current FAQ
                    answer.classList.toggle('hidden');
                    icon.style.transform = answer.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            });
        });

        // Animated Counter
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            function updateCounter() {
                start += increment;
                if (start < target) {
                    element.textContent = Math.floor(start);
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target;
                }
            }
            
            updateCounter();
        }

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('[data-counter]');
                    counters.forEach(counter => {
                        const target = parseInt(counter.getAttribute('data-counter'));
                        animateCounter(counter, target);
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe stats section
        const statsSection = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3.gap-8');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // Smooth scrolling for navigation links with offset for fixed header
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                if (target) {
                    const headerHeight = 80; // Approximate height of fixed header
                    const targetPosition = target.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Get user location for distance calculation
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            const distanceInfo = document.getElementById('distance-info');
            if (distanceInfo) {
                distanceInfo.classList.remove('hidden');
                distanceInfo.textContent = 'Location detected! Click for directions.';
            }
        }
    </script>
</body>

</html>
